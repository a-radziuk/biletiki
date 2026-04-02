<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Section;
use App\Services\OrderFulfillmentService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function show(Request $request)
    {
        $checkout = $request->session()->get('checkout');
        if (! $checkout) {
            return redirect('/')->with('error', 'Your cart is empty.');
        }

        $event = Event::query()->with('sections')->findOrFail($checkout['event_id']);
        $lines = $this->resolveLines($event, $checkout['lines']);
        $total = $lines->sum(fn ($l) => $l['section']->price * $l['quantity']);

        return view('purchase.show', [
            'event' => $event,
            'lines' => $lines,
            'total' => $total,
        ]);
    }

    public function store(Request $request, StripeCheckoutService $stripe)
    {
        $checkout = $request->session()->get('checkout');
        if (! $checkout) {
            return redirect('/')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $event = Event::query()->with('sections')->findOrFail($checkout['event_id']);
        $lines = $this->resolveLines($event, $checkout['lines']);

        if ($lines->isEmpty()) {
            return redirect()->route('events.show', $event)->with('error', 'Your cart is empty.');
        }

        $total = $lines->sum(fn ($l) => $l['section']->price * $l['quantity']);

        $order = null;

        try {
            $order = DB::transaction(function () use ($event, $lines, $validated, $total) {
                foreach ($lines as $line) {
                    $section = Section::query()->whereKey($line['section']->id)->lockForUpdate()->firstOrFail();
                    if ($section->available() < $line['quantity']) {
                        throw new \RuntimeException("Not enough tickets left in {$section->name}.");
                    }
                    $section->held += $line['quantity'];
                    $section->save();
                }

                $order = Order::query()->create([
                    'event_id' => $event->id,
                    'status' => Order::STATUS_PENDING_PAYMENT,
                    'customer_name' => $validated['customer_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'total' => $total,
                    'currency' => config('services.stripe.currency', 'usd'),
                ]);

                foreach ($lines as $line) {
                    $order->items()->create([
                        'section_id' => $line['section']->id,
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['section']->price,
                    ]);
                }

                $order->load(['event', 'items.section']);

                return $order;
            });

            $session = $stripe->createCheckoutSession($order);
            $order->stripe_checkout_session_id = $session->id;
            $order->save();

            $request->session()->forget('checkout');

            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            if ($order !== null) {
                $this->cancelPendingOrder($order);
            }
            report($e);

            return back()->withInput()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    public function thankYou(Order $order)
    {
        $order->load(['event', 'tickets.section']);

        return view('purchase.thank-you', compact('order'));
    }

    public function returnFromPayment(Request $request, Order $order, OrderFulfillmentService $fulfillment, StripeCheckoutService $stripe)
    {
        $sessionId = $request->query('session_id');
        if (
            $sessionId
            && $order->stripe_checkout_session_id
            && hash_equals($order->stripe_checkout_session_id, $sessionId)
        ) {
            $session = $stripe->client()->checkout->sessions->retrieve($sessionId);
            if (($session->payment_status ?? '') === 'paid') {
                $fulfillment->fulfillOrder($order);
            }
        }

        return redirect()->route('purchase.thank-you', $order);
    }

    protected function cancelPendingOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()->whereKey($order->id)->with('items')->lockForUpdate()->first();
            if (! $order || $order->status !== Order::STATUS_PENDING_PAYMENT) {
                return;
            }

            foreach ($order->items as $item) {
                $section = Section::query()->whereKey($item->section_id)->lockForUpdate()->firstOrFail();
                $section->held -= $item->quantity;
                $section->save();
            }

            $order->items()->delete();
            $order->delete();
        });
    }

    /**
     * @param  array<int, array{section_id: int, quantity: int}>  $lineData
     */
    protected function resolveLines(Event $event, array $lineData): Collection
    {
        $byId = $event->sections->keyBy('id');
        $out = collect();

        foreach ($lineData as $row) {
            $section = $byId->get($row['section_id']);
            if (! $section || $row['quantity'] < 1) {
                continue;
            }
            $out->push([
                'section' => $section,
                'quantity' => (int) $row['quantity'],
            ]);
        }

        return $out;
    }
}
