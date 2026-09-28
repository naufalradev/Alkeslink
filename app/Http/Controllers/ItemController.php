<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use App\Models\AuditLog;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /** GET /catalog — public catalog with filters */
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Item::active()
            ->with(['category', 'primaryImage', 'donor'])
            ->latest();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('city')) {
            $query->byCity($request->city);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) =>
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
            );
        }

        $items = $query->paginate(12)->withQueryString();

        return view('pages.catalog.index', compact('items', 'categories'));
    }

    /** GET /catalog/{item} — item detail */
    public function show(Item $item)
    {
        abort_if($item->status === 'draft', 404);

        $item->incrementViews();
        $item->load(['category', 'images', 'donor', 'donationRequests']);

        $existingRequest = null;
        if (auth()->check()) {
            $existingRequest = $item->donationRequests()
                ->where('recipient_id', auth()->id())
                ->whereIn('status', ['pending', 'approved', 'handover'])
                ->first();
        }

        return view('pages.catalog.show', compact('item', 'existingRequest'));
    }

    /** GET /donor/items/create */
    public function create()
    {
        $categories = Category::all();
        return view('pages.donor.upload', compact('categories'));
    }

    /** POST /donor/items */
    public function store(StoreItemRequest $request)
    {
        $item = Item::create([
            ...$request->safe()->except('images'),
            'donor_id' => auth()->id(),
            'status'   => 'draft',
        ]);

        // Handle multiple image uploads
        foreach ($request->file('images') as $index => $file) {
            $path = $file->store("items/{$item->id}", 'public');
            ItemImage::create([
                'item_id'    => $item->id,
                'image_url'  => Storage::url($path),
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }

        AuditLog::record('item', $item->id, auth()->id(), 'created', null, ['status' => 'draft']);

        return redirect()->route('donor.items')
            ->with('success', 'Barang berhasil diunggah dan sedang menunggu verifikasi admin.');
    }

    /** GET /donor/items */
    public function myItems()
    {
        $items = Item::where('donor_id', auth()->id())
            ->with(['category', 'primaryImage'])
            ->latest()
            ->paginate(10);

        return view('pages.donor.my-items', compact('items'));
    }

    /** DELETE /donor/items/{item} */
    public function destroy(Item $item)
    {
        abort_if($item->donor_id !== auth()->id() && !auth()->user()->isAdmin(), 403);
        abort_if(in_array($item->status, ['matched', 'delivered']), 422,
            'Tidak dapat menghapus barang yang sedang/sudah diproses.');

        AuditLog::record('item', $item->id, auth()->id(), 'deleted', ['status' => $item->status]);
        $item->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
