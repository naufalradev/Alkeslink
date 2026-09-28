@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-slate-800 mb-8">Dashboard Admin</h1>

    {{-- Impact Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
        @foreach([
            ['label'=>'Tersalurkan',  'value'=>$stats['total_items'],      'color'=>'teal'],
            ['label'=>'Nilai (jt)',   'value'=>'Rp '.number_format($stats['total_value_idr']/1000000,1).'M', 'color'=>'orange'],
            ['label'=>'Kota',         'value'=>$stats['total_cities'],     'color'=>'blue'],
            ['label'=>'Donatur',      'value'=>$stats['total_donors'],     'color'=>'purple'],
            ['label'=>'Penerima',     'value'=>$stats['total_recipients'], 'color'=>'green'],
        ] as $s)
        <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm text-center">
            <p class="text-2xl font-extrabold text-{{ $s['color'] }}-600">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Pending Verifications --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-10">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-700">Menunggu Verifikasi</h2>
            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-semibold">
                {{ $pending->count() }} barang
            </span>
        </div>

        @if($pending->isEmpty())
            <p class="text-center text-slate-400 text-sm py-8">Tidak ada barang yang menunggu verifikasi. 🎉</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($pending as $item)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                        @if($item->primaryImage)
                            <img src="{{ $item->primaryImage->image_url }}" class="w-full h-full object-cover" alt="" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-slate-800 truncate">{{ $item->title }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $item->donor->name }} · {{ $item->city }} · {{ $item->category->name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('catalog.show', $item) }}" target="_blank"
                           class="text-xs text-teal-600 hover:underline">Lihat</a>
                        <form method="POST" action="{{ route('admin.items.verify', $item) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition">
                                Verifikasi
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.items.reject', $item) }}"
                              onsubmit="return confirm('Tolak barang ini?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition">
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Menunggu Konfirmasi Admin --}}
    @if($pendingConfirm->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-orange-200 overflow-hidden mb-10">
        <div class="px-6 py-4 border-b border-orange-100 flex items-center justify-between bg-orange-50">
            <h2 class="font-bold text-orange-700">⏳ Menunggu Konfirmasi Admin</h2>
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">
                {{ $pendingConfirm->count() }} pengajuan
            </span>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($pendingConfirm as $req)
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-slate-800 truncate">{{ $req->item->title }}</p>
                    <p class="text-xs text-slate-400">
                        Donatur: <strong>{{ $req->item->donor->name }}</strong>
                        · Penerima: <strong>{{ $req->recipient->name }}</strong>
                    </p>
                    <div class="flex gap-2 mt-1">
                        <span class="text-[10px] px-2 py-0.5 rounded-full {{ $req->donor_confirmed ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 text-slate-400' }}">
                            Donatur {{ $req->donor_confirmed ? '✓' : '✗' }}
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full {{ $req->recipient_confirmed ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 text-slate-400' }}">
                            Penerima {{ $req->recipient_confirmed ? '✓' : '✗' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('chat.show', $req) }}" target="_blank"
                       class="text-xs text-blue-500 hover:underline">Lihat Chat</a>
                    <form method="POST" action="{{ route('admin.requests.confirm', $req) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition">
                            ✓ Selesaikan
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Requests --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-700">Pengajuan Terbaru</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentReqs as $req)
            <div class="px-6 py-3 flex items-center gap-4 text-sm">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-slate-700 truncate">{{ $req->item->title }}</p>
                    <p class="text-xs text-slate-400">{{ $req->recipient->name }} → {{ $req->created_at->diffForHumans() }}</p>
                </div>
                @include('components.status-badge', ['status'=>$req->status,'label'=>$req->statusLabel()])
            </div>
            @empty
            <p class="text-center text-slate-400 text-sm py-8">Belum ada pengajuan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
