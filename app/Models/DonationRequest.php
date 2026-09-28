<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'recipient_id', 'reason', 'support_doc_url',
        'status', 'donor_note', 'approved_at', 'handover_at', 'completed_at',
        'donor_confirmed', 'recipient_confirmed', 'admin_confirmed',
        'donor_confirmed_at', 'recipient_confirmed_at', 'admin_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at'           => 'datetime',
            'handover_at'           => 'datetime',
            'completed_at'          => 'datetime',
            'donor_confirmed'       => 'boolean',
            'recipient_confirmed'   => 'boolean',
            'admin_confirmed'       => 'boolean',
            'donor_confirmed_at'    => 'datetime',
            'recipient_confirmed_at'=> 'datetime',
            'admin_confirmed_at'    => 'datetime',
        ];
    }

    /* ── Relationships ── */

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function messages()
    {
        return $this->hasMany(DirectMessage::class)->orderBy('created_at');
    }

    public function review()
    {
        return $this->hasOne(ItemReview::class);
    }

    /* ── Helpers ── */

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu Konfirmasi',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'handover'  => 'Dalam Serah Terima',
            'completed' => 'Selesai',
            default     => $this->status,
        };
    }

    /** Apakah semua pihak sudah konfirmasi serah terima */
    public function allConfirmed(): bool
    {
        return $this->donor_confirmed && $this->recipient_confirmed && $this->admin_confirmed;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'approved'  => 'blue',
            'rejected'  => 'red',
            'handover'  => 'purple',
            'completed' => 'green',
            default     => 'gray',
        };
    }

    /** Ordered list of timeline steps for the status tracker UI. */
    public function timelineSteps(): array
    {
        return [
            ['key' => 'pending',   'label' => 'Pengajuan Dikirim',  'done' => true],
            ['key' => 'approved',  'label' => 'Disetujui Donatur',  'done' => in_array($this->status, ['approved','handover','completed'])],
            ['key' => 'handover',  'label' => 'Konfirmasi Serah Terima', 'done' => in_array($this->status, ['handover','completed'])],
            ['key' => 'completed', 'label' => 'Selesai',            'done' => $this->status === 'completed'],
        ];
    }
}
