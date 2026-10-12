<div id="flash-toasts" style="position:fixed;top:78px;right:12px;z-index:1100;display:flex;flex-direction:column;gap:8px"></div>
<script>
(function(){
  const toasts = document.getElementById('flash-toasts');
  function makeToast(message, type){
    const el = document.createElement('div');
    el.textContent = message;
    el.style.padding = '10px 14px';
    el.style.borderRadius = '10px';
    el.style.color = '#000';
    el.style.minWidth = '180px';
    el.style.boxShadow = '0 6px 18px rgba(0,0,0,0.25)';
    el.style.background = type==='error' ? '#ffb4b4' : '#a7f3d0';
    el.style.border = '1px solid rgba(0,0,0,0.1)';
    toasts.appendChild(el);
    setTimeout(()=>{ el.style.opacity = '0.9'; }, 10);
    setTimeout(()=>{ el.style.opacity = '0.5'; }, 2400);
    setTimeout(()=>{ el.remove(); }, 3200);
  }
  @if(session('status'))
    makeToast(@json(session('status')), 'success');
  @endif
  @if(session('error'))
    makeToast(@json(session('error')), 'error');
  @endif
})();
</script>
