/**
 * ai-chat.js
 * Handles the floating AI chatbot widget.
 * - Toggle panel open/close
 * - Send messages to POST /api/ai/chat
 * - Load chat history on open
 * - Render user/assistant bubbles
 */

document.addEventListener('DOMContentLoaded', () => {
    const toggle     = document.getElementById('ai-toggle');
    const panel      = document.getElementById('ai-panel');
    const iconOpen   = document.getElementById('ai-icon-open');
    const iconClose  = document.getElementById('ai-icon-close');
    const closeBtn   = document.getElementById('ai-close');
    const input      = document.getElementById('ai-input');
    const sendBtn    = document.getElementById('ai-send');
    const messages   = document.getElementById('ai-messages');

    if (!toggle) return; // widget not present on page

    let historyLoaded = false;

    // ── Toggle panel visibility ──
    toggle.addEventListener('click', () => {
        const isOpen = !panel.classList.contains('hidden');
        panel.classList.toggle('hidden', isOpen);
        panel.classList.toggle('flex',   !isOpen);
        iconOpen.classList.toggle('hidden',  !isOpen);
        iconClose.classList.toggle('hidden', isOpen);

        if (!isOpen && !historyLoaded) {
            loadHistory();
        }
    });

    closeBtn?.addEventListener('click', () => {
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
    });

    // ── Send message on button click or Enter key ──
    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        appendBubble('user', text);
        scrollToBottom();

        // Show typing indicator
        const typingId = appendTypingIndicator();

        try {
            const res = await fetch('/api/ai/chat', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await res.json();
            removeTypingIndicator(typingId);
            appendBubble('assistant', data.reply ?? 'Maaf, tidak ada respons.');
        } catch {
            removeTypingIndicator(typingId);
            appendBubble('assistant', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
        }

        scrollToBottom();
    }

    async function loadHistory() {
        historyLoaded = true;
        try {
            const res  = await fetch('/api/ai/history', { headers: { Accept: 'application/json' } });
            const data = await res.json();
            data.messages?.forEach(m => appendBubble(m.role, m.content, false));
            scrollToBottom();
        } catch { /* silent fail */ }
    }

    /**
     * Append a chat bubble to the messages container.
     * @param {'user'|'assistant'} role
     * @param {string} content
     * @param {boolean} animate
     */
    function appendBubble(role, content, animate = true) {
        const isUser = role === 'user';
        const wrap   = document.createElement('div');
        wrap.className = `flex gap-2 ${isUser ? 'justify-end' : ''}${animate ? ' animate-fadeIn' : ''}`;

        if (!isUser) {
            wrap.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center shrink-0 text-teal-600 self-end">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div class="bg-slate-100 rounded-xl rounded-tl-none px-3 py-2 max-w-[85%] text-sm leading-relaxed whitespace-pre-wrap">
                    ${escapeHtml(content)}
                </div>`;
        } else {
            wrap.innerHTML = `
                <div class="bg-teal-600 text-white rounded-xl rounded-tr-none px-3 py-2 max-w-[85%] text-sm leading-relaxed whitespace-pre-wrap">
                    ${escapeHtml(content)}
                </div>`;
        }

        messages.appendChild(wrap);
    }

    function appendTypingIndicator() {
        const id   = `typing-${Date.now()}`;
        const wrap = document.createElement('div');
        wrap.id        = id;
        wrap.className = 'flex gap-2 items-end';
        wrap.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center shrink-0 text-teal-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="bg-slate-100 rounded-xl rounded-tl-none px-3 py-2 text-slate-400 text-xs">
                Sedang mengetik…
            </div>`;
        messages.appendChild(wrap);
        scrollToBottom();
        return id;
    }

    function removeTypingIndicator(id) {
        document.getElementById(id)?.remove();
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g,  '&amp;')
            .replace(/</g,  '&lt;')
            .replace(/>/g,  '&gt;')
            .replace(/"/g,  '&quot;')
            .replace(/'/g,  '&#39;');
    }
});
