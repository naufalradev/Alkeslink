@extends('layouts.app')
@section('title', 'Donasikan Alkes')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Donasikan Alat Kesehatan</h1>
    <p class="text-slate-500 text-sm mb-8">Isi form di bawah untuk mendaftarkan alkes Anda. Tim kami akan memverifikasi sebelum ditayangkan.</p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('donor.items.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Nama Barang <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title') }}" maxlength="200" required
                   placeholder="Mis. Kursi Roda Lipat Standar"
                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
        </div>

        {{-- Category + Condition --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_id" required
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition bg-white">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Kondisi <span class="text-red-500">*</span>
                </label>
                <select name="condition" required
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition bg-white">
                    <option value="baik"         {{ old('condition')==='baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="cukup"        {{ old('condition')==='cukup' ? 'selected' : '' }}>Kondisi Cukup</option>
                    <option value="perlu_servis" {{ old('condition')==='perlu_servis' ? 'selected' : '' }}>Perlu Servis</option>
                </select>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" maxlength="2000"
                      placeholder="Jelaskan kondisi, riwayat penggunaan, aksesoris yang disertakan, dll."
                      class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition resize-none">{{ old('description') }}</textarea>
        </div>

        {{-- Quantity + Estimated value --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Jumlah</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="99"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Estimasi Nilai (Rp)</label>
                <input type="number" name="estimated_value" value="{{ old('estimated_value') }}" min="0" step="1000"
                       placeholder="0"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
            </div>
        </div>

        {{-- Location --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Lokasi</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="province" value="{{ old('province') }}" required placeholder="Provinsi"
                       class="px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                <input type="text" name="city" value="{{ old('city') }}" required placeholder="Kota/Kabupaten"
                       class="px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
                <input type="text" name="district" value="{{ old('district') }}" required placeholder="Kecamatan"
                       class="px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition" />
            </div>
        </div>

        {{-- Images --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Foto Barang <span class="text-red-500">*</span>
                <span class="font-normal text-slate-400">(Min. 1, Maks. 5 foto · Maks. 2 MB/foto)</span>
            </label>
            <div id="image-drop-zone"
                 class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center cursor-pointer hover:border-teal-400 transition">
                <input type="file" name="images[]" id="image-input" multiple accept="image/*" class="hidden" />
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-slate-400">Klik atau seret foto ke sini</p>
            </div>
            <div id="image-previews" class="flex gap-2 flex-wrap mt-3"></div>
        </div>

        <button type="submit"
                class="w-full py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm transition active:scale-95">
            Daftarkan Barang →
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/image-preview.js') }}"></script>
@endpush
