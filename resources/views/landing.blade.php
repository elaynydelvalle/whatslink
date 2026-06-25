<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsLink — Gerador de Links para WhatsApp</title>
  <meta name="description" content="Crie links personalizados para WhatsApp com mensagens pré-definidas. Rastreie cliques, gere QR Codes e gerencie tudo em um painel.">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body data-page="landing">

  <div id="toast"></div>

  <!-- ═══ NAVBAR ═══ -->
  <nav class="landing-nav">
    <div class="landing-nav-inner">
      <a href="{{ url('/') }}" class="nav-brand">
        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:32px;height:32px">
          <circle cx="24" cy="24" r="24" fill="#25D366"/>
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M24 8C15.163 8 8 15.163 8 24c0 2.84.742 5.51 2.044 7.828L8 40l8.41-2.006A15.945 15.945 0 0024 40c8.837 0 16-7.163 16-16S32.837 8 24 8zm-4.5 9.5c-.4-1-.8-1-.8-1s-1.2 0-1.8.6c-.6.6-2.2 2.2-2.2 5.4 0 3.2 2.3 6.3 2.6 6.7.3.4 4.4 7 10.8 9.4 1.5.6 2.7.9 3.6 1.2 1.5.5 2.9.4 3.9.2 1.2-.2 3.7-1.5 4.2-3 .5-1.5.5-2.7.4-3-.1-.3-.5-.5-1-.7-.5-.2-3.1-1.5-3.6-1.7-.5-.2-.8-.3-1.2.2-.3.5-1.3 1.7-1.6 2-.3.3-.6.4-1.1.1-.5-.2-2.1-.8-4-2.5-1.5-1.3-2.5-3-2.8-3.5-.3-.5 0-.8.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.4 0-.7-.1-1z" fill="white"/>
        </svg>
        <span>Whats<strong>Link</strong></span>
      </a>
      <div class="landing-nav-links">
        <a href="#features">Funcionalidades</a>
        <a href="#how">Como funciona</a>
        <a href="#pricing">Planos</a>
      </div>
      <div class="landing-nav-cta">
        <a href="{{ url('/login') }}" class="btn btn-login-access">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          Acessar
        </a>
        <a href="{{ url('/login') }}" class="btn btn-primary" style="padding:9px 18px">Começar grátis</a>
      </div>
    </div>
  </nav>

  <!-- ═══ HERO ═══ -->
  <section class="hero-section">
    <div class="hero-content">
      <div class="hero-badge">✨ Crie seu link personalizado para WhatsApp</div>
      <h1 class="hero-title">Crie links de WhatsApp com <span class="hero-highlight">mensagens automáticas</span></h1>
      <p class="hero-subtitle">Gere links personalizados com mensagens pré-definidas, rastreie cliques em tempo real, crie QR Codes e gerencie tudo em um painel intuitivo.</p>
      <div class="hero-btns">
        <a href="{{ url('/login') }}" class="btn btn-primary hero-btn-primary">Começar grátis agora</a>
        <a href="#how" class="btn btn-secondary hero-btn-secondary">Ver como funciona</a>
      </div>
      <p class="hero-already-have-account">Já tem conta? <a href="{{ url('/login') }}">Acessar agora →</a></p>
      <div class="hero-social-proof">
        <div class="social-avatars">
          <span>👤</span><span>👤</span><span>👤</span><span>👤</span>
        </div>
        <p>Mais de <strong>500 links</strong> criados por usuários em todo o Brasil</p>
      </div>
    </div>
    <div class="hero-mockup">
      <div class="mockup-phone">
        <div class="mockup-screen">
          <div class="mockup-wa-header">
            <div style="width:28px;height:28px;background:rgba(255,255,255,.2);border-radius:50%;display:grid;place-items:center">👤</div>
            <div><div style="font-size:.8rem;font-weight:600">Contato</div><div style="font-size:.65rem;opacity:.8">online</div></div>
          </div>
          <div style="padding:10px;display:flex;flex-direction:column;gap:8px">
            <div class="mockup-bubble">Olá! Vi seu anúncio e gostaria de mais informações 😊</div>
            <div class="mockup-bubble reply">Claro! Em que posso ajudar?</div>
            <div class="mockup-bubble">Quero saber sobre o produto X</div>
          </div>
        </div>
      </div>
      <div class="mockup-glow"></div>
    </div>
  </section>

  <!-- ═══ FEATURES ═══ -->
  <section class="features-section" id="features">
    <div class="section-inner">
      <div class="section-header">
        <div class="section-badge">✨ Funcionalidades</div>
        <h2>Tudo que você precisa para converter mais pelo WhatsApp</h2>
        <p>Uma plataforma completa para criar, gerenciar e rastrear seus links de WhatsApp.</p>
      </div>
      <div class="features-grid">
        <div class="feature-card"><div class="feature-icon-box green">🔗</div><h3>Criação de Links</h3><p>Crie links wa.me personalizados com número e mensagem pré-definida.</p></div>
        <div class="feature-card"><div class="feature-icon-box blue">📈</div><h3>Rastreamento de Cliques</h3><p>Saiba quantas pessoas clicaram em cada link. Métricas automáticas.</p></div>
        <div class="feature-card"><div class="feature-icon-box purple">📷</div><h3>QR Code Automático</h3><p>Gere QR Codes para qualquer link com um clique.</p></div>
        <div class="feature-card"><div class="feature-icon-box orange">🔁</div><h3>Duplicar Links</h3><p>Clone um link existente para criar variações rapidamente.</p></div>
        <div class="feature-card"><div class="feature-icon-box teal">📤</div><h3>Exportação de Dados</h3><p>Exporte todos os seus links em JSON para backup ou integração.</p></div>
        <div class="feature-card"><div class="feature-icon-box pink">🗂️</div><h3>Biblioteca de Links Salvos</h3><p>Edite qualquer link a qualquer momento e mantenha sua coleção organizada.</p></div>
      </div>
    </div>
  </section>

  <!-- ═══ HOW IT WORKS ═══ -->
  <section class="how-section" id="how">
    <div class="section-inner">
      <div class="section-header">
        <div class="section-badge">⚡ Simples assim</div>
        <h2>Como funciona em 3 passos</h2>
        <p>Em menos de 2 minutos você já tem seu link pronto para compartilhar.</p>
      </div>
      <div class="steps-grid">
        <div class="step-card"><div class="step-number">1</div><div class="step-icon">📝</div><h3>Cadastre-se</h3><p>Crie sua conta gratuitamente. Sem cartão de crédito.</p></div>
        <div class="step-arrow">→</div>
        <div class="step-card"><div class="step-number">2</div><div class="step-icon">⚙️</div><h3>Configure seu link</h3><p>Informe o número do WhatsApp e a mensagem automática.</p></div>
        <div class="step-arrow">→</div>
        <div class="step-card"><div class="step-number">3</div><div class="step-icon">🚀</div><h3>Compartilhe</h3><p>Copie o link ou QR Code e acompanhe os cliques.</p></div>
      </div>
    </div>
  </section>

  <!-- ═══ PRICING ═══ -->
  <section class="pricing-section" id="pricing">
    <div class="section-inner">
      <div class="section-header">
        <div class="section-badge">💰 Planos</div>
        <h2>Escolha o plano ideal para você</h2>
        <p>Comece grátis e escale conforme seu negócio cresce.</p>
      </div>
      <div class="pricing-grid" id="pricingGrid">
        <div class="pricing-card"><div class="pricing-name">Carregando planos...</div></div>
      </div>
      <p style="text-align:center;margin-top:24px;font-size:.88rem;color:var(--text-muted)">
        🔒 Pagamento seguro · 📞 Suporte em português · ✅ Cancele quando quiser
      </p>
    </div>
  </section>

  <!-- ═══ CTA BANNER ═══ -->
  <section class="cta-banner">
    <div class="section-inner" style="text-align:center">
      <h2 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:12px">Pronto para aumentar suas conversões?</h2>
      <p style="color:rgba(255,255,255,.85);margin-bottom:28px;font-size:1.05rem">Crie sua conta gratuitamente e comece agora mesmo.</p>
      <a href="{{ url('/login') }}" class="btn" style="background:#fff;color:var(--green-dark);font-weight:700;padding:14px 32px;font-size:1rem;box-shadow:0 4px 16px rgba(0,0,0,.2)">
        Criar minha conta grátis →
      </a>
    </div>
  </section>

  <!-- ═══ FOOTER ═══ -->
  <footer class="landing-footer">
    <div class="section-inner">
      <div class="footer-grid">
        <div>
          <div class="nav-brand" style="margin-bottom:12px;display:flex;align-items:center;gap:8px">
            <span style="font-size:1.1rem;font-weight:800;color:var(--text)">Whats<strong style="color:var(--green-dark)">Link</strong></span>
          </div>
          <p style="font-size:.875rem;color:var(--text-muted);max-width:220px;line-height:1.6">Plataforma SaaS para criação e gestão de links de WhatsApp.</p>
        </div>
        <div>
          <div class="footer-col-title">Produto</div>
          <a href="#features">Funcionalidades</a>
          <a href="#pricing">Planos e preços</a>
          <a href="{{ url('/login') }}">Acessar painel</a>
        </div>
        <div>
          <div class="footer-col-title">Conta</div>
          <a href="{{ url('/login') }}">Entrar</a>
          <a href="{{ url('/login') }}">Criar conta</a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© {{ date('Y') }} WhatsLink · Feito com ❤️ no Brasil</p>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
