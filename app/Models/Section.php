<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'sort_order',
        'price',
        'capacity',
        'sold',
        'held',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function available(): int
    {
        return max(0, $this->capacity - $this->sold - $this->held);
    }
}
