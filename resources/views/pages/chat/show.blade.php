@extends('layouts.app')
@section('title', 'Chat — ' . $donationRequest->item->title)

@section('content')
{{-- Full-height chat shell, sticks to viewport on mobile --}}
<div class="flex flex-col" style="min-height: calc(100vh - 64px);">

    {{-- ── Top bar ── --}}
    <div class="bg-white border-b border-slate-100 px-4 py-3 flex items-center gap-3 sticky top-16 z-10">
        <a href="{{ url()->previous() }}" class="shrink-0 text-slate-400 hover:text-slate-600 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="w-9 h-9 rounded-lg overflow-hidden bg-slate-100 shrink-0">
            @if($donationRequest->item->primaryImage)
                <img src="{{ $donationRequest->item->primaryImage->image_url }}"
                     class="w-full h-full object-cover" alt="" />
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">–</div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-800 text-sm truncate leading-tight">
                {{ $donationRequest->item->title }}
            </p>
            <p class="text-xs text-slate-400 truncate">
                @if(auth()->user()->isDonor())
                    Penerima: {{ $donationRequest->recipient->name }}
                @else
                    Donatur: {{ $donationRequest->item->donor->name }}
                @endif
            </p>
        </div>
        {{-- Status teks --}}
        <span class="text-xs font-bold shrink-0
            @if($donationRequest->status === 'approved') text-blue-600
            @elseif($donationRequest->status === 'handover') text-purple-600
            @elseif($donationRequest->status === 'completed') text-teal-600
            @else text-slate-400 @endif">
            {{ $donationRequest->statusLabel() }}
        </span>
    </div>

    {{-- ── Message list ── --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1" id="msg-list">

        @if($donationRequest->messages->isEmpty())
            <div class="flex items-center justify-center h-32 text-slate-400 text-sm">
                Mulai percakapan di sini.
            </div>
        @endif

        @php $prevDate = null; @endphp
        @foreach($donationRequest->messages as $msg)
        @php $isMe = $msg->sender_id === auth()->id(); $msgDate = $msg->created_at->toDateString(); @endphp

        {{-- Date separator --}}
        @if($msgDate !== $prevDate)
        <div class="flex justify-center py-2">
            <span class="text-[10px] text-slate-400 bg-slate-100 rounded-full px-3 py-0.5">
                {{ $msg->created_at->isSameDay(now()) ? 'Hari ini' : $msg->created_at->format('d M Y') }}
            </span>
        </div>
        @php $prevDate = $msgDate; @endphp
        @endif

        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} mb-0.5">
            <div class="max-w-[78%] sm:max-w-[65%] space-y-1">

                @if(!$isMe && ($loop->first || $donationRequest->messages[$loop->index - 1]->sender_id !== $msg->sender_id))
                    <p class="text-[10px] text-slate-400 font-medium px-1">{{ $msg->sender->name ?? '' }}</p>
                @endif

                {{-- Image attachment --}}
                @if($msg->image_url)
                <div class="{{ $isMe ? 'flex justify-end' : '' }}">
                    <a href="{{ $msg->image_url }}" target="_blank" class="block">
                        <img src="{{ $msg->image_url }}"
                             alt="Gambar"
                             class="rounded-2xl {{ $isMe ? 'rounded-br-sm' : 'rounded-bl-sm' }} max-w-full max-h-60 object-cover" />
                    </a>
                </div>
                @endif

                {{-- Text body --}}
                @if($msg->body)
                <div class="px-3.5 py-2.5 rounded-2xl text-sm leading-relaxed break-words
                    {{ $isMe
                        ? 'bg-teal-600 text-white rounded-br-sm'
                        : 'bg-white border border-slate-200 text-slate-700 rounded-bl-sm' }}">
                    {!! nl2br(e($msg->body)) !!}
                </div>
                @endif

                <p class="text-[10px] text-slate-400 px-1 {{ $isMe ? 'text-right' : '' }}">
                    {{ $msg->created_at->format('H:i') }}
                    @if($isMe && $msg->read_at)
                        · <span class="text-teal-400">Dibaca</span>
                    @endif
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Konfirmasi serah terima ── --}}
    @if(in_array($donationRequest->status, ['approved', 'handover']))
    <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
        <p class="text-xs font-semibold text-slate-600 mb-2">Konfirmasi Serah Terima</p>
        <div class="flex gap-2 text-xs mb-3">
            @foreach([
                ['label'=>'Donatur',  'done'=>$donationRequest->donor_confirmed],
                ['label'=>'Penerima', 'done'=>$donationRequest->recipient_confirmed],
                ['label'=>'Admin',    'done'=>$donationRequest->admin_confirmed],
            ] as $p)
            <div class="flex-1 text-center py-2 rounded-lg border
                {{ $p['done'] ? 'border-teal-200 bg-teal-50 text-teal-700 font-semibold' : 'border-slate-200 text-slate-400' }}">
                <p class="font-semibold">{{ $p['label'] }}</p>
                <p class="text-[10px] mt-0.5">{{ $p['done'] ? '✓' : '–' }}</p>
            </div>
            @endforeach
        </div>

        @if(auth()->user()->isDonor() && !$donationRequest->donor_confirmed && $donationRequest->status === 'approved')
            <form method="POST" action="{{ route('donor.requests.handover', $donationRequest) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                    Konfirmasi Serah Terima
                </button>
            </form>
        @endif

        @if(auth()->user()->isRecipient() && !$donationRequest->recipient_confirmed && $donationRequest->status === 'handover')
            <form method="POST" action="{{ route('recipient.requests.confirm', $donationRequest) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition">
                    Konfirmasi Barang Diterima
                </button>
            </form>
        @endif
    </div>
    @endif

    {{-- ── Input area ── --}}
    <div class="border-t border-slate-100 bg-white px-3 py-3 sticky bottom-0"
         x-data="chatInput()">

        {{-- Image preview --}}
        <div x-show="preview" class="mb-2 relative inline-block">
            <img :src="preview" class="h-20 rounded-xl object-cover border border-slate-200" alt="Preview" />
            <button type="button" @click="clearImage()"
                    class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-slate-700 text-white text-xs flex items-center justify-center">
                ×
            </button>
        </div>

        <form method="POST" action="{{ route('chat.store', $donationRequest) }}"
              enctype="multipart/form-data"
              class="flex items-end gap-2"
              @submit="submitting = true">
            @csrf

            {{-- Hidden file input --}}
            <input type="file" name="image" accept="image/*" x-ref="fileInput"
                   class="hidden" @change="onFile($event)" />

            {{-- Attach image button --}}
            <button type="button" @click="$refs.fileInput.click()"
                    class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-teal-600 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 21"/>
                </svg>
            </button>

            {{-- Text input --}}
            <textarea name="body" rows="1"
                      x-ref="textarea"
                      @input="autoGrow($el)"
                      @keydown.enter.exact.prevent="!submitting && $el.closest('form').requestSubmit()"
                      placeholder="Pesan…"
                      maxlength="2000"
                      class="flex-1 px-3 py-2 text-sm bg-slate-100 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none leading-relaxed"
                      style="max-height:120px"></textarea>

            {{-- Send button --}}
            <button type="submit" :disabled="submitting"
                    class="shrink-0 w-9 h-9 rounded-xl bg-teal-600 hover:bg-teal-700 text-white flex items-center justify-center transition active:scale-95 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
function chatInput() {
    return {
        preview: null,
        submitting: false,
        onFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => this.preview = ev.target.result;
            reader.readAsDataURL(file);
        },
        clearImage() {
            this.preview = null;
            this.$refs.fileInput.value = '';
        },
        autoGrow(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        }
    }
}

// Scroll to bottom on load
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('msg-list');
    if (list) list.scrollTop = list.scrollHeight;
});
</script>
@endpush
@endsection
