<?php

namespace App\Services;

use App\Models\Order;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeCheckoutService
{
    public function client(): StripeClient
    {
        $secret = config('services.stripe.secret');
        if (! $secret) {
            throw new RuntimeException('STRIPE_SECRET is not configured.');
        }

        return new StripeClient($secret);
    }

    public function createCheckoutSession(Order $order): Session
    {
        $order->load(['event', 'items.section']);
        $currency = strtolower($order->currency);

        $lineItems = [];
        foreach ($order->items as $line) {
            $section = $line->section;
            $unitAmount = $this->toStripeMinorUnits((float) $line->unit_price, $currency);
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $order->event->name.' — '.$section->name,
                        'description' => 'Ticket — '.$section->name,
                    ],
                    'unit_amount' => $unitAmount,
                ],
                'quantity' => $line->quantity,
            ];
        }

        $successUrl = route('purchase.return', [$order], absolute: true).'?session_id={CHECKOUT_SESSION_ID}';

        return $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'client_reference_id' => $order->uuid,
            'customer_email' => $order->email,
            'line_items' => $lineItems,
            'metadata' => [
                'order_uuid' => (string) $order->uuid,
            ],
            'success_url' => $successUrl,
            'cancel_url' => route('events.show', $order->event, absolute: true),
        ]);
    }

    /**
     * Stripe amounts are in the smallest currency unit (e.g. cents), except zero-decimal currencies.
     */
    public function toStripeMinorUnits(float $amount, string $currency): int
    {
        $currency = strtolower($currency);
        $zeroDecimal = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];

        if (in_array($currency, $zeroDecimal, true)) {
            return (int) round($amount);
        }

        return (int) round($amount * 100);
    }
}
