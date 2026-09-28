@extends('layouts.app')
@section('title', 'Katalog Alkes')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Katalog Alat Kesehatan</h1>
        <p class="text-slate-500 mt-1 text-sm">Temukan alkes layak pakai di sekitar Anda secara gratis.</p>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('catalog.index') }}"
          class="bg-white rounded-2xl shadow-sm p-4 mb-8 flex flex-col sm:flex-row gap-3 items-end">

        {{-- Search --}}
        <div class="flex-1">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama alkes..."
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
        </div>

        {{-- Category --}}
        <div class="sm:w-48">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-1">Kategori</label>
            <select name="category"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- City --}}
        <div class="sm:w-44">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-1">Kota</label>
            <input type="text" name="city" value="{{ request('city') }}"
                   placeholder="Mis. Jakarta"
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
        </div>

        <button type="submit"
                class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition shrink-0">
            Filter
        </button>
        @if(request()->hasAny(['search','category','city']))
            <a href="{{ route('catalog.index') }}"
               class="px-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-500 hover:bg-slate-50 transition shrink-0">
                Reset
            </a>
        @endif
    </form>

    {{-- ── Items Grid ── --}}
    @if($items->isEmpty())
        <div class="text-center py-20 text-slate-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-medium">Tidak ada barang yang cocok.</p>
            <p class="text-sm mt-1">Coba ubah filter pencarian Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($items as $item)
                <x-item-card :item="$item" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
