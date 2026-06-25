<div id="ai-assistant-wrapper" class="pos-ai-wrap">
    <button id="ai-assistant-toggle" class="pos-ai-fab" type="button" aria-label="Open AI assistant">
        <i class="fas fa-robot"></i>
        <span class="pos-ai-pulse"></span>
        <span id="ai-alert-dot" class="pos-ai-alert-dot d-none"></span>
    </button>

    <section id="ai-chat-window" class="pos-ai-panel d-none" aria-label="POS AI assistant chat">
        <header class="pos-ai-header">
            <div class="pos-ai-title">
                <span class="pos-ai-icon"><i class="fas fa-brain"></i></span>
                <div>
                    <h6>POS Command AI</h6>
                    <small>Live System Intel</small>
                </div>
            </div>
            <button id="ai-close" class="pos-ai-close" type="button" aria-label="Close AI assistant">
                <i class="fas fa-times"></i>
            </button>
        </header>

        <main id="ai-chat-history" class="pos-ai-history">
            <div class="ai-msg mb-3 d-flex flex-column align-items-start">
                <div class="msg-bubble">
                    Hello. I can monitor stock, orders, payments, staff, and sales in real time.
                </div>
                <small class="meta">POS AI | JUST NOW</small>
            </div>
        </main>

        <footer class="pos-ai-footer">
            <div id="ai-stock-alert" class="pos-ai-stock-alert d-none"></div>
            <div class="pos-ai-shortcuts">
                <button type="button" class="ai-shortcut" data-cmd="Give me today's sales summary.">Today Sales</button>
                <button type="button" class="ai-shortcut" data-cmd="List low stock products and what to restock first.">Low Stock</button>
                <button type="button" class="ai-shortcut" data-cmd="Show pending operational actions now.">Next Actions</button>
            </div>
            <div id="ai-upload-preview" class="pos-ai-preview d-none">
                <div class="d-flex align-items-center overflow-hidden">
                    <i class="fas fa-file mr-2"></i>
                    <span id="ai-file-name" class="small text-truncate"></span>
                </div>
                <button id="ai-remove-file" class="btn btn-link p-0" type="button" aria-label="Remove file">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <div class="pos-ai-input-row">
                <label for="ai-file-input" class="pos-ai-icon-btn mb-0" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </label>
                <input type="file" id="ai-file-input" class="d-none" accept="image/*,.pdf,.doc,.docx">

                <input type="text" id="ai-input" class="form-control" placeholder="Ask anything about the system..." autocomplete="off">

                <button id="ai-voice-btn" class="pos-ai-icon-btn" type="button" title="Voice input">
                    <i class="fas fa-microphone"></i>
                </button>
                <button id="ai-send-btn" class="pos-ai-send" type="button" aria-label="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </footer>
    </section>
</div>

<style>
.pos-ai-wrap { position: fixed; right: 24px; bottom: 24px; z-index: 9999; font-family: "Open Sans", sans-serif; }
.pos-ai-fab { width: 62px; height: 62px; border: 0; border-radius: 999px; color: #fff; background: linear-gradient(135deg, #16a34a, #166534); box-shadow: 0 14px 30px rgba(22, 101, 52, 0.45); position: relative; transition: transform .2s ease, box-shadow .2s ease; }
.pos-ai-fab i { font-size: 22px; position: relative; z-index: 2; }
.pos-ai-alert-dot { position:absolute; right:2px; top:2px; width:12px; height:12px; border-radius:999px; background:#ef4444; border:2px solid #fff; z-index:3; }
.pos-ai-fab:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 18px 34px rgba(22, 101, 52, 0.55); }
.pos-ai-pulse { position: absolute; inset: 0; border-radius: 999px; background: rgba(22, 163, 74, 0.32); animation: pos-ai-pulse 2s infinite; }
@keyframes pos-ai-pulse { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(1.5); opacity: 0; } }

.pos-ai-panel { position: absolute; right: 0; bottom: 78px; width: 390px; height: 620px; background: rgba(8, 15, 30, 0.97); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; overflow: hidden; box-shadow: 0 24px 50px rgba(0, 0, 0, .45); display: flex; flex-direction: column; }
.pos-ai-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 14px; background: rgba(255,255,255,.04); border-bottom: 1px solid rgba(255,255,255,.1); }
.pos-ai-title { display: flex; align-items: center; gap: 10px; color: #fff; }
.pos-ai-title h6 { margin: 0; font-weight: 700; }
.pos-ai-title small { color: #4ade80; }
.pos-ai-icon { width: 34px; height: 34px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: rgba(34,197,94,.2); color: #86efac; }
.pos-ai-close { border: 0; background: transparent; color: #9ca3af; width: 34px; height: 34px; border-radius: 8px; }
.pos-ai-close:hover { color: #fff; background: rgba(255,255,255,.08); }

.pos-ai-history { flex: 1; overflow: auto; padding: 14px; }
.pos-ai-history .msg-bubble { max-width: 86%; padding: 10px 12px; border-radius: 14px; border: 1px solid rgba(255,255,255,.12); color: #fff; line-height: 1.38; font-size: 13px; }
.ai-msg .msg-bubble { background: rgba(255,255,255,.08); border-top-left-radius: 4px; }
.user-msg .msg-bubble { background: linear-gradient(135deg, #16a34a, #166534); border-top-right-radius: 4px; }
.pos-ai-history .meta { color: #9ca3af; margin-top: 4px; font-size: 10px; }

.pos-ai-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,.1); background: #0b1325; }
.pos-ai-stock-alert { font-size: 12px; color: #fecaca; background: rgba(127, 29, 29, 0.35); border: 1px solid rgba(239, 68, 68, 0.45); border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; }
.pos-ai-shortcuts { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px; }
.ai-shortcut { border:1px solid rgba(255,255,255,.16); background:rgba(255,255,255,.06); color:#e5e7eb; border-radius:999px; font-size:11px; padding:4px 8px; }
.ai-shortcut:hover { background:rgba(255,255,255,.12); }
.pos-ai-preview { display: flex; align-items: center; justify-content: space-between; color: #e5e7eb; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.14); border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; }
.pos-ai-preview button { color: #f87171; }
.pos-ai-input-row { display: grid; grid-template-columns: 32px 1fr 32px 36px; gap: 8px; align-items: center; }
.pos-ai-input-row input.form-control { height: 38px; border-radius: 10px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.03); color: #fff; font-size: 13px; }
.pos-ai-input-row input.form-control:focus { border-color: #22c55e; box-shadow: 0 0 0 .18rem rgba(34,197,94,.2); }
.pos-ai-icon-btn { border: 0; width: 32px; height: 32px; border-radius: 8px; background: transparent; color: #9ca3af; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
.pos-ai-icon-btn:hover { color: #fff; background: rgba(255,255,255,.08); }
.pos-ai-send { border: 0; width: 36px; height: 36px; border-radius: 999px; background: linear-gradient(135deg, #16a34a, #166534); color: #fff; }

.ai-typing-indicator span { height: 6px; width: 6px; background: #4ade80; display: inline-block; border-radius: 50%; animation: ai-bounce 1.3s infinite ease-in-out; margin: 0 2px; }
.ai-typing-indicator span:nth-child(2) { animation-delay: .2s; }
.ai-typing-indicator span:nth-child(3) { animation-delay: .4s; }
@keyframes ai-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

@media (max-width: 768px) {
  .pos-ai-wrap { right: 16px; bottom: 16px; }
  .pos-ai-fab { width: 56px; height: 56px; }
  .pos-ai-panel { width: min(94vw, 360px); height: min(78vh, 560px); bottom: 70px; border-radius: 14px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('ai-assistant-toggle');
    const windowEl = document.getElementById('ai-chat-window');
    const closeBtn = document.getElementById('ai-close');
    const input = document.getElementById('ai-input');
    const sendBtn = document.getElementById('ai-send-btn');
    const historyEl = document.getElementById('ai-chat-history');
    const voiceBtn = document.getElementById('ai-voice-btn');
    const fileInput = document.getElementById('ai-file-input');
    const uploadPreview = document.getElementById('ai-upload-preview');
    const fileNameDisplay = document.getElementById('ai-file-name');
    const removeFileBtn = document.getElementById('ai-remove-file');
    const stockAlert = document.getElementById('ai-stock-alert');
    const alertDot = document.getElementById('ai-alert-dot');
    if (!toggle || !windowEl || !input || !sendBtn || !historyEl) return;

    let chatHistory = [];
    let isListening = false;
    let selectedFile = null;
    let lowStockAlertShown = false;

    toggle.addEventListener('click', () => {
        windowEl.classList.toggle('d-none');
        if (!windowEl.classList.contains('d-none')) {
            input.focus();
            historyEl.scrollTop = historyEl.scrollHeight;
        }
    });
    closeBtn.addEventListener('click', () => windowEl.classList.add('d-none'));

    async function sendMessage() {
        const text = input.value.trim();
        if (!text && !selectedFile) return;
        if (text) {
            appendMessage('user', text);
            input.value = '';
        } else if (selectedFile) {
            appendMessage('user', 'Sent a file: ' + selectedFile.name);
        }
        const typingId = showTypingIndicator();

        try {
            let response;
            if (selectedFile) {
                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('prompt', text || 'Analyze this file.');
                formData.append('_token', '{{ csrf_token() }}');
                response = await fetch('{{ route("admin.ai.analyze") }}', { method: 'POST', body: formData });
                removeFile();
            } else {
                response = await fetch('{{ route("admin.ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text, history: chatHistory })
                });
            }

            const raw = await response.text();
            let data = {};
            try { data = JSON.parse(raw); } catch (_) { data = { error: raw || ('HTTP ' + response.status) }; }
            removeTypingIndicator(typingId);

            if (response.ok && data.reply) {
                appendMessage('ai', data.reply);
                if (text) chatHistory.push({ role: 'user', content: text });
                chatHistory.push({ role: 'assistant', content: data.reply });
                if (chatHistory.length > 14) chatHistory.splice(0, 2);
                speak(data.reply);
            } else {
                const errorText = data.error || data.message || ('HTTP ' + response.status);
                appendMessage('ai', 'SYSTEM ERROR: ' + errorText);
            }
        } catch (error) {
            removeTypingIndicator(typingId);
            appendMessage('ai', 'CONNECTION ERROR: ' + (error && error.message ? error.message : 'Unable to reach AI service.'));
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', function(e) { if (e.key === 'Enter') sendMessage(); });
    document.querySelectorAll('.ai-shortcut').forEach((btn) => {
        btn.addEventListener('click', function() {
            input.value = this.dataset.cmd || '';
            sendMessage();
        });
    });

    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            selectedFile = e.target.files[0];
            fileNameDisplay.textContent = selectedFile.name;
            uploadPreview.classList.remove('d-none');
            input.placeholder = 'Add a question about this file...';
        }
    });

    function removeFile() {
        selectedFile = null;
        fileInput.value = '';
        uploadPreview.classList.add('d-none');
        input.placeholder = 'Ask anything about the system...';
    }
    removeFileBtn.addEventListener('click', removeFile);

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new Recognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
        recognition.onstart = function() {
            isListening = true;
            voiceBtn.classList.add('text-danger');
            voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        };
        recognition.onresult = function(event) {
            input.value = event.results[0][0].transcript;
            sendMessage();
        };
        recognition.onend = function() {
            isListening = false;
            voiceBtn.classList.remove('text-danger');
            voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
        };
        recognition.onerror = recognition.onend;
        voiceBtn.addEventListener('click', function() {
            if (isListening) recognition.stop();
            else {
                if ('speechSynthesis' in window) window.speechSynthesis.cancel();
                recognition.start();
            }
        });
    } else {
        voiceBtn.style.display = 'none';
    }

    function speak(text) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 1.0;
        utterance.pitch = 0.95;
        const voices = window.speechSynthesis.getVoices();
        const voice = voices.find(v => v.name.includes('Google UK English Male')) || voices.find(v => v.name.includes('Male')) || voices[0];
        if (voice) utterance.voice = voice;
        window.speechSynthesis.speak(utterance);
    }

    function appendMessage(role, text) {
        const div = document.createElement('div');
        div.className = role + '-msg mb-3 d-flex flex-column ' + (role === 'user' ? 'align-items-end' : 'align-items-start');
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const label = role === 'user' ? 'YOU' : 'POS AI';
        div.innerHTML = '<div class="msg-bubble">' + String(text).replace(/\n/g, '<br>') + '</div><small class="meta">' + label + ' | ' + timestamp + '</small>';
        historyEl.appendChild(div);
        historyEl.scrollTop = historyEl.scrollHeight;
    }

    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'ai-msg mb-3 d-flex flex-column align-items-start';
        div.innerHTML = '<div class="msg-bubble ai-typing-indicator"><span></span><span></span><span></span></div>';
        historyEl.appendChild(div);
        historyEl.scrollTop = historyEl.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    async function checkLowStockAlerts() {
        try {
            const response = await fetch('{{ route("admin.ai.alerts") }}', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            if (data.has_alert) {
                const topItems = (data.items || []).slice(0, 3).map(function(i) { return i.name + ' (' + i.stock + ')'; }).join(', ');
                stockAlert.textContent = data.message + (topItems ? ' Priority: ' + topItems + '.' : '');
                stockAlert.classList.remove('d-none');
                alertDot.classList.remove('d-none');

                if (!lowStockAlertShown) {
                    appendMessage('ai', 'Stock alert. ' + data.message + ' Please restock soon.');
                    lowStockAlertShown = true;
                }
            } else {
                stockAlert.classList.add('d-none');
                alertDot.classList.add('d-none');
            }
        } catch (_) {}
    }

    checkLowStockAlerts();
    setInterval(checkLowStockAlerts, 60000);
});
</script>
