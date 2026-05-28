<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsLink — Recuperar senha</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="page-login">
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
      <p class="brand-tagline">Recupere o acesso à sua conta em poucos passos.</p>
    </div>
  </div>

  <div class="login-form-panel">
    <div class="login-form-inner">
      <a href="{{ url('/login') }}" class="back-link">← Voltar ao login</a>

      <div class="form-header">
        <h2>Recuperar senha</h2>
        <p>Digite seu e-mail e enviaremos um link para redefinir sua senha.</p>
      </div>

      @if(session('status'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ url('/forgot-password') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="email">E-mail</label>
          <input class="form-input" type="email" id="email" name="email" placeholder="seu@email.com" required autofocus>
          @error('email')
            <div class="error-msg" style="display:block">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary">Enviar link de recuperação</button>
      </form>
    </div>
  </div>
</body>
</html>
