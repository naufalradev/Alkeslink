<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id', 'category_id', 'title', 'description',
        'condition', 'quantity', 'province', 'city', 'district',
        'status', 'verified_by', 'verified_at', 'estimated_value', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'verified_at'     => 'datetime',
            'estimated_value' => 'decimal:2',
        ];
    }

    /* ── Relationships ── */

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class)->where('is_primary', true);
    }

    public function donationRequests()
    {
        return $this->hasMany(DonationRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(ItemReview::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /* ── Scopes ── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory(Builder $query, $categorySlug): Builder
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
    }

    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /* ── Helpers ── */

    public function conditionLabel(): string
    {
        return match ($this->condition) {
            'baik'         => 'Kondisi Baik',
            'cukup'        => 'Kondisi Cukup',
            'perlu_servis' => 'Perlu Servis',
            default        => $this->condition,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'Menunggu Verifikasi',
            'active'    => 'Tersedia',
            'matched'   => 'Sedang Diproses',
            'delivered' => 'Sudah Disalurkan',
            'closed'    => 'Ditutup',
            default     => $this->status,
        };
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function avgRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
