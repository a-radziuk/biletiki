<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $event = Event::query()->orderBy('id')->first();
    if ($event) {
        return redirect()->route('events.show', $event);
    }

    return view('welcome');
});

Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/purchase', [PurchaseController::class, 'show'])->name('purchase.show');
Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/purchase/{order}/thank-you', [PurchaseController::class, 'thankYou'])->name('purchase.thank-you');
Route::get('/purchase/{order}/return', [PurchaseController::class, 'returnFromPayment'])->name('purchase.return');

Route::post('/webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');
