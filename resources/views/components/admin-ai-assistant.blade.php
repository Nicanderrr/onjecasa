@php
    $aiArea = request()->routeIs('superadmin.*') ? 'superadmin' : (request()->routeIs('cashier.*') ? 'cashier' : 'admin');
    $aiChatRoute = $aiArea === 'superadmin'
        ? route('superadmin.ai.chat')
        : ($aiArea === 'cashier' ? route('cashier.ai.chat') : route('admin.ai.chat'));
    $aiAlertsRoute = $aiArea === 'superadmin'
        ? route('superadmin.ai.alerts')
        : ($aiArea === 'cashier' ? route('cashier.ai.alerts') : route('admin.ai.alerts'));
    $aiRealtimeRoute = $aiArea === 'superadmin'
        ? route('superadmin.ai.realtime.call')
        : ($aiArea === 'cashier' ? route('cashier.ai.realtime.call') : route('admin.ai.realtime.call'));
    $aiAnalyzeRoute = $aiArea === 'admin' ? route('admin.ai.analyze') : null;
    $aiWorkspace = [
        'admin' => [
            'subtitle' => 'Live system intelligence',
            'hello' => 'Hi, I am here. Ask me about stock, orders, payments, staff, or sales.',
            'placeholder' => 'Ask anything about the system...',
            'shortcuts' => [
                ['label' => 'Today Sales', 'cmd' => "Give me today's sales summary."],
                ['label' => 'Low Stock', 'cmd' => 'List low stock products and what to restock first.'],
                ['label' => 'Next Actions', 'cmd' => 'Show pending operational actions now.'],
            ],
        ],
        'cashier' => [
            'subtitle' => 'Your cashier workspace only',
            'hello' => 'Hi, I am here. Ask me about your sales, receipts, payments, or product lookup.',
            'placeholder' => 'Ask about your cashier activity...',
            'shortcuts' => [
                ['label' => 'My Sales', 'cmd' => "Give me my sales summary for today."],
                ['label' => 'My Receipts', 'cmd' => 'Summarize my latest receipts.'],
                ['label' => 'Low Stock', 'cmd' => 'List low stock products I should watch while selling.'],
            ],
        ],
        'superadmin' => [
            'subtitle' => 'Full system intelligence',
            'hello' => 'Hi, I am here. Ask me about users, sales, payments, stock, audit logs, or system activity.',
            'placeholder' => 'Ask about the whole system...',
            'shortcuts' => [
                ['label' => 'System Pulse', 'cmd' => 'Give me a full system pulse summary.'],
                ['label' => 'User Risk', 'cmd' => 'Summarize user and security issues.'],
                ['label' => 'Audit', 'cmd' => 'Summarize recent audit activity.'],
            ],
        ],
    ][$aiArea];
@endphp

<div id="ai-assistant-wrapper" class="admin-ai-wrap">
    <button id="ai-assistant-toggle" class="admin-ai-fab" type="button" aria-label="Open AI assistant">
        <i class="bi bi-robot"></i>
        <span class="admin-ai-pulse"></span>
        <span id="ai-alert-dot" class="admin-ai-alert-dot d-none"></span>
    </button>

    <section id="ai-chat-window" class="admin-ai-panel d-none" aria-label="AI assistant chat">
        <header class="admin-ai-header">
            <div class="admin-ai-title">
                <span class="admin-ai-icon"><i class="bi bi-stars"></i></span>
                <div>
                    <h6>NewPOS AI</h6>
                    <small>{{ $aiWorkspace['subtitle'] }}</small>
                </div>
            </div>
            <button id="ai-close" class="admin-ai-close" type="button" aria-label="Close AI assistant">
                <i class="bi bi-x-lg"></i>
            </button>
        </header>

        <main id="ai-chat-history" class="admin-ai-history">
            <div class="ai-msg mb-3 d-flex flex-column align-items-start">
                <div class="msg-bubble">
                    {{ $aiWorkspace['hello'] }}
                </div>
                <small class="meta">NEWPOS AI | JUST NOW</small>
            </div>
        </main>

        <footer class="admin-ai-footer">
            <div id="ai-stock-alert" class="admin-ai-stock-alert d-none"></div>
            <div id="ai-voice-status" class="admin-ai-voice-status d-none">Voice assistant is ready.</div>
            <div class="admin-ai-shortcuts">
                @foreach($aiWorkspace['shortcuts'] as $shortcut)
                    <button type="button" class="ai-shortcut" data-cmd="{{ $shortcut['cmd'] }}">{{ $shortcut['label'] }}</button>
                @endforeach
            </div>
            <div id="ai-upload-preview" class="admin-ai-preview d-none">
                <div class="d-flex align-items-center overflow-hidden">
                    <i class="bi bi-paperclip me-2"></i>
                    <span id="ai-file-name" class="small text-truncate"></span>
                </div>
                <button id="ai-remove-file" class="btn btn-link p-0" type="button" aria-label="Remove file">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="admin-ai-input-row {{ $aiAnalyzeRoute ? '' : 'no-file-upload' }}">
                <label for="ai-file-input" class="admin-ai-icon-btn mb-0 {{ $aiAnalyzeRoute ? '' : 'd-none' }}" title="Attach file">
                    <i class="bi bi-paperclip"></i>
                </label>
                <input type="file" id="ai-file-input" class="d-none" accept="image/*,.pdf,.doc,.docx">

                <input type="text" id="ai-input" class="form-control" placeholder="{{ $aiWorkspace['placeholder'] }}" autocomplete="off">

                <button id="ai-voice-btn" class="admin-ai-icon-btn" type="button" title="Voice input">
                    <i class="bi bi-mic"></i>
                </button>
                <button id="ai-send-btn" class="admin-ai-send" type="button" aria-label="Send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </footer>
    </section>
</div>

<style>
.admin-ai-wrap { position: fixed; right: 24px; bottom: 24px; z-index: 9999; font-family: "Segoe UI", Arial, sans-serif; }
.admin-ai-wrap.is-dragging .admin-ai-fab { cursor: grabbing; }
.admin-ai-fab { width: 62px; height: 62px; border: 0; border-radius: 999px; color: #fff; background: linear-gradient(135deg, #2563eb, #0f766e); box-shadow: 0 14px 30px rgba(37, 99, 235, 0.35); position: relative; transition: transform .2s ease, box-shadow .2s ease, opacity .15s ease; cursor: grab; touch-action: none; user-select: none; }
.admin-ai-fab i { font-size: 22px; position: relative; z-index: 2; }
.admin-ai-alert-dot { position:absolute; right:2px; top:2px; width:12px; height:12px; border-radius:999px; background:#ef4444; border:2px solid #fff; z-index:3; }
.admin-ai-fab:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 18px 34px rgba(37, 99, 235, 0.45); }
.admin-ai-pulse { position: absolute; inset: 0; border-radius: 999px; background: rgba(37, 99, 235, 0.25); animation: admin-ai-pulse 2s infinite; }
@keyframes admin-ai-pulse { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(1.5); opacity: 0; } }

.admin-ai-panel {
  position: absolute;
  top: auto;
  right: 0;
  bottom: 86px;
  width: 390px;
  height: min(560px, calc(100vh - 132px));
  max-height: calc(100vh - 132px);
  background: var(--admin-surface);
  border: 1px solid #dbe4ef;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 24px 50px rgba(15, 23, 42, .2);
  display: flex;
  flex-direction: column;
}
.admin-ai-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 14px; background: var(--admin-surface-soft); border-bottom: 1px solid #dbe4ef; }
.admin-ai-title { display: flex; align-items: center; gap: 10px; color: #0f172a; }
.admin-ai-title h6 { margin: 0; font-weight: 800; }
.admin-ai-title small { color: #2563eb; }
.admin-ai-icon { width: 34px; height: 34px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: #dbeafe; color: #1d4ed8; }
.admin-ai-close { border: 0; background: transparent; color: #64748b; width: 34px; height: 34px; border-radius: 8px; }
.admin-ai-close:hover { color: #0f172a; background: rgba(15,23,42,.06); }

.admin-ai-history {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 14px;
  background: var(--admin-surface);
  scrollbar-width: thin;
}
.admin-ai-history .msg-bubble { max-width: 86%; padding: 10px 12px; border-radius: 14px; border: 1px solid #dbe4ef; color: #0f172a; line-height: 1.38; font-size: 13px; background: var(--admin-surface); }
.ai-msg .msg-bubble { border-top-left-radius: 4px; }
.user-msg .msg-bubble { background: linear-gradient(135deg, #2563eb, #0f766e); border-color: transparent; color: #fff; border-top-right-radius: 4px; }
.admin-ai-history .meta { color: #64748b; margin-top: 4px; font-size: 10px; }

.admin-ai-footer { padding: 12px; border-top: 1px solid #dbe4ef; background: var(--admin-surface); }
.admin-ai-stock-alert { font-size: 12px; color: #991b1b; background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; }
.admin-ai-voice-status { font-size: 12px; color: #075985; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; }
.admin-ai-voice-status.listening { color: #991b1b; background: #fee2e2; border-color: #fecaca; }
.admin-ai-voice-status.speaking { color: #065f46; background: #d1fae5; border-color: #a7f3d0; }
.admin-ai-shortcuts { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px; }
.ai-shortcut { border:1px solid #dbe4ef; background:#f8fbff; color:#0f172a; border-radius:999px; font-size:11px; padding:4px 8px; }
.ai-shortcut:hover { background:#eff6ff; }
.admin-ai-preview { display: flex; align-items: center; justify-content: space-between; color: #0f172a; background: var(--admin-surface-soft); border: 1px solid #dbe4ef; border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; }
.admin-ai-preview button { color: #dc2626; }
.admin-ai-input-row { display: grid; grid-template-columns: 32px 1fr 32px 36px; gap: 8px; align-items: center; }
.admin-ai-input-row.no-file-upload { grid-template-columns: 1fr 32px 36px; }
.admin-ai-input-row input.form-control { height: 38px; border-radius: 10px; border: 1px solid #dbe4ef; background: var(--admin-surface-soft); color: #0f172a; font-size: 13px; }
.admin-ai-input-row input.form-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 .18rem rgba(37,99,235,.15); }
.admin-ai-icon-btn { border: 0; width: 32px; height: 32px; border-radius: 8px; background: transparent; color: #64748b; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
.admin-ai-icon-btn:hover { color: #0f172a; background: rgba(15,23,42,.06); }
.admin-ai-send { border: 0; width: 36px; height: 36px; border-radius: 999px; background: linear-gradient(135deg, #2563eb, #0f766e); color: #fff; }
.ai-typing-indicator span { height: 6px; width: 6px; background: #2563eb; display: inline-block; border-radius: 50%; animation: ai-bounce 1.3s infinite ease-in-out; margin: 0 2px; }
.ai-typing-indicator span:nth-child(2) { animation-delay: .2s; }
.ai-typing-indicator span:nth-child(3) { animation-delay: .4s; }
@keyframes ai-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

@media (max-width: 768px) {
  .admin-ai-wrap { right: 16px; bottom: 16px; }
  .admin-ai-fab { width: 56px; height: 56px; }
  .admin-ai-panel { top: auto; width: min(94vw, 360px); height: min(72vh, calc(100vh - 104px)); max-height: calc(100vh - 104px); bottom: 66px; border-radius: 14px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const aiChatRoute = @json($aiChatRoute);
    const aiAlertsRoute = @json($aiAlertsRoute);
    const aiRealtimeRoute = @json($aiRealtimeRoute);
    const aiAnalyzeRoute = @json($aiAnalyzeRoute);
    const aiPlaceholder = @json($aiWorkspace['placeholder']);
    const toggle = document.getElementById('ai-assistant-toggle');
    const windowEl = document.getElementById('ai-chat-window');
    const closeBtn = document.getElementById('ai-close');
    const input = document.getElementById('ai-input');
    const sendBtn = document.getElementById('ai-send-btn');
    const historyEl = document.getElementById('ai-chat-history');
    const voiceBtn = document.getElementById('ai-voice-btn');
    const voiceStatus = document.getElementById('ai-voice-status');
    const fileInput = document.getElementById('ai-file-input');
    const uploadPreview = document.getElementById('ai-upload-preview');
    const fileNameDisplay = document.getElementById('ai-file-name');
    const removeFileBtn = document.getElementById('ai-remove-file');
    const stockAlert = document.getElementById('ai-stock-alert');
    const alertDot = document.getElementById('ai-alert-dot');
    const wrapper = document.getElementById('ai-assistant-wrapper');
    if (!toggle || !windowEl || !input || !sendBtn || !historyEl || !wrapper) return;

    let chatHistory = [];
    let isListening = false;
    let voiceConversationActive = false;
    let realtimeState = {
        pc: null,
        dc: null,
        stream: null,
        audio: null,
        connecting: false,
        connected: false,
        responseText: ''
    };
    let selectedFile = null;
    let lowStockAlertShown = false;
    let dragState = {
        active: false,
        moved: false,
        startX: 0,
        startY: 0,
        pointerId: null,
        rect: null
    };
    const positionStorageKey = 'adminHMD.aiAssistantPosition';

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function getSavedPosition() {
        try {
            const raw = window.localStorage.getItem(positionStorageKey);
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            if (typeof parsed.left !== 'number' || typeof parsed.top !== 'number') return null;
            return parsed;
        } catch (_) {
            return null;
        }
    }

    function savePosition(left, top) {
        try {
            window.localStorage.setItem(positionStorageKey, JSON.stringify({ left, top }));
        } catch (_) {}
    }

    function setWrapperPosition(left, top) {
        wrapper.style.left = left + 'px';
        wrapper.style.top = top + 'px';
        wrapper.style.right = 'auto';
        wrapper.style.bottom = 'auto';
        savePosition(left, top);
    }

    function applyInitialPosition() {
        const saved = getSavedPosition();
        if (saved) {
            const maxLeft = Math.max(0, window.innerWidth - wrapper.offsetWidth);
            const maxTop = Math.max(0, window.innerHeight - wrapper.offsetHeight);
            setWrapperPosition(clamp(saved.left, 0, maxLeft), clamp(saved.top, 0, maxTop));
            return;
        }

        const maxLeft = Math.max(0, window.innerWidth - wrapper.offsetWidth);
        const maxTop = Math.max(0, window.innerHeight - wrapper.offsetHeight);
        const initialLeft = Math.max(0, maxLeft - 24);
        const initialTop = Math.max(0, maxTop - 24);
        setWrapperPosition(initialLeft, initialTop);
    }

    function keepInViewport() {
        const rect = wrapper.getBoundingClientRect();
        const maxLeft = Math.max(0, window.innerWidth - rect.width);
        const maxTop = Math.max(0, window.innerHeight - rect.height);
        const left = clamp(rect.left, 0, maxLeft);
        const top = clamp(rect.top, 0, maxTop);
        setWrapperPosition(left, top);
    }

    applyInitialPosition();
    window.addEventListener('resize', keepInViewport);

    toggle.addEventListener('click', () => {
        if (dragState.moved) {
            dragState.moved = false;
            return;
        }
        windowEl.classList.toggle('d-none');
        if (!windowEl.classList.contains('d-none')) {
            input.focus();
            historyEl.scrollTop = historyEl.scrollHeight;
        }
    });
    closeBtn.addEventListener('click', () => {
        stopVoiceConversation();
        windowEl.classList.add('d-none');
    });

    toggle.addEventListener('pointerdown', function(event) {
        dragState.active = true;
        dragState.moved = false;
        dragState.pointerId = event.pointerId;
        dragState.rect = wrapper.getBoundingClientRect();
        dragState.startX = event.clientX;
        dragState.startY = event.clientY;
        wrapper.classList.add('is-dragging');
        toggle.setPointerCapture?.(event.pointerId);
    });

    window.addEventListener('pointermove', function(event) {
        if (!dragState.active || dragState.pointerId !== event.pointerId) return;

        const deltaX = event.clientX - dragState.startX;
        const deltaY = event.clientY - dragState.startY;

        if (Math.abs(deltaX) > 4 || Math.abs(deltaY) > 4) {
            dragState.moved = true;
        }

        const rect = dragState.rect || wrapper.getBoundingClientRect();
        const maxLeft = Math.max(0, window.innerWidth - rect.width);
        const maxTop = Math.max(0, window.innerHeight - rect.height);
        const left = clamp(rect.left + deltaX, 0, maxLeft);
        const top = clamp(rect.top + deltaY, 0, maxTop);
        wrapper.style.left = left + 'px';
        wrapper.style.top = top + 'px';
        wrapper.style.right = 'auto';
        wrapper.style.bottom = 'auto';
    });

    window.addEventListener('pointerup', function(event) {
        if (dragState.pointerId !== event.pointerId) return;

        if (dragState.active) {
            const rect = wrapper.getBoundingClientRect();
            savePosition(rect.left, rect.top);
        }

        dragState.active = false;
        dragState.pointerId = null;
        dragState.rect = null;
        wrapper.classList.remove('is-dragging');

        window.setTimeout(function() {
            dragState.moved = false;
        }, 0);
    });

    window.addEventListener('pointercancel', function(event) {
        if (dragState.pointerId !== event.pointerId) return;
        dragState.active = false;
        dragState.pointerId = null;
        dragState.rect = null;
        wrapper.classList.remove('is-dragging');
        dragState.moved = false;
    });

    function setVoiceStatus(message, state) {
        if (!voiceStatus) return;
        voiceStatus.textContent = message;
        voiceStatus.classList.toggle('d-none', !voiceConversationActive && !message);
        voiceStatus.classList.toggle('listening', state === 'listening');
        voiceStatus.classList.toggle('speaking', state === 'speaking');
    }

    function setVoiceButton(active) {
        if (!voiceBtn) return;
        voiceBtn.classList.toggle('text-danger', active);
        voiceBtn.title = active ? 'Turn voice chat off' : 'Start voice chat';
        voiceBtn.innerHTML = active ? '<i class="bi bi-mic-mute"></i>' : '<i class="bi bi-mic"></i>';
    }

    async function sendMessage(messageText = null, options = {}) {
        const text = (messageText ?? input.value).trim();
        if (!text && !selectedFile) return;
        if (text) {
            appendMessage('user', text);
            if (messageText === null) input.value = '';
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
                if (!aiAnalyzeRoute) {
                    throw new Error('File analysis is only available in the admin workspace.');
                }
                response = await fetch(aiAnalyzeRoute, { method: 'POST', body: formData });
                removeFile();
            } else {
                response = await fetch(aiChatRoute, {
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
                speak(data.reply, options.afterSpeak);
            } else {
                const errorText = data.error || data.message || ('HTTP ' + response.status);
                appendMessage('ai', 'SYSTEM ERROR: ' + errorText);
                if (typeof options.afterSpeak === 'function') options.afterSpeak();
            }
        } catch (error) {
            removeTypingIndicator(typingId);
            appendMessage('ai', 'CONNECTION ERROR: ' + (error && error.message ? error.message : 'Unable to reach AI service.'));
            if (typeof options.afterSpeak === 'function') options.afterSpeak();
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
        input.placeholder = aiPlaceholder;
    }
    removeFileBtn.addEventListener('click', removeFile);

    if (voiceBtn && navigator.mediaDevices && window.RTCPeerConnection) {
        voiceBtn.addEventListener('click', function() {
            if (voiceConversationActive) {
                stopVoiceConversation();
                return;
            }

            startVoiceConversation();
        });
    } else {
        if (voiceBtn) voiceBtn.style.display = 'none';
    }

    function startVoiceConversation() {
        voiceConversationActive = true;
        windowEl.classList.remove('d-none');
        setVoiceButton(true);
        connectRealtime().catch(function(error) {
            appendMessage('ai', error.message || 'Voice assistant could not connect.');
            stopVoiceConversation();
        });
    }

    function stopVoiceConversation() {
        voiceConversationActive = false;
        disconnectRealtime();
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        setVoiceButton(false);
        setVoiceStatus('', '');
    }

    function disconnectRealtime() {
        try { realtimeState.dc?.send(JSON.stringify({ type: 'response.cancel' })); } catch (_) {}
        try { realtimeState.dc?.close(); } catch (_) {}
        try { realtimeState.pc?.close(); } catch (_) {}
        try { realtimeState.stream?.getTracks().forEach(track => track.stop()); } catch (_) {}
        realtimeState.pc = null;
        realtimeState.dc = null;
        realtimeState.stream = null;
        realtimeState.connected = false;
        realtimeState.connecting = false;
        realtimeState.responseText = '';
    }

    async function connectRealtime() {
        if (realtimeState.connected || realtimeState.connecting) return;
        realtimeState.connecting = true;
        setVoiceStatus('Requesting microphone...', '');

        const pc = new RTCPeerConnection();
        const audio = realtimeState.audio || document.createElement('audio');
        realtimeState.pc = pc;
        realtimeState.audio = audio;
        audio.autoplay = true;
        audio.playsInline = true;
        audio.muted = false;
        audio.volume = 1;
        if (!audio.isConnected) {
            audio.hidden = true;
            document.body.appendChild(audio);
        }

        pc.ontrack = function(event) {
            const outputStream = event.streams[0] || new MediaStream([event.track]);
            audio.srcObject = outputStream;
            audio.play().catch(function() {
                setVoiceStatus('Tap the mic to enable sound.', '');
            });
        };

        const stream = await navigator.mediaDevices.getUserMedia({
            audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
        });
        stream.getAudioTracks().forEach(track => pc.addTrack(track, stream));

        const dc = pc.createDataChannel('oai-events');
        realtimeState.dc = dc;
        dc.onmessage = function(message) {
            try { handleRealtimeEvent(JSON.parse(message.data)); } catch (_) {}
        };
        dc.onerror = function() {
            if (voiceConversationActive) setVoiceStatus('Voice connection needs attention. Tap the mic to reconnect.', '');
        };
        dc.onopen = function() {
            realtimeState.connected = true;
            realtimeState.connecting = false;
            setVoiceStatus('Online. Speak naturally.', 'listening');
            sendRealtime({
                type: 'response.create',
                response: {
                    instructions: 'Say hi in one short sentence, then ask what you can help with. Keep it natural and brief.'
                }
            });
        };
        dc.onclose = function() {
            realtimeState.connected = false;
            if (voiceConversationActive) setVoiceStatus('Voice disconnected. Tap the mic to reconnect.', '');
        };
        pc.onconnectionstatechange = function() {
            if (['failed', 'disconnected', 'closed'].includes(pc.connectionState) && voiceConversationActive) {
                setVoiceStatus('Voice connection dropped. Tap the mic to reconnect.', '');
            }
        };

        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);

        const response = await fetch(aiRealtimeRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/sdp',
                'Accept': 'application/sdp',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-NewPOS-Page': window.location.pathname
            },
            body: offer.sdp
        });

        if (!response.ok) {
            throw new Error(await response.text() || ('Realtime failed with HTTP ' + response.status));
        }

        await pc.setRemoteDescription({ type: 'answer', sdp: await response.text() });
        realtimeState.stream = stream;
    }

    function sendRealtime(event) {
        if (!realtimeState.dc || realtimeState.dc.readyState !== 'open') return;
        realtimeState.dc.send(JSON.stringify(event));
    }

    function handleRealtimeEvent(event) {
        if (event.type === 'input_audio_buffer.speech_started') {
            setVoiceStatus('Listening...', 'listening');
            return;
        }

        if (event.type === 'input_audio_buffer.speech_stopped') {
            setVoiceStatus('Understanding...', '');
            return;
        }

        if (event.type === 'conversation.item.input_audio_transcription.completed' && event.transcript) {
            appendMessage('user', event.transcript);
            chatHistory.push({ role: 'user', content: event.transcript });
            if (chatHistory.length > 14) chatHistory.splice(0, 2);
            return;
        }

        if (event.type === 'response.audio.delta' || event.type === 'response.output_audio.delta') {
            setVoiceStatus('Speaking...', 'speaking');
            return;
        }

        if (['response.audio_transcript.delta', 'response.output_audio_transcript.delta', 'response.output_text.delta'].includes(event.type)) {
            realtimeState.responseText += event.delta || '';
            return;
        }

        if (['response.audio_transcript.done', 'response.output_audio_transcript.done', 'response.output_text.done'].includes(event.type)) {
            const text = event.transcript || event.text || realtimeState.responseText;
            if (text) {
                appendMessage('ai', text);
                chatHistory.push({ role: 'assistant', content: text });
                if (chatHistory.length > 14) chatHistory.splice(0, 2);
            }
            realtimeState.responseText = '';
            return;
        }

        if (event.type === 'response.done') {
            setVoiceStatus('Online. Speak naturally.', 'listening');
            realtimeState.responseText = '';
            return;
        }

        if (event.type === 'error') {
            setVoiceStatus(event.error?.message || 'Voice assistant needs attention.', '');
        }
    }

    function speak(text, onDone) {
        if (!('speechSynthesis' in window)) {
            if (typeof onDone === 'function') onDone();
            return;
        }
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 1.02;
        utterance.pitch = 1.0;
        const voices = window.speechSynthesis.getVoices();
        const voice = voices.find(v => v.name.includes('Google UK English')) || voices.find(v => v.lang && v.lang.startsWith('en')) || voices[0];
        if (voice) utterance.voice = voice;
        utterance.onstart = function() {
            if (voiceConversationActive) setVoiceStatus('Speaking...', 'speaking');
        };
        utterance.onend = function() {
            if (typeof onDone === 'function') onDone();
        };
        utterance.onerror = function() {
            if (typeof onDone === 'function') onDone();
        };
        window.speechSynthesis.speak(utterance);
    }

    function appendMessage(role, text) {
        const div = document.createElement('div');
        div.className = role + '-msg mb-3 d-flex flex-column ' + (role === 'user' ? 'align-items-end' : 'align-items-start');
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const label = role === 'user' ? 'YOU' : 'NEWPOS AI';
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
            const response = await fetch(aiAlertsRoute, { headers: { 'Accept': 'application/json' } });
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
