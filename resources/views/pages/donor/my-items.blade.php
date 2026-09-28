@extends('layouts.app')
@section('title', 'Barang Saya')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Barang Saya</h1>
        <a href="{{ route('donor.items.create') }}"
           class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold transition">
            + Donasikan
        </a>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <p class="font-medium">Anda belum mendonasikan barang apapun.</p>
            <a href="{{ route('donor.items.create') }}" class="text-teal-600 text-sm mt-2 inline-block hover:underline">
                Mulai Donasi Sekarang →
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-4">
                {{-- Thumbnail --}}
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                    @if($item->primaryImage)
                        <img src="{{ $item->primaryImage->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover" />
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $item->title }}</p>
                    <p class="text-xs text-slate-400">{{ $item->city }} · {{ $item->category->name }}</p>
                    <p class="text-xs text-slate-400">{{ $item->donationRequests()->where('status','pending')->count() }} pengajuan masuk</p>
                </div>

                {{-- Status + actions --}}
                <div class="flex items-center gap-3 shrink-0">
                    @include('components.status-badge', ['status'=>$item->status, 'label'=>$item->statusLabel()])
                    <a href="{{ route('catalog.show', $item) }}"
                       class="text-xs text-teal-600 hover:underline">Lihat</a>
                    @if(in_array($item->status, ['draft','active']))
                    <form method="POST" action="{{ route('donor.items.destroy', $item) }}"
                          onsubmit="return confirm('Yakin hapus barang ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">Hapus</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    @endif
</div>
@endsection
