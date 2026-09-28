<?php

namespace App\Http\Controllers;

use App\Models\DonationRequest;
use App\Models\DirectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectMessageController extends Controller
{
    /** GET /chat/{donationRequest} */
    public function show(DonationRequest $donationRequest)
    {
        $this->authorizeAccess($donationRequest);

        $donationRequest->load(['messages.sender', 'item', 'item.primaryImage', 'item.donor', 'recipient']);

        // Mark incoming messages as read
        $donationRequest->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('pages.chat.show', compact('donationRequest'));
    }

    /** POST /chat/{donationRequest} */
    public function store(Request $request, DonationRequest $donationRequest)
    {
        $this->authorizeAccess($donationRequest);

        $request->validate([
            'body'  => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Must have body or image
        if (!$request->filled('body') && !$request->hasFile('image')) {
            return back()->withErrors(['body' => 'Tulis pesan atau lampirkan gambar.']);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store("chat/{$donationRequest->id}", 'public');
            $imageUrl = Storage::url($path);
        }

        DirectMessage::create([
            'donation_request_id' => $donationRequest->id,
            'sender_id'           => auth()->id(),
            'body'                => $request->body ?? '',
            'image_url'           => $imageUrl,
        ]);

        return back();
    }

    private function authorizeAccess(DonationRequest $donationRequest): void
    {
        $userId  = auth()->id();
        $allowed = [$donationRequest->recipient_id, $donationRequest->item->donor_id];
        abort_if(!in_array($userId, $allowed), 403);
        abort_if(!in_array($donationRequest->status, ['approved', 'handover', 'completed']), 403,
            'Chat hanya tersedia setelah pengajuan disetujui.');
    }
}
