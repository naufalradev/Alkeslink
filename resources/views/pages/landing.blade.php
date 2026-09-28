@extends('layouts.app')
@section('title', 'AlkesLink')

@section('content')

{{-- ══════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════ --}}
<section class="relative bg-gradient-to-br from-teal-600 via-teal-500 to-cyan-500 text-white overflow-hidden">
    {{-- Decorative circles --}}
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
    <div class="absolute bottom-0 left-10 w-40 h-40 bg-white/5 rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28
                flex flex-col lg:flex-row items-center gap-12">

        {{-- Text --}}
        <div class="flex-1 text-center lg:text-left space-y-6">
            <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-widest">
                Platform Hibah Alkes Indonesia
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight">
                Salurkan Alkes<br/>Tak Terpakai,<br/>
                <span class="text-orange-300">Ubah Jadi Harapan</span>
            </h1>
            <p class="text-teal-100 text-lg max-w-xl mx-auto lg:mx-0">
                Hubungkan donatur alat kesehatan bekas layak pakai dengan masyarakat yang membutuhkan — gratis, transparan, berdampak nyata.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                @guest
                    <a href="{{ route('register') }}"
                       class="px-7 py-3.5 rounded-xl bg-orange-500 hover:bg-orange-600 font-semibold shadow-lg transition active:scale-95">
                        Mulai Donasi — Gratis
                    </a>
                @endguest
                @auth
                    @if(auth()->user()->isDonor())
                        <a href="{{ route('donor.items.create') }}"
                           class="px-7 py-3.5 rounded-xl bg-orange-500 hover:bg-orange-600 font-semibold shadow-lg transition">
                            + Donasikan Sekarang
                        </a>
                    @endif
                @endauth
                <a href="{{ route('catalog.index') }}"
                   class="px-7 py-3.5 rounded-xl bg-white/20 hover:bg-white/30 font-semibold border border-white/30 transition">
                    Cari Alkes
                </a>
            </div>
        </div>

        {{-- Hero illustration --}}
        <div class="flex-1 flex justify-center lg:justify-end">
            <div class="relative w-72 h-72 lg:w-96 lg:h-96">
                <div class="absolute inset-0 bg-white/10 rounded-3xl rotate-6"></div>
                <div class="absolute inset-4 bg-white/10 rounded-2xl -rotate-3"></div>
                <div class="absolute inset-8 flex items-center justify-center">
                    <svg class="w-40 h-40 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     IMPACT METRICS BANNER
══════════════════════════════════════════ --}}
<section id="impact" class="bg-white py-16 border-b border-slate-100">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">

        <div class="space-y-1">
            <p class="text-4xl font-extrabold text-teal-600"
               id="counter-items" data-target="{{ $stats['total_items'] }}">0</p>
            <p class="text-sm text-slate-500 font-medium">Alkes Tersalurkan</p>
        </div>

        <div class="space-y-1">
            <p class="text-4xl font-extrabold text-orange-500"
               id="counter-value" data-target="{{ $stats['total_value_idr'] }}" data-format="currency">0</p>
            <p class="text-sm text-slate-500 font-medium">Perkiraan Biaya Terhematkan</p>
        </div>

        <div class="space-y-1">
            <p class="text-4xl font-extrabold text-blue-600"
               id="counter-cities" data-target="{{ $stats['total_cities'] }}">0</p>
            <p class="text-sm text-slate-500 font-medium">Kota Terjangkau</p>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════ --}}
<section id="how-it-works" class="bg-slate-50 py-20">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-slate-800 text-center mb-12">Bagaimana AlkesLink Bekerja?</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['step'=>'1','icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z','title'=>'Upload Barang','desc'=>'Donatur unggah foto, kondisi, dan lokasi alkes.'],
                ['step'=>'2','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Verifikasi Admin','desc'=>'Tim AlkesLink memverifikasi kelayakan barang.'],
                ['step'=>'3','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','title'=>'Penerima Mengajukan','desc'=>'Penerima mengajukan permohonan dengan alasan singkat.'],
                ['step'=>'4','icon'=>'M5 13l4 4L19 7','title'=>'Serah Terima','desc'=>'Donatur mengonfirmasi, penerima menerima barang.'],
            ] as $s)
            <div class="flex flex-col items-center text-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-lg">
                    {{ $s['step'] }}
                </div>
                <h3 class="font-semibold text-slate-700">{{ $s['title'] }}</h3>
                <p class="text-sm text-slate-500">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     CATALOG PREVIEW
══════════════════════════════════════════ --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-slate-800">Barang Tersedia Sekarang</h2>
            <a href="{{ route('catalog.index') }}" class="text-sm text-teal-600 hover:underline font-medium">
                Lihat Semua →
            </a>
        </div>

        {{-- Category filter pills --}}
        <div class="flex gap-2 flex-wrap mb-8">
            <a href="{{ route('catalog.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium bg-teal-600 text-white">Semua</a>
            @foreach($categories as $cat)
                <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:border-teal-500 hover:text-teal-600 transition">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if($featured->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <p class="text-lg font-medium">Belum ada barang tersedia.</p>
                <p class="text-sm mt-1">Jadilah yang pertama mendonasikan alkes Anda!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featured as $item)
                    <x-item-card :item="$item" />
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════
     CTA BANNER
══════════════════════════════════════════ --}}
<section class="bg-teal-600 py-20 text-white text-center">
    <div class="max-w-2xl mx-auto px-4 space-y-5">
        <h2 class="text-3xl font-extrabold">Punya alkes tak terpakai di rumah?</h2>
        <p class="text-teal-100">Kursi roda, kruk, alat cek gula darah, atau suplemen — semua bisa membantu orang lain!</p>
        <a href="{{ route('register') }}"
           class="inline-block px-8 py-4 rounded-xl bg-orange-500 hover:bg-orange-600
                  font-bold text-lg shadow-lg transition active:scale-95">
            Mulai Donasi — Gratis &rarr;
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/modules/impact-counter.js') }}"></script>
@endpush
