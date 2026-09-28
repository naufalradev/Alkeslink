<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    protected $fillable = ['item_review_id', 'image_url', 'sort_order'];

    public function review()
    {
        return $this->belongsTo(ItemReview::class, 'item_review_id');
    }
}
