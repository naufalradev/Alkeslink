@extends('layouts.app')
@section('title', $item->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 sm:py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-slate-400 mb-5 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-teal-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('catalog.index') }}" class="hover:text-teal-600 transition">Katalog</a>
        <span>/</span>
        <span class="text-slate-600 truncate max-w-[180px]">{{ $item->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">

        {{-- ── LEFT: Gallery + Tabs ── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Carousel --}}
            @php $images = $item->images; @endphp
            <div x-data="{ active: 0, images: {{ $images->count() }} }" class="space-y-2">

                {{-- Main image --}}
                <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100">
                    @if($images->isNotEmpty())
                        @foreach($images as $i => $img)
                        <img src="{{ $img->image_url }}"
                             alt="{{ $item->title }}"
                             x-show="active === {{ $i }}"
                             class="absolute inset-0 w-full h-full object-cover" />
                        @endforeach

                        {{-- Prev / Next (only if multiple) --}}
                        @if($images->count() > 1)
                        <button @click="active = (active - 1 + images) % images"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 flex items-center justify-center text-slate-600 hover:bg-white transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button @click="active = (active + 1) % images"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 flex items-center justify-center text-slate-600 hover:bg-white transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        {{-- Dot indicators --}}
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                            @foreach($images as $i => $img)
                            <button @click="active = {{ $i }}"
                                    :class="active === {{ $i }} ? 'bg-white w-4' : 'bg-white/50 w-2'"
                                    class="h-2 rounded-full transition-all"></button>
                            @endforeach
                        </div>
                        @endif
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-slate-300 text-sm">
                            Tidak ada foto
                        </div>
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if($images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                    @foreach($images as $i => $img)
                    <button @click="active = {{ $i }}"
                            :class="active === {{ $i }} ? 'ring-2 ring-teal-500 ring-offset-1' : 'opacity-60 hover:opacity-100'"
                            class="w-14 h-14 rounded-lg overflow-hidden shrink-0 transition">
                        <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-cover" />
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Tabs --}}
            <div x-data="{ tab: 'desc' }">
                <div class="flex border-b border-slate-200 mb-4">
                    @foreach(['desc' => 'Deskripsi', 'sterile' => 'Panduan Kebersihan', 'faq' => 'FAQ'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-b-2 border-teal-600 text-teal-600 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                            class="px-4 py-2 text-sm transition">{{ $label }}</button>
                    @endforeach
                </div>

                <div x-show="tab === 'desc'" class="text-sm text-slate-600 leading-relaxed">
                    {{ $item->description ?? 'Tidak ada deskripsi tambahan.' }}
                </div>

                <div x-show="tab === 'sterile'" class="text-sm text-slate-600 leading-relaxed space-y-2">
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Bersihkan permukaan dengan kain yang dibasahi alkohol 70%.</li>
                        <li>Untuk alat logam, rendam dalam larutan disinfektan 15–30 menit.</li>
                        <li>Bilas dengan air bersih dan keringkan sepenuhnya.</li>
                        <li>Konsultasikan dengan tenaga kesehatan untuk alkes yang bersentuhan langsung dengan tubuh.</li>
                        <li>Simpan di tempat kering dan bersih setelah digunakan.</li>
                    </ol>
                </div>

                <div x-show="tab === 'faq'" class="text-sm text-slate-600 space-y-4">
                    @foreach([
                        ['q' => 'Apakah barang ini sudah diverifikasi?', 'a' => 'Ya, setiap barang diverifikasi oleh tim AlkesLink sebelum ditampilkan di katalog.'],
                        ['q' => 'Bagaimana proses pengiriman?',          'a' => 'Serah terima dapat dilakukan langsung atau via ekspedisi sesuai kesepakatan.'],
                        ['q' => 'Apakah ada biaya?',                     'a' => 'Platform AlkesLink sepenuhnya gratis. Biaya pengiriman (jika ada) disepakati kedua pihak.'],
                    ] as $f)
                    <div>
                        <p class="font-semibold text-slate-700">{{ $f['q'] }}</p>
                        <p class="text-slate-500 mt-1">{{ $f['a'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Info + CTA ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Title + location --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-wide
                    @if($item->status === 'active') text-teal-600
                    @elseif($item->status === 'matched') text-blue-600
                    @elseif($item->status === 'delivered') text-slate-500
                    @else text-slate-400 @endif
                    mb-1">
                    {{ $item->statusLabel() }}
                </p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 leading-snug">{{ $item->title }}</h1>
                <p class="text-sm text-slate-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $item->district }}, {{ $item->city }}
                </p>
            </div>

            {{-- Detail rows --}}
            <div class="bg-white rounded-2xl border border-slate-100 divide-y divide-slate-100 text-sm">
                @foreach([
                    ['Kategori',       $item->category->name],
                    ['Kondisi',        $item->conditionLabel()],
                    ['Jumlah',         $item->quantity . ' unit'],
                ] as $row)
                <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-slate-500">{{ $row[0] }}</span>
                    <span class="font-medium text-slate-700">{{ $row[1] }}</span>
                </div>
                @endforeach
                @if($item->estimated_value > 0)
                <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-slate-500">Estimasi Nilai</span>
                    <span class="font-semibold text-teal-600">Rp {{ number_format($item->estimated_value,0,',','.') }}</span>
                </div>
                @endif
            </div>

            {{-- Donor --}}
            <div class="flex items-center gap-3 px-4 py-3 bg-white rounded-2xl border border-slate-100 text-sm">
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-semibold text-slate-600 shrink-0 text-xs">
                    {{ strtoupper(substr($item->donor->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-slate-400">Donatur</p>
                    <p class="font-semibold text-slate-700">{{ $item->donor->name }}</p>
                </div>
            </div>

            {{-- CTA --}}
            @if($item->status === 'active')
                @guest
                    <a href="{{ route('login') }}"
                       class="block w-full text-center py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm transition">
                        Masuk untuk Mengajukan
                    </a>
                @endguest
                @auth
                    @if(auth()->user()->isRecipient())
                        @if($existingRequest)
                            <p class="text-sm text-center py-3 text-slate-500">
                                Pengajuan Anda sedang diproses —
                                <span class="font-semibold">{{ $existingRequest->statusLabel() }}</span>
                            </p>
                        @else
                            <button onclick="document.getElementById('req-modal').classList.remove('hidden'); document.getElementById('req-modal').classList.add('flex');"
                                    class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm transition active:scale-[.98]">
                                Ajukan Permohonan
                            </button>
                        @endif
                    @endif
                @endauth
            @else
                <p class="text-center py-3 text-sm text-slate-400">Barang ini tidak tersedia saat ini.</p>
            @endif

        </div>
    </div>
</div>

{{-- ── Ulasan ── --}}
@php
    $reviews     = $item->reviews()->with(['reviewer','images'])->latest()->get();
    $avgRating   = $item->avgRating();
    $reviewCount = $reviews->count();
@endphp

@if($reviewCount > 0 || $item->status === 'delivered')
<div class="max-w-5xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Ulasan</h2>
            @if($reviewCount > 0)
            <div class="flex items-center gap-1.5 text-sm">
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-slate-200' }}"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <span class="font-semibold text-slate-700">{{ $avgRating }}</span>
                <span class="text-slate-400 text-xs">({{ $reviewCount }})</span>
            </div>
            @endif
        </div>

        @if($reviews->isEmpty())
            <p class="text-center text-slate-400 text-sm py-10">Belum ada ulasan.</p>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($reviews as $review)
            <div class="px-5 py-4">
                <div class="flex items-start gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-semibold text-slate-600 text-xs shrink-0">
                        {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700">{{ $review->reviewer->name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-400">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                @if($review->comment)
                    <p class="text-sm text-slate-600 leading-relaxed mb-3">{{ $review->comment }}</p>
                @endif
                {{-- Review images --}}
                @if($review->images->isNotEmpty())
                <div class="flex gap-2 flex-wrap">
                    @foreach($review->images as $img)
                    <a href="{{ $img->image_url }}" target="_blank">
                        <img src="{{ $img->image_url }}" alt=""
                             class="w-20 h-20 rounded-xl object-cover border border-slate-100 hover:opacity-90 transition" />
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

{{-- ── Request modal ── --}}
<div id="req-modal"
     class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl p-6 space-y-4"
         style="animation: modalPop .2s ease both">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Ajukan Permohonan</h2>
            <button onclick="document.getElementById('req-modal').classList.add('hidden'); document.getElementById('req-modal').classList.remove('flex');"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-sm text-slate-500">Jelaskan kebutuhan Anda untuk <strong>{{ $item->title }}</strong>.</p>

        <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}" />

            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1.5">
                    Alasan <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="4" required minlength="20" maxlength="1000"
                          placeholder="Ceritakan mengapa Anda membutuhkan alkes ini…"
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition resize-none"></textarea>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1.5">
                    Dokumen Pendukung <span class="text-slate-400 font-normal">(KTP / Surat Dokter, opsional)</span>
                </label>
                <input type="file" name="support_doc" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-slate-500
                              file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                              file:bg-slate-100 file:text-slate-600 file:text-xs file:font-medium
                              hover:file:bg-slate-200 transition" />
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button"
                        onclick="document.getElementById('req-modal').classList.add('hidden'); document.getElementById('req-modal').classList.remove('flex');"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('req-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
});
</script>
@endpush
@endsection
