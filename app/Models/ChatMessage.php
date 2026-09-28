<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    public $timestamps = false;

    protected $fillable = ['session_id', 'role', 'content'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function session()
    {
        return $this->belongsTo(ChatSession::class);
    }
}
