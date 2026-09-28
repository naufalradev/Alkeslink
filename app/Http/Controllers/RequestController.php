<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\DonationRequest;
use App\Models\AuditLog;
use App\Http\Requests\StoreRequestForm;
use App\Services\ImpactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    public function __construct(private ImpactService $impactService) {}

    /** POST /requests — Recipient submits a request */
    public function store(StoreRequestForm $request)
    {
        $item = Item::findOrFail($request->item_id);
        abort_if($item->status !== 'active', 422, 'Barang ini tidak tersedia.');

        $docUrl = null;
        if ($request->hasFile('support_doc')) {
            $path   = $request->file('support_doc')->store('docs', 'public');
            $docUrl = Storage::url($path);
        }

        $donationRequest = DonationRequest::create([
            'item_id'         => $item->id,
            'recipient_id'    => auth()->id(),
            'reason'          => $request->reason,
            'support_doc_url' => $docUrl,
            'status'          => 'pending',
        ]);

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(), 'created',
            null, ['status' => 'pending']);

        return back()->with('success', 'Permohonan berhasil dikirim! Donatur akan segera mengkonfirmasi.');
    }

    /** GET /recipient/requests — Recipient's request history */
    public function myRequests()
    {
        $requests = DonationRequest::where('recipient_id', auth()->id())
            ->with(['item.primaryImage', 'item.category', 'review'])
            ->latest()
            ->paginate(10);

        return view('pages.recipient.my-requests', compact('requests'));
    }

    /** GET /donor/requests — All requests for the donor's items */
    public function donorRequests()
    {
        $requests = DonationRequest::whereHas('item', fn($q) => $q->where('donor_id', auth()->id()))
            ->with(['item.primaryImage', 'recipient'])
            ->whereIn('status', ['pending', 'approved', 'handover', 'completed'])
            ->latest()
            ->paginate(10);

        return view('pages.donor.requests', compact('requests'));
    }

    /** PATCH /requests/{request}/approve — Donor approves a request */
    public function approve(DonationRequest $donationRequest, Request $request)
    {
        $this->ensureDonorOwns($donationRequest);

        $oldStatus = $donationRequest->status;
        $donationRequest->update([
            'status'      => 'approved',
            'donor_note'  => $request->donor_note,
            'approved_at' => now(),
        ]);
        $donationRequest->item->update(['status' => 'matched']);

        // Reject all other pending requests for same item
        DonationRequest::where('item_id', $donationRequest->item_id)
            ->where('id', '!=', $donationRequest->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(),
            'status_changed', ['status' => $oldStatus], ['status' => 'approved']);

        return back()->with('success', 'Permohonan disetujui. Hubungi penerima untuk jadwal serah terima.');
    }

    /** PATCH /requests/{request}/reject — Donor rejects a request */
    public function reject(DonationRequest $donationRequest, Request $request)
    {
        $this->ensureDonorOwns($donationRequest);

        $oldStatus = $donationRequest->status;
        $donationRequest->update([
            'status'     => 'rejected',
            'donor_note' => $request->donor_note,
        ]);

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(),
            'status_changed', ['status' => $oldStatus], ['status' => 'rejected']);

        return back()->with('success', 'Permohonan ditolak.');
    }

    /** PATCH /donor/requests/{request}/handover — Donatur konfirmasi serah terima */
    public function handover(DonationRequest $donationRequest)
    {
        $this->ensureDonorOwns($donationRequest);
        abort_if($donationRequest->status !== 'approved', 422, 'Status tidak valid.');

        $donationRequest->update([
            'status'               => 'handover',
            'handover_at'          => now(),
            'donor_confirmed'      => true,
            'donor_confirmed_at'   => now(),
        ]);

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(),
            'status_changed', ['status' => 'approved'], ['status' => 'handover']);

        return back()->with('success', 'Konfirmasi serah terima Anda tercatat. Menunggu konfirmasi penerima.');
    }

    /** PATCH /recipient/requests/{request}/confirm — Penerima konfirmasi terima barang */
    public function recipientConfirm(DonationRequest $donationRequest)
    {
        abort_if($donationRequest->recipient_id !== auth()->id(), 403);
        abort_if($donationRequest->status !== 'handover', 422, 'Status tidak valid.');
        abort_if($donationRequest->recipient_confirmed, 422, 'Sudah dikonfirmasi.');

        $donationRequest->update([
            'recipient_confirmed'    => true,
            'recipient_confirmed_at' => now(),
        ]);

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(),
            'recipient_confirmed', null, ['recipient_confirmed' => true]);

        // Jika donatur & penerima sudah konfirmasi, tunggu admin
        return back()->with('success', 'Konfirmasi Anda tercatat. Menunggu konfirmasi admin untuk menyelesaikan proses.');
    }

    /** PATCH /recipient/requests/{request}/complete — (lama, tetap ada untuk kompatibilitas) */
    public function complete(DonationRequest $donationRequest)
    {
        return $this->recipientConfirm($donationRequest);
    }

    private function ensureDonorOwns(DonationRequest $req): void
    {
        abort_if($req->item->donor_id !== auth()->id(), 403);
    }
}
