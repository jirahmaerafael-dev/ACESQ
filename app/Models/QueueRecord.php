<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueueRecord extends Model
{
    protected $primaryKey = 'queue_id';

    protected $fillable = [
        'window_id',
        'student_name',
        'ticket_prefix',
        'student_session_hash',
        'queue_date',
        'queue_number',
        'queue_position',
        'status',
        'missed_count',
        'terms_agreed',
        'public_token',
        'called_at',
    ];

    protected function casts(): array
    {
        return [
            'queue_id' => 'integer',
            'window_id' => 'integer',
            'queue_date' => 'date',
            'queue_number' => 'integer',
            'queue_position' => 'integer',
            'missed_count' => 'integer',
            'terms_agreed' => 'boolean',
            'called_at' => 'datetime',
        ];
    }

    public function window(): BelongsTo
    {
        return $this->belongsTo(Window::class, 'window_id', 'window_id');
    }

    public function missedCalls(): HasMany
    {
        return $this->hasMany(MissedCall::class, 'queue_id', 'queue_id')
            ->orderBy('call_number');
    }

    public function displayNumber(): string
    {
        $prefix = $this->ticket_prefix ?? $this->window->queue_prefix;

        return $prefix.str_pad((string) $this->queue_number, 3, '0', STR_PAD_LEFT);
    }
}
