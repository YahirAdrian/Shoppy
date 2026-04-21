<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSession extends Model
{
    protected $fillable = [
        'seller_id',
        'opening_cash',
        'current_cash',
        'status',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_cash' => 'decimal:2',
            'current_cash' => 'decimal:2',
            'started_at'   => 'datetime',
            'finished_at'  => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'pos_session_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canEnd(): bool
    {
        return (float) $this->current_cash === 0.0;
    }
}
