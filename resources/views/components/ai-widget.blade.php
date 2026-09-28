{{-- AI Chat Widget — floating bottom-right corner --}}
<div id="ai-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">

    {{-- Chat panel --}}
    <div id="ai-panel"
         class="hidden flex-col w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden"
         style="max-height: 480px;">

        {{-- Header --}}
        <div class="bg-teal-600 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <p class="font-semibold text-sm">AlkesLink Assistant</p>
            </div>
            <button id="ai-close" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div id="ai-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-sm" style="max-height:320px;">
            {{-- Greeting bubble --}}
            <div class="flex gap-2">
                <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center shrink-0 text-teal-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div class="bg-slate-100 rounded-xl rounded-tl-none px-3 py-2 max-w-[85%] leading-relaxed">
                    Halo! Saya asisten AlkesLink. Ada yang bisa saya bantu mengenai donasi atau pencarian alat kesehatan? 😊
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-slate-100 p-3 flex gap-2">
            <input id="ai-input" type="text" placeholder="Ketik pertanyaan Anda..."
                   class="flex-1 text-sm px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition" />
            <button id="ai-send"
                    class="px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white transition shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Toggle button --}}
    <button id="ai-toggle"
            class="w-14 h-14 rounded-full bg-teal-600 hover:bg-teal-700 shadow-lg text-white
                   flex items-center justify-center transition-all duration-200 active:scale-95">
        <svg id="ai-icon-open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <svg id="ai-icon-close" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
