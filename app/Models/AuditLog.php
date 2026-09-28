<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'entity_type', 'entity_id', 'actor_id',
        'action', 'old_value', 'new_value', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_value'  => 'array',
            'new_value'  => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Convenience factory method.
     */
    public static function record(
        string $entityType,
        int    $entityId,
        int    $actorId,
        string $action,
        mixed  $oldValue = null,
        mixed  $newValue = null,
        ?string $notes   = null,
    ): self {
        return self::create([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'actor_id'    => $actorId,
            'action'      => $action,
            'old_value'   => $oldValue,
            'new_value'   => $newValue,
            'notes'       => $notes,
            'created_at'  => now(),
        ]);
    }
}
