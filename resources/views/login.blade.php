<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsLink — Entrar</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="page-login" data-page="login">

  <div id="toast"></div>

  <div class="login-brand">
    <div class="brand-inner">
      <div class="brand-logo">
        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="24" cy="24" r="24" fill="rgba(255,255,255,0.15)"/>
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M24 8C15.163 8 8 15.163 8 24c0 2.84.742 5.51 2.044 7.828L8 40l8.41-2.006A15.945 15.945 0 0024 40c8.837 0 16-7.163 16-16S32.837 8 24 8zm-4.5 9.5c-.4-1-.8-1-.8-1s-1.2 0-1.8.6c-.6.6-2.2 2.2-2.2 5.4 0 3.2 2.3 6.3 2.6 6.7.3.4 4.4 7 10.8 9.4 1.5.6 2.7.9 3.6 1.2 1.5.5 2.9.4 3.9.2 1.2-.2 3.7-1.5 4.2-3 .5-1.5.5-2.7.4-3-.1-.3-.5-.5-1-.7-.5-.2-3.1-1.5-3.6-1.7-.5-.2-.8-.3-1.2.2-.3.5-1.3 1.7-1.6 2-.3.3-.6.4-1.1.1-.5-.2-2.1-.8-4-2.5-1.5-1.3-2.5-3-2.8-3.5-.3-.5 0-.8.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.4 0-.7-.1-1z"
            fill="white"/>
        </svg>
        <h1>WhatsLink</h1>
      </div>
      <p class="brand-tagline">Crie links personalizados para WhatsApp com mensagens pré-definidas em poucos cliques.</p>
      <div class="brand-features">
        <div class="brand-feature"><div class="feature-icon">✓</div><p>Links com mensagens automáticas</p></div>
        <div class="brand-feature"><div class="feature-icon">✓</div><p>Rastreamento de cliques automático</p></div>
        <div class="brand-feature"><div class="feature-icon">✓</div><p>QR Code para cada link</p></div>
        <div class="brand-feature"><div class="feature-icon">👑</div><p>Painel administrativo completo</p></div>
      </div>
    </div>
    <div class="brand-circles"></div>
  </div>

  <div class="login-form-panel">
    <div class="login-form-inner">

      <a href="{{ url('/') }}" class="back-link">← Voltar ao site</a>

      <div class="form-header">
        <h2>Acessar minha conta</h2>
        <p>Bem-vindo de volta ao WhatsLink</p>
      </div>

      <div id="googleSignInWrap" class="google-signin-wrap">
        <div id="googleBtnWrap" style="display:flex;justify-content:center"></div>
        <button id="googleFallbackBtn" class="btn-google-main" onclick="handleGoogleFallback()">
          <svg width="20" height="20" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0">
            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
            <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
          Continuar com Google
        </button>
        <div id="googleSetupNotice" class="google-setup-notice hidden">
          <span>⚙️</span>
          <span>Google Sign-In não configurado. <a href="#" onclick="document.getElementById('tabLogin').click();return false">Use e-mail e senha</a>.</span>
        </div>
      </div>

      <div class="divider"><span>ou entre com e-mail</span></div>

      <div class="auth-tabs">
        <button class="auth-tab active" id="tabLogin">Entrar</button>
        <button class="auth-tab" id="tabRegister">Criar conta</button>
      </div>

      <form id="formLogin" autocomplete="off">
        <div class="form-group">
          <label class="form-label" for="loginEmail">E-mail</label>
          <input class="form-input" type="email" id="loginEmail" placeholder="seu@email.com" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="loginPass">Senha</label>
          <div class="password-wrap">
            <input class="form-input" type="password" id="loginPass" placeholder="••••••••" required>
            <button type="button" class="btn-eye" id="btnEye" tabindex="-1">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Entrar na conta</button>
        <div id="errLogin" class="error-msg hidden"></div>
        <div style="text-align:center;margin-top:12px">
          <a href="{{ url('/forgot-password') }}" style="font-size:13px;color:#6b7280;text-decoration:none">Esqueci minha senha</a>
        </div>
      </form>

      <form id="formRegister" class="hidden" autocomplete="off">
        <div class="form-group">
          <label class="form-label" for="regName">Seu nome</label>
          <input class="form-input" type="text" id="regName" placeholder="João Silva" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="regEmail">E-mail</label>
          <input class="form-input" type="email" id="regEmail" placeholder="seu@email.com" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="regPass">Senha</label>
            <input class="form-input" type="password" id="regPass" placeholder="Mín. 6 caracteres" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="regPass2">Confirmar</label>
            <input class="form-input" type="password" id="regPass2" placeholder="Repita" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Criar conta grátis</button>
        <div id="errReg" class="error-msg hidden"></div>
      </form>

    </div>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
  <script>
    document.getElementById('btnEye').addEventListener('click', () => {
      const inp = document.getElementById('loginPass');
      const icon = document.getElementById('eyeIcon');
      const show = inp.type === 'password';
      inp.type = show ? 'text' : 'password';
      icon.innerHTML = show
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    });
    window.handleGoogleFallback = function() {
      if (typeof GOOGLE_CLIENT_ID !== 'undefined' && GOOGLE_CLIENT_ID) return;
      document.getElementById('googleSetupNotice').classList.remove('hidden');
      document.getElementById('googleFallbackBtn').classList.add('hidden');
    };
  </script>
</body>
</html>
