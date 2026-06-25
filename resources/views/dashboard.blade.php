<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsLink — Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="page-dashboard" data-page="dashboard">

  <div id="toast"></div>

  <div id="qrModal" class="modal-overlay hidden">
    <div class="modal-box">
      <button class="modal-close" id="qrModalClose">✕</button>
      <h3 id="qrTitle">QR Code</h3>
      <p class="text-muted">Escaneie para abrir no WhatsApp</p>
      <img class="qr-img" id="qrImg" src="" alt="QR Code">
      <div class="qr-url" id="qrLinkUrl"></div>
      <div class="qr-actions">
        <button class="qr-action-btn" id="btnShareQrLink" title="Compartilhar link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </button>
        <button class="qr-action-btn" id="btnShareQrText" title="Compartilhar mensagem">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </button>
        <button class="qr-action-btn" id="btnSaveQr" title="Salvar QR Code">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </button>
      </div>
    </div>
  </div>

  <nav class="navbar">
    <a href="{{ url('/dashboard') }}" class="nav-brand">
      <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="24" cy="24" r="24" fill="#25D366"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M24 8C15.163 8 8 15.163 8 24c0 2.84.742 5.51 2.044 7.828L8 40l8.41-2.006A15.945 15.945 0 0024 40c8.837 0 16-7.163 16-16S32.837 8 24 8zm-4.5 9.5c-.4-1-.8-1-.8-1s-1.2 0-1.8.6c-.6.6-2.2 2.2-2.2 5.4 0 3.2 2.3 6.3 2.6 6.7.3.4 4.4 7 10.8 9.4 1.5.6 2.7.9 3.6 1.2 1.5.5 2.9.4 3.9.2 1.2-.2 3.7-1.5 4.2-3 .5-1.5.5-2.7.4-3-.1-.3-.5-.5-1-.7-.5-.2-3.1-1.5-3.6-1.7-.5-.2-.8-.3-1.2.2-.3.5-1.3 1.7-1.6 2-.3.3-.6.4-1.1.1-.5-.2-2.1-.8-4-2.5-1.5-1.3-2.5-3-2.8-3.5-.3-.5 0-.8.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.4 0-.7-.1-1z" fill="white"/>
      </svg>
      <span>Whats<strong>Link</strong></span>
    </a>
    <div class="nav-right">
      <div class="nav-user">
        <div class="nav-avatar" id="navAvatar">A</div>
        <span class="nav-name" id="navName">Usuário</span>
      </div>
      <button class="btn-logout" id="btnLogout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sair
      </button>
    </div>
  </nav>

  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="sidebar-section-label">Painel</div>
      <button class="sidebar-item active" data-panel="overview"><span class="s-icon">📊</span> Dashboard</button>
      <div class="sidebar-section-label" style="margin-top:8px">Links</div>
      <button class="sidebar-item" data-panel="create"><span class="s-icon">✚</span> Criar Link</button>
      <button class="sidebar-item" data-panel="list"><span class="s-icon">📋</span> Meus Links</button>
      <div class="sidebar-section-label" style="margin-top:8px">Conta</div>
      <button class="sidebar-item" data-panel="billing"><span class="s-icon">💳</span> Meu Plano</button>
    </aside>

    <div class="admin-content">

      <div id="panelOverview" class="admin-panel">
        <div class="dash-welcome" id="dashWelcome">
          <div>
            <h2 id="dashGreeting">Olá!</h2>
            <p class="text-muted">Gerencie seus links de WhatsApp a partir daqui.</p>
          </div>
          <button class="btn btn-primary" style="white-space:nowrap" onclick="window._showPanel('create')">✚ Criar novo link</button>
        </div>
        <div class="stats-bar" style="margin-bottom:24px">
          <div class="stat-card"><div class="stat-icon green">🔗</div><div><div class="stat-value" id="statTotal">0</div><div class="stat-label">Links criados</div></div></div>
          <div class="stat-card"><div class="stat-icon blue">👆</div><div><div class="stat-value" id="statClicks">0</div><div class="stat-label">Cliques totais</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">📅</div><div><div class="stat-value" id="statToday">0</div><div class="stat-label">Criados hoje</div></div></div>
        </div>
        <div class="overview-two-col">
          <div class="card">
            <div class="card-title">🔗 Links recentes</div>
            <div id="dashRecentLinks"><p class="text-muted">Nenhum link criado ainda.</p></div>
            <button class="btn btn-secondary" style="width:100%;margin-top:16px" onclick="window._showPanel('list')">Ver todos os links →</button>
          </div>
          <div class="card">
            <div class="card-title">💡 Dicas rápidas</div>
            <div class="dash-tips">
              <div class="dash-tip"><span class="dash-tip-icon">🔗</span><div><strong>Crie links por categoria</strong><p>Nomeie cada link com o produto ou serviço — ex: "Produto X", "Serviço VIP".</p></div></div>
              <div class="dash-tip"><span class="dash-tip-icon">📷</span><div><strong>Use o QR Code</strong><p>Gere QR Codes para materiais impressos, stories e apresentações.</p></div></div>
              <div class="dash-tip"><span class="dash-tip-icon">📋</span><div><strong>Copie com um clique</strong><p>No card do link, clique em 📋 para copiar o link instantaneamente.</p></div></div>
            </div>
          </div>
        </div>
      </div>

      <div id="panelCreate" class="admin-panel hidden">
        <div class="create-grid">
          <div class="card">
            <div class="card-title">✚ Novo link de WhatsApp</div>
            <form id="formCreate">
              <div class="form-group">
                <label class="form-label" for="labelInput">Nome do link <span class="label-optional">(opcional)</span></label>
                <input class="form-input" type="text" id="labelInput" placeholder="Ex: Suporte, Vendas, Promo...">
              </div>
              <div class="form-group">
                <label class="form-label" for="phoneInput">Número do WhatsApp <span style="color:var(--danger)">*</span></label>
                <div class="phone-input-wrap">
                  <span class="phone-flag">🇧🇷</span>
                  <span class="phone-prefix">+55</span>
                  <input class="form-input with-prefix" type="tel" id="phoneInput" placeholder="(11) 99999-9999" maxlength="16" inputmode="numeric" required>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="msgInput">Mensagem pré-definida <span style="color:var(--danger)">*</span></label>
                <div class="format-toolbar">
                  <button type="button" class="format-btn" data-format="bold" title="Negrito"><strong>B</strong></button>
                  <button type="button" class="format-btn" data-format="italic" title="Itálico"><em>I</em></button>
                  <button type="button" class="format-btn" data-format="strike" title="Tachado"><s>S</s></button>
                  <button type="button" class="format-btn" data-format="mono" title="Mono"><code>M</code></button>
                  <span class="format-hint">Selecione o texto e clique para formatar</span>
                </div>
                <textarea class="form-input" id="msgInput" rows="4" maxlength="1000" placeholder="Olá! Vi seu anúncio e gostaria de mais informações." required></textarea>
                <div class="char-count" id="charCount">0 / 1000</div>
              </div>
              <div class="link-preview-box">
                <span>🔗</span>
                <span class="link-url" id="previewUrl">Preencha o número para ver o link...</span>
                <button type="button" class="btn btn-ghost btn-icon" id="btnCopyPreview" title="Copiar link">📋</button>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">✚ Criar link</button>
                <button type="reset" class="btn btn-secondary">Limpar</button>
              </div>
            </form>
          </div>
          <div>
            <div class="card" style="padding:0;overflow:hidden">
              <div class="card-title" style="padding:18px 22px 0">📱 Prévia do WhatsApp</div>
              <div style="padding:0 22px 22px">
                <div class="whatsapp-preview">
                  <div class="wa-header">
                    <div class="wa-avatar">👤</div>
                    <div class="wa-contact"><h4 id="waContact">Número</h4><p>online</p></div>
                  </div>
                  <div class="wa-body">
                    <div>
                      <div class="wa-bubble empty" id="waBubble">Sua mensagem aparecerá aqui...</div>
                      <div class="wa-time" id="waTime"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="panelList" class="admin-panel hidden" style="margin-top:0">
        <div class="card">
          <div class="list-header">
            <div class="card-title" style="margin:0">📋 Meus links</div>
            <div class="search-box">
              <span class="search-icon">🔍</span>
              <input class="form-input" type="search" id="searchInput" placeholder="Buscar por nome, número...">
            </div>
          </div>
          <div class="links-list" id="linksList"></div>
        </div>
      </div>

      <div id="panelBilling" class="admin-panel hidden" style="margin-top:0">
        <div class="card" style="margin-bottom:20px">
          <div class="card-title">💳 Meu plano atual</div>
          <p id="currentPlanLabel" class="text-muted">Carregando...</p>
        </div>
        <div class="plan-cards-grid" id="billingPlanCards"><p class="text-muted">Carregando planos...</p></div>
      </div>

    </div>
  </div>

  <div id="cpfModal" class="modal-overlay hidden">
    <div class="modal-box">
      <button class="modal-close" id="cpfModalClose">✕</button>
      <h3>Confirmar dados para o boleto</h3>
      <p class="text-muted" style="margin-bottom:16px">Informe seu CPF ou CNPJ para gerarmos a cobrança no Asaas.</p>
      <div class="form-group">
        <label class="form-label" for="cpfInput">CPF ou CNPJ</label>
        <input class="form-input" id="cpfInput" placeholder="000.000.000-00">
      </div>
      <button class="btn btn-primary" style="width:100%" id="btnConfirmSubscribe">Gerar boleto</button>
    </div>
  </div>

  <div id="boletoModal" class="modal-overlay hidden">
    <div class="modal-box">
      <button class="modal-close" id="boletoModalClose">✕</button>
      <h3>📄 Boleto gerado!</h3>
      <p class="text-muted">Vencimento: <strong id="boletoDueDate"></strong></p>
      <p class="text-muted">Valor: <strong id="boletoAmount"></strong></p>
      <a id="boletoLink" href="#" target="_blank" class="btn btn-primary" style="width:100%;margin-top:16px">Abrir boleto para pagamento →</a>
      <p class="text-muted" style="font-size:.8rem;margin-top:12px">Após o pagamento ser confirmado pelo Asaas, seu plano é atualizado automaticamente.</p>
    </div>
  </div>

  <script>
    const now = new Date();
    document.getElementById('waTime').textContent =
      now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
  </script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
