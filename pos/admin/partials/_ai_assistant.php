<div id="pos-ai-assistant" class="pos-ai-assistant" data-chat-endpoint="ai_chat.php" data-analyze-endpoint="ai_analyze.php">
  <button type="button" id="pos-ai-toggle" class="pos-ai-toggle" aria-label="Open Command Center AI">
    <i class="fas fa-brain"></i>
    <span></span>
  </button>

  <section id="pos-ai-window" class="pos-ai-window" aria-live="polite">
    <header class="pos-ai-header">
      <div class="pos-ai-title">
        <div class="pos-ai-mark"><i class="fas fa-microchip"></i></div>
        <div>
          <h3>Command Center AI</h3>
          <p><span class="pos-ai-status-dot"></span>POS operations intelligence</p>
        </div>
      </div>
      <button type="button" id="pos-ai-close" class="pos-ai-icon-btn" aria-label="Close assistant">
        <i class="fas fa-times"></i>
      </button>
    </header>

    <div class="pos-ai-quick">
      <button type="button" data-prompt="What's happening in the POS right now?">System update</button>
      <button type="button" data-prompt="Which products need my attention today?">Product risks</button>
      <button type="button" data-prompt="Summarize today's sales and recent payments.">Sales brief</button>
    </div>

    <div id="pos-ai-history" class="pos-ai-history">
      <article class="pos-ai-message pos-ai-message-bot">
        <div class="pos-ai-bubble">
          I am online. Ask for a sales brief, inventory risks, popular products, unpaid orders, or upload a product/receipt image for analysis.
        </div>
        <small>COMMAND AI - JUST NOW</small>
      </article>
    </div>

    <div id="pos-ai-upload-preview" class="pos-ai-upload-preview d-none">
      <div>
        <i class="fas fa-paperclip"></i>
        <span id="pos-ai-file-name"></span>
      </div>
      <button type="button" id="pos-ai-remove-file" aria-label="Remove selected file"><i class="fas fa-trash"></i></button>
    </div>

    <form id="pos-ai-form" class="pos-ai-form">
      <label class="pos-ai-attach" for="pos-ai-file">
        <i class="fas fa-paperclip"></i>
      </label>
      <input type="file" id="pos-ai-file" class="d-none" accept="image/*,.txt,.csv,.json,.log,.md,.pdf,.doc,.docx">
      <input type="text" id="pos-ai-input" placeholder="Ask Command Center AI..." autocomplete="off">
      <button type="button" id="pos-ai-voice" class="pos-ai-icon-btn" aria-label="Use voice input">
        <i class="fas fa-microphone"></i>
      </button>
      <button type="submit" class="pos-ai-send" aria-label="Send message">
        <i class="fas fa-paper-plane"></i>
      </button>
    </form>
  </section>
</div>

<style>
  .pos-ai-assistant {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 9999;
    font-family: Outfit, "Open Sans", sans-serif;
  }

  .pos-ai-toggle {
    position: relative;
    display: grid;
    width: 64px;
    height: 64px;
    place-items: center;
    border: 0;
    border-radius: 20px;
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #0f766e);
    box-shadow: 0 18px 38px rgba(37, 99, 235, 0.34);
    cursor: pointer;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }

  .pos-ai-toggle:hover {
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 24px 48px rgba(15, 118, 110, 0.36);
  }

  .pos-ai-toggle i {
    position: relative;
    z-index: 1;
    font-size: 1.5rem;
  }

  .pos-ai-toggle span {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: rgba(37, 99, 235, 0.34);
    animation: pos-ai-pulse 2s ease-out infinite;
  }

  .pos-ai-window {
    position: absolute;
    right: 0;
    bottom: 82px;
    display: none;
    width: min(430px, calc(100vw - 32px));
    height: min(650px, calc(100vh - 120px));
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.18);
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.96);
    box-shadow: 0 28px 70px rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(18px);
  }

  .pos-ai-assistant.is-open .pos-ai-window {
    display: grid;
    grid-template-rows: auto auto 1fr auto auto;
    animation: pos-ai-rise 0.2s ease-out;
  }

  .pos-ai-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(226, 232, 240, 0.12);
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(20, 184, 166, 0.12));
  }

  .pos-ai-title {
    display: flex;
    gap: 0.8rem;
    align-items: center;
    min-width: 0;
  }

  .pos-ai-mark {
    display: grid;
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 14px;
    color: #bfdbfe;
    background: rgba(37, 99, 235, 0.18);
  }

  .pos-ai-title h3 {
    margin: 0;
    color: #fff;
    font-size: 1rem;
    font-weight: 800;
  }

  .pos-ai-title p {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin: 0.16rem 0 0;
    color: #9ca3af;
    font-size: 0.76rem;
    font-weight: 700;
  }

  .pos-ai-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.14);
  }

  .pos-ai-icon-btn,
  .pos-ai-send,
  .pos-ai-attach {
    display: grid;
    width: 38px;
    height: 38px;
    flex: 0 0 auto;
    place-items: center;
    border: 0;
    border-radius: 12px;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.06);
    cursor: pointer;
  }

  .pos-ai-quick {
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 1rem 0;
    overflow-x: auto;
    scrollbar-width: none;
  }

  .pos-ai-quick::-webkit-scrollbar {
    display: none;
  }

  .pos-ai-quick button {
    flex: 0 0 auto;
    padding: 0.44rem 0.72rem;
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 999px;
    color: #dbeafe;
    background: rgba(37, 99, 235, 0.16);
    font-size: 0.73rem;
    font-weight: 800;
    cursor: pointer;
  }

  .pos-ai-history {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.35) transparent;
  }

  .pos-ai-history::-webkit-scrollbar {
    width: 5px;
  }

  .pos-ai-history::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(148, 163, 184, 0.35);
  }

  .pos-ai-message {
    display: flex;
    flex-direction: column;
    max-width: 86%;
  }

  .pos-ai-message-user {
    align-self: flex-end;
    align-items: flex-end;
  }

  .pos-ai-message-bot {
    align-self: flex-start;
    align-items: flex-start;
  }

  .pos-ai-bubble {
    padding: 0.82rem 0.92rem;
    border: 1px solid rgba(226, 232, 240, 0.12);
    border-radius: 16px;
    color: #f8fafc;
    background: rgba(255, 255, 255, 0.07);
    font-size: 0.86rem;
    line-height: 1.55;
    white-space: pre-wrap;
  }

  .pos-ai-message-user .pos-ai-bubble {
    border-top-right-radius: 5px;
    background: linear-gradient(135deg, #2563eb, #0f766e);
  }

  .pos-ai-message-bot .pos-ai-bubble {
    border-top-left-radius: 5px;
  }

  .pos-ai-message small {
    margin-top: 0.35rem;
    color: #64748b;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.06em;
  }

  .pos-ai-upload-preview {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin: 0 1rem 0.75rem;
    padding: 0.65rem 0.75rem;
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 12px;
    color: #e2e8f0;
    background: rgba(255, 255, 255, 0.06);
  }

  .pos-ai-upload-preview > div {
    display: flex;
    min-width: 0;
    gap: 0.5rem;
    align-items: center;
  }

  .pos-ai-upload-preview span {
    overflow: hidden;
    font-size: 0.78rem;
    font-weight: 800;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .pos-ai-upload-preview button {
    border: 0;
    color: #fecaca;
    background: transparent;
    cursor: pointer;
  }

  .pos-ai-form {
    display: flex;
    gap: 0.55rem;
    align-items: center;
    padding: 0.85rem 1rem 1rem;
    border-top: 1px solid rgba(226, 232, 240, 0.12);
    background: rgba(2, 6, 23, 0.52);
  }

  .pos-ai-form input[type="text"] {
    min-width: 0;
    height: 42px;
    flex: 1 1 auto;
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 12px;
    color: #fff;
    background: rgba(255, 255, 255, 0.07);
    font-size: 0.86rem;
    outline: 0;
    padding: 0 0.85rem;
  }

  .pos-ai-form input[type="text"]::placeholder {
    color: #94a3b8;
  }

  .pos-ai-send {
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #10b981);
  }

  .pos-ai-typing {
    display: inline-flex;
    gap: 0.25rem;
    align-items: center;
    min-width: 56px;
  }

  .pos-ai-typing span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #60a5fa;
    animation: pos-ai-bounce 1.2s ease-in-out infinite;
  }

  .pos-ai-typing span:nth-child(2) {
    animation-delay: 0.16s;
  }

  .pos-ai-typing span:nth-child(3) {
    animation-delay: 0.32s;
  }

  .pos-ai-listening {
    color: #60a5fa;
    box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
  }

  @keyframes pos-ai-pulse {
    0% { transform: scale(1); opacity: 0.7; }
    100% { transform: scale(1.55); opacity: 0; }
  }

  @keyframes pos-ai-rise {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  @keyframes pos-ai-bounce {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.45; }
    40% { transform: translateY(-4px); opacity: 1; }
  }

  @media (max-width: 575.98px) {
    .pos-ai-assistant {
      right: 16px;
      bottom: 16px;
    }

    .pos-ai-toggle {
      width: 56px;
      height: 56px;
      border-radius: 18px;
    }

    .pos-ai-window {
      right: -2px;
      bottom: 72px;
      height: min(560px, calc(100vh - 96px));
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('pos-ai-assistant');
  if (!wrapper) return;

  const toggle = document.getElementById('pos-ai-toggle');
  const closeBtn = document.getElementById('pos-ai-close');
  const form = document.getElementById('pos-ai-form');
  const input = document.getElementById('pos-ai-input');
  const historyEl = document.getElementById('pos-ai-history');
  const fileInput = document.getElementById('pos-ai-file');
  const filePreview = document.getElementById('pos-ai-upload-preview');
  const fileName = document.getElementById('pos-ai-file-name');
  const removeFileBtn = document.getElementById('pos-ai-remove-file');
  const voiceBtn = document.getElementById('pos-ai-voice');
  const quickButtons = wrapper.querySelectorAll('[data-prompt]');
  const chatEndpoint = wrapper.dataset.chatEndpoint;
  const analyzeEndpoint = wrapper.dataset.analyzeEndpoint;

  let selectedFile = null;
  let chatHistory = [];
  let recognition = null;
  let listening = false;

  function openAssistant() {
    wrapper.classList.add('is-open');
    setTimeout(function () { input.focus(); }, 60);
    scrollHistory();
  }

  function closeAssistant() {
    wrapper.classList.remove('is-open');
  }

  function scrollHistory() {
    historyEl.scrollTop = historyEl.scrollHeight;
  }

  function timestamp() {
    return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function appendMessage(role, text) {
    const article = document.createElement('article');
    article.className = 'pos-ai-message ' + (role === 'user' ? 'pos-ai-message-user' : 'pos-ai-message-bot');

    const bubble = document.createElement('div');
    bubble.className = 'pos-ai-bubble';
    bubble.textContent = text;

    const meta = document.createElement('small');
    meta.textContent = (role === 'user' ? 'ADMIN' : 'COMMAND AI') + ' - ' + timestamp();

    article.appendChild(bubble);
    article.appendChild(meta);
    historyEl.appendChild(article);
    scrollHistory();
    return article;
  }

  function showTyping() {
    const article = document.createElement('article');
    article.className = 'pos-ai-message pos-ai-message-bot';
    article.innerHTML = '<div class="pos-ai-bubble"><span class="pos-ai-typing"><span></span><span></span><span></span></span></div><small>COMMAND AI</small>';
    historyEl.appendChild(article);
    scrollHistory();
    return article;
  }

  function setFile(file) {
    selectedFile = file;
    if (selectedFile) {
      fileName.textContent = selectedFile.name;
      filePreview.classList.remove('d-none');
      input.placeholder = 'Add a question about this file...';
    }
  }

  function clearFile() {
    selectedFile = null;
    fileInput.value = '';
    filePreview.classList.add('d-none');
    input.placeholder = 'Ask Command Center AI...';
  }

  async function sendMessage(forcedPrompt) {
    const text = (forcedPrompt || input.value).trim();
    if (!text && !selectedFile) return;

    openAssistant();
    appendMessage('user', text || ('Analyze file: ' + selectedFile.name));
    input.value = '';

    const typingEl = showTyping();

    try {
      let response;
      if (selectedFile) {
        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('prompt', text || 'Analyze this POS file and summarize what matters.');

        response = await fetch(analyzeEndpoint, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
        });
        clearFile();
      } else {
        response = await fetch(chatEndpoint, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ message: text, history: chatHistory })
        });
      }

      const data = await response.json();
      typingEl.remove();

      if (!response.ok || data.error) {
        appendMessage('bot', data.error || 'The AI assistant could not complete the request.');
        return;
      }

      appendMessage('bot', data.reply);
      if (text) {
        chatHistory.push({ role: 'user', content: text });
      }
      chatHistory.push({ role: 'assistant', content: data.reply });
      chatHistory = chatHistory.slice(-10);
      speak(data.reply);
    } catch (error) {
      typingEl.remove();
      appendMessage('bot', 'Connection error. The assistant endpoint could not be reached.');
    }
  }

  function speak(text) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 1;
    utterance.pitch = 0.95;
    window.speechSynthesis.speak(utterance);
  }

  function setupVoice() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      voiceBtn.style.display = 'none';
      return;
    }

    recognition = new SpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = 'en-US';

    recognition.onstart = function () {
      listening = true;
      voiceBtn.classList.add('pos-ai-listening');
      voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
      if ('speechSynthesis' in window) window.speechSynthesis.cancel();
    };

    recognition.onresult = function (event) {
      input.value = event.results[0][0].transcript;
      sendMessage();
    };

    recognition.onend = function () {
      listening = false;
      voiceBtn.classList.remove('pos-ai-listening');
      voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
    };
  }

  toggle.addEventListener('click', function () {
    if (wrapper.classList.contains('is-open')) closeAssistant();
    else openAssistant();
  });

  closeBtn.addEventListener('click', closeAssistant);

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    sendMessage();
  });

  fileInput.addEventListener('change', function (event) {
    if (event.target.files && event.target.files.length) {
      setFile(event.target.files[0]);
      openAssistant();
    }
  });

  removeFileBtn.addEventListener('click', clearFile);

  quickButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      sendMessage(button.dataset.prompt || '');
    });
  });

  setupVoice();
  voiceBtn.addEventListener('click', function () {
    if (!recognition) return;
    if (listening) recognition.stop();
    else recognition.start();
  });
});
</script>
