<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Window extends Model
{
    protected $primaryKey = 'window_id';

    protected $fillable = [
        'window_name',
        'queue_prefix',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function staffUsers(): HasMany
    {
        return $this->hasMany(StaffUser::class, 'window_id', 'window_id');
    }

    public function queueRecords(): HasMany
    {
        return $this->hasMany(QueueRecord::class, 'window_id', 'window_id');
    }
}
