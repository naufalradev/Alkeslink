<?php

namespace App\Http\Controllers;

use App\Models\DonationRequest;
use App\Models\ItemReview;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemReviewController extends Controller
{
    /** POST /reviews */
    public function store(Request $request)
    {
        $request->validate([
            'donation_request_id' => 'required|exists:donation_requests,id',
            'rating'              => 'required|integer|min:1|max:5',
            'comment'             => 'nullable|string|max:1000',
            'images'              => 'nullable|array|max:3',
            'images.*'            => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $donationRequest = DonationRequest::with('item')->findOrFail($request->donation_request_id);

        abort_if($donationRequest->recipient_id !== auth()->id(), 403);
        abort_if($donationRequest->status !== 'completed', 422, 'Ulasan hanya bisa diberikan setelah serah terima selesai.');
        abort_if($donationRequest->review()->exists(), 422, 'Anda sudah memberikan ulasan untuk pengajuan ini.');

        $review = ItemReview::create([
            'item_id'             => $donationRequest->item_id,
            'donation_request_id' => $donationRequest->id,
            'reviewer_id'         => auth()->id(),
            'rating'              => $request->rating,
            'comment'             => $request->comment,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store("reviews/{$review->id}", 'public');
                ReviewImage::create([
                    'item_review_id' => $review->id,
                    'image_url'      => Storage::url($path),
                    'sort_order'     => $index,
                ]);
            }
        }

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }
}
