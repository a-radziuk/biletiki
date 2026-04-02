<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $event->load('sections');

        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*' => ['integer', 'min:0', 'max:10'],
        ]);

        $lines = [];
        $totalQty = 0;

        foreach ($event->sections as $section) {
            $qty = (int) ($validated['sections'][$section->id] ?? 0);
            if ($qty < 1) {
                continue;
            }
            if ($qty > $section->available()) {
                return back()
                    ->withInput()
                    ->withErrors(['sections' => "Not enough tickets left in {$section->name}."]);
            }
            $lines[] = [
                'section_id' => $section->id,
                'quantity' => $qty,
            ];
            $totalQty += $qty;
        }

        if ($totalQty < 1) {
            return back()
                ->withInput()
                ->withErrors(['sections' => 'Select at least one ticket.']);
        }

        if ($totalQty > 10) {
            return back()
                ->withInput()
                ->withErrors(['sections' => 'You can buy at most 10 tickets in one order.']);
        }

        $request->session()->put('checkout', [
            'event_id' => $event->id,
            'lines' => $lines,
        ]);

        return redirect()->route('purchase.show');
    }
}
