<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redirecionando...</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <style>
    body { display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;gap:16px;background:linear-gradient(135deg,var(--green-dark) 0%,var(--green) 100%); }
    .redirect-box { background:#fff;border-radius:20px;padding:40px 48px;text-align:center;box-shadow:0 16px 48px rgba(0,0,0,.18);max-width:360px;width:90%; }
    .redirect-icon { font-size:3.5rem;margin-bottom:16px;display:block; }
    .redirect-box h2 { font-size:1.2rem;font-weight:800;color:var(--text);margin-bottom:8px; }
    .redirect-box p  { font-size:.875rem;color:var(--text-muted);line-height:1.5; }
    .redirect-spinner { width:40px;height:40px;border:3px solid var(--green-muted);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite;margin:16px auto 0; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .redirect-error { color:var(--danger);font-weight:600; }
  </style>
</head>
<body>
  <div class="redirect-box">
    <span class="redirect-icon">📱</span>
    <h2 id="rdTitle">Abrindo WhatsApp...</h2>
    <p id="rdMsg">Você será redirecionado em instantes.</p>
    <div class="redirect-spinner" id="rdSpinner"></div>
  </div>

  <script>
    async function go() {
      const p   = new URLSearchParams(window.location.search);
      const lid = p.get('lid');
      if (!lid) { showError('Parâmetros inválidos.'); return; }

      try {
        const [urlRes] = await Promise.all([
          fetch(`/api/links/url/${encodeURIComponent(lid)}`).then(r => r.json()),
          fetch(`/api/links/click/${encodeURIComponent(lid)}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }),
        ]);
        if (!urlRes.ok) { showError('Link não encontrado ou expirado.'); return; }
        window.location.href = urlRes.data.url;
      } catch(e) {
        showError('Erro ao carregar o link.');
      }
    }

    function showError(msg) {
      document.getElementById('rdTitle').textContent = 'Link inválido';
      document.getElementById('rdMsg').innerHTML = `<span class="redirect-error">${msg}</span>`;
      document.getElementById('rdSpinner').style.display = 'none';
    }

    go();
  </script>
</body>
</html>
