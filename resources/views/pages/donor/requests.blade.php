@extends('layouts.app')
@section('title', 'Permintaan Masuk')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-slate-800 mb-8">Permintaan Masuk</h1>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <p class="font-medium">Belum ada permintaan untuk barang Anda.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($requests as $req)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs text-slate-400">Untuk barang:</p>
                        <a href="{{ route('catalog.show', $req->item) }}"
                           class="font-semibold text-slate-800 hover:text-teal-600 transition text-sm">
                            {{ $req->item->title }}
                        </a>
                    </div>
                    @include('components.status-badge', ['status'=>$req->status, 'label'=>$req->statusLabel()])
                </div>

                <div class="bg-slate-50 rounded-xl p-3 text-sm text-slate-600">
                    <p class="text-xs font-semibold text-slate-400 uppercase mb-1">Alasan Penerima:</p>
                    {{ $req->reason }}
                </div>

                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span>Pemohon: <strong>{{ $req->recipient->name }}</strong></span>
                    <span>· {{ $req->created_at->diffForHumans() }}</span>
                    @if($req->support_doc_url)
                        <a href="{{ $req->support_doc_url }}" target="_blank"
                           class="text-teal-600 hover:underline">Lihat Dokumen</a>
                    @endif
                </div>

                {{-- Tombol Setujui / Tolak (pending) --}}
                @if($req->status === 'pending')
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <form method="POST" action="{{ route('donor.requests.approve', $req) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <textarea name="donor_note" placeholder="Catatan untuk penerima (opsional)"
                                  rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl mb-2 focus:outline-none focus:ring-1 focus:ring-teal-400 resize-none"></textarea>
                        <button type="submit"
                                class="w-full py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition">
                            ✓ Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ route('donor.requests.reject', $req) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <textarea name="donor_note" placeholder="Alasan penolakan"
                                  rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl mb-2 focus:outline-none focus:ring-1 focus:ring-red-300 resize-none"></textarea>
                        <button type="submit"
                                class="w-full py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition">
                            ✕ Tolak
                        </button>
                    </form>
                </div>
                @endif

                {{-- Disetujui: tombol chat + konfirmasi serah terima --}}
                @if($req->status === 'approved')
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('chat.show', $req) }}"
                       class="flex-1 text-center py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold transition">
                        💬 Buka Chat dengan Penerima
                    </a>
                    @if(!$req->donor_confirmed)
                    <form method="POST" action="{{ route('donor.requests.handover', $req) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition">
                            Konfirmasi Serah Terima
                        </button>
                    </form>
                    @else
                    <div class="flex-1 text-center py-2 rounded-xl bg-purple-50 text-purple-600 text-xs font-semibold">
                        ✓ Sudah Dikonfirmasi
                    </div>
                    @endif
                </div>
                @endif

                {{-- Dalam handover: chat + status konfirmasi --}}
                @if($req->status === 'handover')
                <div class="pt-2 border-t border-slate-100 space-y-3">
                    <a href="{{ route('chat.show', $req) }}"
                       class="block w-full text-center py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold transition">
                        💬 Buka Chat dengan Penerima
                    </a>

                    {{-- Progress konfirmasi --}}
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="rounded-lg border p-2 {{ $req->donor_confirmed ? 'border-teal-300 bg-teal-50 text-teal-600' : 'border-slate-200 text-slate-400' }}">
                            <p class="font-semibold">Donatur</p>
                            <p>{{ $req->donor_confirmed ? '✓ Konfirmasi' : 'Belum' }}</p>
                        </div>
                        <div class="rounded-lg border p-2 {{ $req->recipient_confirmed ? 'border-teal-300 bg-teal-50 text-teal-600' : 'border-slate-200 text-slate-400' }}">
                            <p class="font-semibold">Penerima</p>
                            <p>{{ $req->recipient_confirmed ? '✓ Konfirmasi' : 'Belum' }}</p>
                        </div>
                        <div class="rounded-lg border p-2 {{ $req->admin_confirmed ? 'border-teal-300 bg-teal-50 text-teal-600' : 'border-slate-200 text-slate-400' }}">
                            <p class="font-semibold">Admin</p>
                            <p>{{ $req->admin_confirmed ? '✓ Selesai' : 'Menunggu' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($req->status === 'completed')
                <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs text-green-600">
                    <span class="font-bold">🎉 Serah terima selesai</span>
                    <a href="{{ route('chat.show', $req) }}" class="ml-auto text-blue-500 hover:underline">Lihat Chat</a>
                </div>
                @endif

                @if($req->donor_note)
                <div class="border-t border-slate-100 pt-2 text-xs text-slate-500">
                    <span class="font-semibold">Catatan Anda:</span> {{ $req->donor_note }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
