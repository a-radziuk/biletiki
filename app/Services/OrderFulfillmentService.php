<?php

namespace App\Services;

use App\Mail\TicketsPurchasedMail;
use App\Models\Order;
use App\Models\Section;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderFulfillmentService
{
    /**
     * Issues tickets and sends email once after payment is confirmed (e.g. Stripe webhook or return URL).
     */
    public function fulfillOrder(Order $order): bool
    {
        if ($order->status === Order::STATUS_COMPLETED) {
            return true;
        }

        $order->load(['event', 'items.section']);

        $issued = false;

        DB::transaction(function () use ($order, &$issued) {
            $order = Order::query()->whereKey($order->id)->with('items')->lockForUpdate()->firstOrFail();

            if ($order->status === Order::STATUS_COMPLETED) {
                return;
            }

            foreach ($order->items as $item) {
                $section = Section::query()->whereKey($item->section_id)->lockForUpdate()->firstOrFail();
                if ($section->held < $item->quantity) {
                    throw new \RuntimeException('Invalid held count for section '.$section->id);
                }
                $section->held -= $item->quantity;
                $section->sold += $item->quantity;
                $section->save();
            }

            foreach ($order->items as $item) {
                for ($n = 0; $n < $item->quantity; $n++) {
                    Ticket::query()->create([
                        'order_id' => $order->id,
                        'section_id' => $item->section_id,
                        'public_code' => Str::lower(Str::random(32)),
                    ]);
                }
            }

            $order->status = Order::STATUS_COMPLETED;
            $order->paid_at = now();
            $order->save();

            $issued = true;
        });

        if (! $issued) {
            return true;
        }

        $order->refresh()->load(['tickets.section', 'event']);

        Mail::to($order->email)->send(new TicketsPurchasedMail($order));

        $order->tickets_emailed_at = now();
        $order->save();

        return true;
    }
}
