<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\DonationRequest;
use App\Models\AuditLog;
use App\Services\ImpactService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private ImpactService $impactService) {}

    /** GET /admin — Dashboard overview */
    public function dashboard()
    {
        $stats     = $this->impactService->getCumulativeStats();
        $pending   = Item::where('status', 'draft')->with('donor','category')->latest()->get();
        $recentReqs= DonationRequest::with('item','recipient')->latest()->limit(10)->get();

        // Pengajuan yang sudah dikonfirmasi donatur & penerima, menunggu konfirmasi admin
        $pendingConfirm = DonationRequest::where('status', 'handover')
            ->where('donor_confirmed', true)
            ->where('recipient_confirmed', true)
            ->where('admin_confirmed', false)
            ->with(['item', 'recipient', 'item.donor'])
            ->latest()
            ->get();

        return view('pages.admin.dashboard', compact('stats', 'pending', 'recentReqs', 'pendingConfirm'));
    }

    /** PATCH /admin/requests/{donationRequest}/confirm — Admin finalkan serah terima */
    public function confirmHandover(DonationRequest $donationRequest)
    {
        abort_if($donationRequest->status !== 'handover', 422, 'Status tidak valid.');
        abort_if(!$donationRequest->donor_confirmed || !$donationRequest->recipient_confirmed, 422,
            'Kedua pihak belum konfirmasi.');

        $donationRequest->update([
            'admin_confirmed'    => true,
            'admin_confirmed_at' => now(),
            'status'             => 'completed',
            'completed_at'       => now(),
        ]);
        $donationRequest->item->update(['status' => 'delivered']);

        $this->impactService->refreshToday();

        AuditLog::record('donation_request', $donationRequest->id, auth()->id(),
            'status_changed', ['status' => 'handover'], ['status' => 'completed']);

        return back()->with('success', "Serah terima \"{$donationRequest->item->title}\" telah diselesaikan.");
    }

    /** PATCH /admin/items/{item}/verify — Admin approves a submitted item */
    public function verifyItem(Item $item)
    {
        abort_if($item->status !== 'draft', 422, 'Item sudah diverifikasi.');

        $item->update([
            'status'      => 'active',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        AuditLog::record('item', $item->id, auth()->id(), 'verified',
            ['status' => 'draft'], ['status' => 'active']);

        return back()->with('success', "Item \"{$item->title}\" diverifikasi dan sekarang aktif di katalog.");
    }

    /** PATCH /admin/items/{item}/reject — Admin rejects a submitted item */
    public function rejectItem(Item $item, Request $request)
    {
        $item->update(['status' => 'closed']);

        AuditLog::record('item', $item->id, auth()->id(), 'rejected',
            ['status' => 'draft'], ['status' => 'closed', 'notes' => $request->notes]);

        return back()->with('success', "Item \"{$item->title}\" ditolak.");
    }
}
