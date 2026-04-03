<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'uuid',
        'event_id',
        'status',
        'customer_name',
        'email',
        'phone',
        'stripe_checkout_session_id',
        'total',
        'currency',
        'paid_at',
        'tickets_emailed_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'tickets_emailed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->uuid) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->orderBy('id');
    }
}
