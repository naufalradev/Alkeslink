<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'phone', 'avatar_url', 'province', 'city', 'district',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* ── Relationships ── */

    public function items()
    {
        return $this->hasMany(Item::class, 'donor_id');
    }

    public function requests()
    {
        return $this->hasMany(DonationRequest::class, 'recipient_id');
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    /* ── Helpers ── */

    public function isDonor(): bool    { return $this->role === 'donor'; }
    public function isRecipient(): bool{ return $this->role === 'recipient'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
}
