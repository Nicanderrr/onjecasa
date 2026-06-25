@extends('layouts.admin')

@section('content')
<div class="card shadow">
  <div class="card-header border-0 d-flex justify-content-between align-items-center">
    <h3 class="mb-0">AI Assistant</h3>
    <span class="badge badge-success">OpenAI: {{ env('OPENAI_MODEL', 'gpt-4o-mini') }}</span>
  </div>
  <div class="card-body">
    <div id="chat-box" style="height: 420px; overflow-y:auto; background:#f8f9fe; border:1px solid #e9ecef; border-radius:8px; padding:12px;"></div>
    <div class="mt-3 d-flex gap-2">
      <input id="chat-input" class="form-control" placeholder="Ask about sales, stock, orders, staff...">
      <button id="send-btn" class="btn btn-primary">Send</button>
    </div>
  </div>
</div>

<script>
(() => {
  const chatBox = document.getElementById('chat-box');
  const chatInput = document.getElementById('chat-input');
  const sendBtn = document.getElementById('send-btn');
  const history = [];

  const add = (role, text) => {
    const row = document.createElement('div');
    row.style.marginBottom = '10px';
    row.innerHTML = `<div><strong>${role === 'user' ? 'You' : 'AI'}:</strong> ${text}</div>`;
    chatBox.appendChild(row);
    chatBox.scrollTop = chatBox.scrollHeight;
  };

  const send = async () => {
    const message = chatInput.value.trim();
    if (!message) return;

    add('user', message);
    history.push({ role: 'user', content: message });
    chatInput.value = '';

    try {
      const res = await fetch('{{ route('admin.ai.chat') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message, history })
      });

      const data = await res.json();
      const reply = data.reply || data.error || 'No response.';
      add('assistant', reply);
      history.push({ role: 'assistant', content: reply });
    } catch (e) {
      add('assistant', 'Request failed.');
    }
  };

  sendBtn.addEventListener('click', send);
  chatInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
  add('assistant', 'Ask me anything about your system status, orders, stock, staff, and sales.');
})();
</script>
@endsection
