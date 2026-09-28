@extends('layouts.app')
@section('title', 'Pengajuan Saya')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Pengajuan Saya</h1>

    @if($requests->isEmpty())
        <div class="text-center py-20 text-slate-400">
            <p class="font-medium">Belum ada pengajuan.</p>
            <a href="{{ route('catalog.index') }}" class="text-teal-600 text-sm mt-2 inline-block hover:underline">
                Cari Alkes di Katalog →
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($requests as $req)
            <div class="bg-white rounded-2xl border border-slate-100 divide-y divide-slate-100">

                {{-- ── Header row ── --}}
                <div class="p-5 flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                        @if($req->item->primaryImage)
                            <img src="{{ $req->item->primaryImage->image_url }}"
                                 class="w-full h-full object-cover" alt="" />
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('catalog.show', $req->item) }}"
                           class="font-semibold text-slate-800 hover:text-teal-600 transition text-sm leading-snug line-clamp-1">
                            {{ $req->item->title }}
                        </a>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $req->item->city }} · {{ $req->item->category->name }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Diajukan {{ $req->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Status: hanya teks bold, tanpa badge --}}
                    <div class="text-right shrink-0">
                        <p class="text-xs font-bold
                            @if($req->status === 'pending') text-yellow-600
                            @elseif($req->status === 'approved') text-blue-600
                            @elseif($req->status === 'rejected') text-red-500
                            @elseif($req->status === 'handover') text-purple-600
                            @elseif($req->status === 'completed') text-teal-600
                            @else text-slate-500 @endif">
                            {{ $req->statusLabel() }}
                        </p>
                    </div>
                </div>

                {{-- ── Timeline ── --}}
                <div class="px-5 py-3">
                    <ol class="flex items-center">
                        @foreach($req->timelineSteps() as $i => $step)
                        <li class="flex items-center {{ $i < count($req->timelineSteps())-1 ? 'flex-1' : '' }}">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0
                                {{ $step['done'] ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                {{ $step['done'] ? '✓' : ($i+1) }}
                            </span>
                            <span class="text-[10px] ml-1.5 {{ $step['done'] ? 'text-teal-700 font-medium' : 'text-slate-400' }} hidden sm:block truncate">
                                {{ $step['label'] }}
                            </span>
                            @if($i < count($req->timelineSteps())-1)
                                <div class="flex-1 h-px mx-2 {{ $step['done'] ? 'bg-teal-300' : 'bg-slate-200' }}"></div>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>

                {{-- ── Catatan donatur ── --}}
                @if($req->donor_note)
                <div class="px-5 py-3 text-sm text-slate-600">
                    <span class="font-semibold text-slate-700">Catatan donatur:</span>
                    {{ $req->donor_note }}
                </div>
                @endif

                {{-- ── Tombol Chat (approved / handover / completed) ── --}}
                @if(in_array($req->status, ['approved', 'handover', 'completed']))
                <div class="px-5 py-3">
                    <a href="{{ route('chat.show', $req) }}"
                       class="text-sm font-semibold text-teal-600 hover:text-teal-700 transition">
                        Buka Chat dengan Donatur →
                    </a>
                </div>
                @endif

                {{-- ── Konfirmasi serah terima (handover) ── --}}
                @if($req->status === 'handover')
                <div class="px-5 py-4 space-y-4">

                    {{-- Progress 3 pihak --}}
                    <div class="grid grid-cols-3 gap-3 text-center text-xs">
                        @foreach([
                            ['label' => 'Donatur',  'done' => $req->donor_confirmed],
                            ['label' => 'Penerima', 'done' => $req->recipient_confirmed],
                            ['label' => 'Admin',    'done' => $req->admin_confirmed],
                        ] as $p)
                        <div class="rounded-xl border py-3 {{ $p['done'] ? 'border-teal-200 bg-teal-50' : 'border-slate-200' }}">
                            <p class="font-semibold {{ $p['done'] ? 'text-teal-700' : 'text-slate-500' }}">
                                {{ $p['label'] }}
                            </p>
                            <p class="{{ $p['done'] ? 'text-teal-600 font-bold' : 'text-slate-400' }} mt-0.5">
                                {{ $p['done'] ? '✓ Konfirmasi' : 'Belum' }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    @if(!$req->recipient_confirmed)
                    <form method="POST" action="{{ route('recipient.requests.confirm', $req) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition">
                            Konfirmasi Barang Diterima
                        </button>
                    </form>
                    @else
                    <p class="text-sm text-center text-slate-500">
                        Konfirmasi Anda tercatat. Menunggu konfirmasi admin.
                    </p>
                    @endif
                </div>
                @endif

                {{-- ── Form ulasan (completed) ── --}}
                @if($req->status === 'completed')
                <div class="px-5 py-4">
                    @if($req->review)
                        {{-- Sudah ada ulasan --}}
                        <p class="text-xs font-semibold text-slate-500 mb-2">Ulasan Anda</p>
                        <div class="flex gap-0.5 mb-1" aria-label="Rating {{ $req->review->rating }} dari 5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $req->review->rating ? 'text-yellow-400' : 'text-slate-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($req->review->comment)
                            <p class="text-sm text-slate-600">{{ $req->review->comment }}</p>
                        @endif

                    @else
                        {{-- Form ulasan baru --}}
                        <p class="text-sm font-semibold text-slate-700 mb-3">Beri Ulasan untuk Barang Ini</p>

                        <form method="POST" action="{{ route('reviews.store') }}"
                              enctype="multipart/form-data"
                              class="space-y-3"
                              x-data="{ rating: 0, previews: [] }"
                              @submit.prevent="
                                  if (rating === 0) { alert('Pilih rating bintang terlebih dahulu.'); return; }
                                  $el.submit();
                              ">
                            @csrf
                            <input type="hidden" name="donation_request_id" value="{{ $req->id }}" />
                            <input type="hidden" name="rating" x-bind:value="rating" />

                            {{-- Bintang klik --}}
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        x-on:click="rating = {{ $i }}"
                                        class="text-3xl leading-none focus:outline-none transition-transform active:scale-90">
                                    <span :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-slate-200'">★</span>
                                </button>
                                @endfor
                            </div>

                            <textarea name="comment" rows="2" maxlength="1000"
                                      placeholder="Ceritakan pengalaman menerima barang ini… (opsional)"
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition resize-none"></textarea>

                            {{-- Upload foto ulasan (maks 3) --}}
                            <div>
                                <label class="text-xs font-semibold text-slate-600 block mb-1.5">
                                    Foto Barang yang Diterima <span class="text-slate-400 font-normal">(opsional, maks 3)</span>
                                </label>
                                <input type="file" name="images[]" accept="image/*" multiple
                                       @change="previews = Array.from($event.target.files).slice(0,3).map(f => URL.createObjectURL(f))"
                                       class="w-full text-sm text-slate-500
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:bg-slate-100 file:text-slate-600 file:text-xs file:font-medium
                                              hover:file:bg-slate-200 transition" />
                                {{-- Preview thumbnails --}}
                                <div class="flex gap-2 mt-2 flex-wrap" x-show="previews.length > 0">
                                    <template x-for="src in previews" :key="src">
                                        <img :src="src" class="w-16 h-16 rounded-xl object-cover border border-slate-200" />
                                    </template>
                                </div>
                            </div>

                            <button type="submit"
                                    class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition">
                                Kirim Ulasan
                            </button>
                        </form>
                    @endif
                </div>
                @endif

            </div>{{-- /card --}}
            @endforeach
        </div>

        <div class="mt-8">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
