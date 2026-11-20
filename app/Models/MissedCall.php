<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissedCall extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'missed_id';

    protected $fillable = [
        'queue_id',
        'call_number',
        'slots_pushed',
        'missed_at',
    ];

    protected function casts(): array
    {
        return [
            'call_number' => 'integer',
            'slots_pushed' => 'integer',
            'missed_at' => 'datetime',
        ];
    }

    public function queueRecord(): BelongsTo
    {
        return $this->belongsTo(QueueRecord::class, 'queue_id', 'queue_id');
    }
}
