<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StaffUser extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'window_id',
        'full_name',
        'username',
        'password_hash',
        'role',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'window_id' => 'integer',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function window(): BelongsTo
    {
        return $this->belongsTo(Window::class, 'window_id', 'window_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
