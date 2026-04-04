<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'name',
        'due_date',
        'is_completed',
        'completed_at',
        'repeat_type',
        'repeat_interval',
        'next_due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'repeat_interval' => 'integer',
            'next_due_date' => 'date',
        ];
    }

    public function isRecurring(): bool
    {
        return $this->repeat_type !== 'none';
    }

    public function isOverdue(): bool
    {
        return !$this->is_completed && $this->due_date && $this->due_date->lt(today());
    }

    public function calculateNextDueDate(): ?Carbon
    {
        $base = $this->due_date ?? today();
        $interval = $this->repeat_interval ?? 1;

        return match ($this->repeat_type) {
            'daily' => $base->copy()->addDays($interval),
            'weekly' => $base->copy()->addWeeks($interval),
            'monthly' => $base->copy()->addMonths($interval),
            default => null,
        };
    }
}
