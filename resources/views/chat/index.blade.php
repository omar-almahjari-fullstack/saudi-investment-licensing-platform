@extends('layouts.app')

@section('content')
<div class="chat-container" style="max-width:960px;margin:0 auto;padding-bottom:24px;">
  <div class="glass-card header-card" style="padding:16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;">
    <div>
      <h1 style="margin:0;color:#fff;font-size:22px;">الدردشة مع مدير النظام</h1>
    </div>
    <a href="/" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
  </div>

  <div class="glass-card chat-wrapper" style="padding:0;min-height:60vh;display:flex;flex-direction:column;">
    <style>
      .chat-wrapper {
        box-sizing: border-box;
        border-radius: 12px;
        overflow: hidden;
        height:60vh;
        min-height:340px;
        display:flex;
        flex-direction:column;
      }
      .chat-panel {
        display:flex;
        flex-direction:column;
        flex:1 1 auto;
        min-height: 0;
        height: 100%;
        background: transparent;
      }
      #chat-thread {
        flex:1 1 0%;
        padding: 12px;
        gap: 8px;
        display:flex;
        flex-direction:column;
        overflow-y:auto;
        overflow-x:hidden;
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box;
        min-height:0;
        max-height:100%;
        scrollbar-width: thin;
        scrollbar-color: #07b7ff #0b1830;
        background: transparent;
      }
      #chat-thread::-webkit-scrollbar {
        width: 7px;
        background: #0b1830;
        border-radius: 10px;
      }
      #chat-thread::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg,#0ee5e8,#07b7ff);
        border-radius: 10px;
      }
      .chat-row {
        width: 100%;
        display:flex;
        flex-direction:column;
        box-sizing:border-box;
        margin-bottom: 6px;
        align-items: flex-start;
      }
      .chat-row.me { align-items: flex-end; }
      .chat-row.other { align-items: flex-start; }
      .chat-bubble {
        display: inline-block;
        box-sizing: border-box;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        font-size: 15px;
        line-height: 1.7;
        min-width: 60px;
        max-width: 350px;
        background: #0b1830 !important;
        border: 1.5px solid #60a5fa !important;
        color: #e5e7eb !important;
        margin-bottom: 2px !important;
        word-break: break-word;
        white-space: pre-wrap;
        text-align: right;
        transition: background 0.2s;
      }
      .chat-row.other .chat-bubble {
        background: #1f2937 !important;
        border-color: #4dc66e !important;
        color: #e5e7eb !important;
      }
      .chat-date-separator {
        width: 100%;
        text-align: center;
        margin: 18px 0 8px 0;
      }
      .chat-date-separator span {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 14px;
        background: #273347;
        color: #a8b4c6;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: .05em;
      }
      .chat-time {
        font-size: 11px;
        color: #60a5fa;
        margin-top: 3px;
        margin-inline-start: 8px;
        direction: ltr;
      }
      .chat-row.me .chat-time { text-align: left; }
      .chat-row.other .chat-time { text-align: right; }
      #chat-send {
        display:flex;
        gap:8px;
        padding:12px;
        border-top:1px solid rgba(255,255,255,0.06);
        background:#0f1724;
        box-sizing:border-box;
        align-items:center;
      }
      #chat-text {
        flex:1;
        min-height:48px;
        max-height:140px;
        height:56px;
        resize:vertical;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid #374151;
        background:#1f2937;
        color:#fff;
        box-sizing:border-box;
        overflow:auto;
        font-size:14px;
      }
      .chat-send-button {
        background: linear-gradient(135deg,#0ee5e8,#07b7ff);
        border: none;
        color: #05202a;
        padding: 12px 18px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        height:56px;
        min-width:120px;
      }
      @media (max-width: 640px) {
        .chat-wrapper { min-height:340px; height:68vh; }
        #chat-send { flex-direction: column; align-items: stretch; padding:10px; gap:8px; }
        #chat-text { height:48px; max-height:120px; }
        .chat-send-button { width:100%; height:48px; min-width:unset; border-radius:10px; padding:12px; }
        .chat-bubble { max-width: 85%; }
        .chat-time { max-width:85%; }
      }
      .chat-thread, #chat-thread, .chat-panel { max-width:100%; }
      .chat-bubble { margin:0; }
    </style>

    <div class="chat-panel">
      <div id="chat-thread" class="chat-thread" aria-live="polite" role="log">
        @php $lastDate = null; @endphp
        @if(isset($messages) && $messages->count())
          @foreach($messages as $i => $m)
            @php
              $isMe = ($m->sender_id && $m->sender_id === $uid);
              $showDate = false;
              if($i === 0) $showDate = true;
              elseif($lastDate) {
                $diffDays = \Carbon\Carbon::parse($m->created_at)->diffInDays(\Carbon\Carbon::parse($lastDate));
                if($diffDays >= 3) $showDate = true;
              }
              $lastDate = $m->created_at;
            @endphp

            @if($showDate)
              <div class="chat-date-separator"><span>{{ \Carbon\Carbon::parse($m->created_at)->format('Y-m-d') }}</span></div>
            @endif

            <div class="chat-row @if($isMe) me @else other @endif">
              <div class="chat-bubble @if($isMe) me @else other @endif">{!! nl2br(e($m->content)) !!}</div>
              <div class="chat-time @if($isMe) me @else other @endif">{{ \Carbon\Carbon::parse($m->created_at)->format('H:i:s') }}</div>
            </div>
          @endforeach
        @else
          <div style="color:#9ca3af;padding:12px;">لا توجد رسائل حتى الآن. ابدأ بمراسلة المدير أو انتظر رسالة منه.</div>
        @endif
      </div>

      <form id="chat-send" aria-label="نموذج إرسال الرسائل">
        <textarea id="chat-text" name="content" placeholder="اكتب رسالتك..." autocomplete="off" rows="2"></textarea>
        <button class="chat-send-button" type="submit">إرسال</button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    const thread = document.getElementById('chat-thread');
    const form = document.getElementById('chat-send');
    const input = document.getElementById('chat-text');

    function scrollToBottom() {
      try {
        thread.scrollTop = thread.scrollHeight;
      } catch(e){}
    }

    window.addEventListener('load', scrollToBottom);

    if ('ResizeObserver' in window) {
      new ResizeObserver(scrollToBottom).observe(thread);
    }

    function bubble(text, who){
      const row = document.createElement('div');
      row.className = 'chat-row ' + (who === 'me' ? 'me' : 'other');

      const b = document.createElement('div');
      b.className = 'chat-bubble ' + (who === 'me' ? 'me' : 'other');
      b.innerHTML = (''+text).replace(/\n/g, '<br/>');

      const time = document.createElement('div');
      time.className = 'chat-time ' + (who === 'me' ? 'me' : 'other');
      time.textContent = new Date().toLocaleTimeString();

      row.appendChild(b);
      row.appendChild(time);
      thread.appendChild(row);
      scrollToBottom();
    }

    const conversationId = {!! json_encode(optional($conversation)->id ?? null) !!};

    form.addEventListener('submit', function(e){
      e.preventDefault();
      const txt = (input.value||'').trim();
      if(!txt) return;
      bubble(txt, 'me');
      input.value='';
      fetch('{{ route('chat.send') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ conversation_id: conversationId, content: txt })
      });
    });

    input.addEventListener('keydown', function(e){
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit', { cancelable: true }));
      }
    });

    scrollToBottom();
  })();
</script>
@endpush
@endsection