<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, OrderFulfillmentService $fulfillment)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            Log::warning('Stripe webhook: STRIPE_WEBHOOK_SECRET not set');

            return response('Webhook not configured', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook: invalid signature', ['message' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $uuid = $session->metadata['order_uuid'] ?? null;

            if (! $uuid) {
                return response('OK', 200);
            }

            $order = Order::query()->where('uuid', $uuid)->first();
            if (! $order) {
                Log::warning('Stripe webhook: order not found', ['order_uuid' => $uuid]);

                return response('OK', 200);
            }

            if (($session->payment_status ?? '') !== 'paid') {
                return response('OK', 200);
            }

            try {
                $fulfillment->fulfillOrder($order);
            } catch (Throwable $e) {
                Log::error('Stripe webhook fulfillment failed', [
                    'order_uuid' => $uuid,
                    'message' => $e->getMessage(),
                ]);

                return response('Error', 500);
            }
        }

        return response('OK', 200);
    }
}
