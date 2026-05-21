<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsLink — Admin</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="page-admin" data-page="admin">

  <div id="toast"></div>

  <nav class="navbar">
    <a href="{{ url('/admin') }}" class="nav-brand">
      <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="24" cy="24" r="24" fill="#25D366"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M24 8C15.163 8 8 15.163 8 24c0 2.84.742 5.51 2.044 7.828L8 40l8.41-2.006A15.945 15.945 0 0024 40c8.837 0 16-7.163 16-16S32.837 8 24 8zm-4.5 9.5c-.4-1-.8-1-.8-1s-1.2 0-1.8.6c-.6.6-2.2 2.2-2.2 5.4 0 3.2 2.3 6.3 2.6 6.7.3.4 4.4 7 10.8 9.4 1.5.6 2.7.9 3.6 1.2 1.5.5 2.9.4 3.9.2 1.2-.2 3.7-1.5 4.2-3 .5-1.5.5-2.7.4-3-.1-.3-.5-.5-1-.7-.5-.2-3.1-1.5-3.6-1.7-.5-.2-.8-.3-1.2.2-.3.5-1.3 1.7-1.6 2-.3.3-.6.4-1.1.1-.5-.2-2.1-.8-4-2.5-1.5-1.3-2.5-3-2.8-3.5-.3-.5 0-.8.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.4 0-.7-.1-1z" fill="white"/>
      </svg>
      <span>Whats<strong>Link</strong></span>
      <span class="admin-badge">Admin</span>
    </a>
    <div class="nav-right">
      <a href="{{ url('/') }}" class="btn-site-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        Ver site
      </a>
      <div class="nav-user">
        <div class="nav-avatar" id="navAvatar" style="background:#7c3aed">A</div>
        <span class="nav-name" id="navName">Admin</span>
      </div>
      <button class="btn-logout" id="btnLogout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sair
      </button>
    </div>
  </nav>

  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="sidebar-section-label">Dashboard</div>
      <button class="sidebar-item active" data-panel="overview"><span class="s-icon">📊</span> Visão Geral</button>
      <div class="sidebar-section-label" style="margin-top:8px">Gestão</div>
      <button class="sidebar-item" data-panel="users"><span class="s-icon">👥</span> Usuários</button>
      <button class="sidebar-item" data-panel="links"><span class="s-icon">🔗</span> Todos os Links</button>
      <div class="sidebar-section-label" style="margin-top:8px">SaaS</div>
      <button class="sidebar-item" data-panel="plans"><span class="s-icon">💳</span> Planos</button>
      <div class="sidebar-section-label" style="margin-top:8px">Dados</div>
      <button class="sidebar-item" data-panel="export"><span class="s-icon">📤</span> Exportar</button>
    </aside>

    <div class="admin-content">

      <div id="panel-overview" class="admin-panel">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">📊 Visão Geral do Sistema</h2>
        <div class="overview-grid">
          <div class="overview-card"><div class="stat-icon green">👥</div><div><div class="stat-value" id="ovUsers">0</div><div class="stat-label">Usuários</div></div></div>
          <div class="overview-card"><div class="stat-icon blue">🔗</div><div><div class="stat-value" id="ovLinks">0</div><div class="stat-label">Links criados</div></div></div>
          <div class="overview-card"><div class="stat-icon purple">📈</div><div><div class="stat-value" id="ovClicks">0</div><div class="stat-label">Cliques totais</div></div></div>
          <div class="overview-card"><div class="stat-icon" style="background:#fef3c7">🆕</div><div><div class="stat-value" id="ovToday">0</div><div class="stat-label">Cadastros hoje</div></div></div>
        </div>
        <div class="overview-two-col">
          <div class="card"><div class="card-title">🏆 Links mais clicados</div><div id="topLinksList"><p class="text-muted">Carregando...</p></div></div>
          <div class="card"><div class="card-title">🆕 Cadastros recentes</div><div id="recentUsersList"><p class="text-muted">Carregando...</p></div></div>
        </div>
      </div>

      <div id="panel-users" class="admin-panel hidden">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
          <div>
            <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:4px">👥 Cadastros</h2>
            <p class="text-muted" style="font-size:.875rem">Usuários registrados no sistema</p>
          </div>
        </div>
        <div class="user-plan-stats" id="userPlanStats"></div>
        <div class="filter-bar">
          <div class="period-filter" id="periodFilter">
            <button class="period-btn active" data-period="all">Todos</button>
            <button class="period-btn" data-period="today">Hoje</button>
            <button class="period-btn" data-period="week">Esta semana</button>
            <button class="period-btn" data-period="month">Este mês</button>
            <button class="period-btn" data-period="quarter">90 dias</button>
          </div>
          <div class="search-box" style="width:220px">
            <span class="search-icon">🔍</span>
            <input class="form-input" type="search" id="searchUsers" placeholder="Buscar por nome ou e-mail...">
          </div>
        </div>
        <div class="card" style="padding:0">
          <div class="table-wrap">
            <table class="data-table">
              <thead><tr><th>Usuário</th><th>Plano</th><th>Links</th><th>Cliques</th><th>Cadastrado em</th><th>Status</th><th>Ações</th></tr></thead>
              <tbody id="usersTableBody"><tr><td colspan="7" class="text-muted" style="text-align:center;padding:32px">Carregando...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="panel-links" class="admin-panel hidden">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">🔗 Todos os Links do Sistema</h2>
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="search-box" style="width:220px"><span class="search-icon">🔍</span><input class="form-input" type="search" id="searchLinks" placeholder="Buscar link..."></div>
            <select class="filter-select" id="filterUser"><option value="">Todos os usuários</option></select>
          </div>
        </div>
        <div class="card" style="padding:0">
          <div class="table-wrap">
            <table class="data-table">
              <thead><tr><th>Link / Mensagem</th><th>Número</th><th>Dono</th><th>Cliques</th><th>Criado</th><th>Ações</th></tr></thead>
              <tbody id="linksTableBody"><tr><td colspan="6" class="text-muted" style="text-align:center;padding:32px">Carregando...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="panel-plans" class="admin-panel hidden">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:6px">💳 Gerenciar Planos</h2>
        <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:24px">Os planos editados aqui aparecem automaticamente na landing page.</p>
        <div class="plans-layout">
          <div class="plan-form-card" id="planForm-wrap">
            <div class="card-title" id="planFormTitle">✚ Novo plano</div>
            <form id="planForm">
              <div class="form-group"><label class="form-label">Nome do plano *</label><input class="form-input" id="planName" placeholder="Ex: Pro, Business..." required></div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Preço (R$)</label><input class="form-input" type="number" id="planPrice" placeholder="0 = Grátis" min="0" step="0.01" value="0"></div>
                <div class="form-group"><label class="form-label">Máx. links</label><input class="form-input" type="number" id="planMaxLinks" placeholder="-1 = ilimitado" value="10"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Ordem</label><input class="form-input" type="number" id="planOrder" value="1" min="1"></div>
                <div class="form-group"><label class="form-label">Botão CTA</label><input class="form-input" id="planCta" placeholder="Ex: Assinar..."></div>
              </div>
              <div class="form-group"><label class="form-label">Funcionalidades <span class="label-optional">(uma por linha)</span></label><textarea class="form-input" id="planFeatures" rows="5" placeholder="5 links ativos&#10;QR Code por link"></textarea></div>
              <div class="form-group"><label class="check-label" style="font-size:.9rem;color:var(--text)"><input type="checkbox" id="planHighlighted"> ⭐ Destacar como "Mais popular"</label></div>
              <div style="display:flex;gap:10px;margin-top:4px">
                <button type="submit" class="btn btn-primary" style="flex:1">Salvar plano</button>
                <button type="button" class="btn btn-secondary" id="btnCancelPlan">Cancelar</button>
              </div>
            </form>
          </div>
          <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
              <div style="font-weight:700;font-size:1rem">Planos cadastrados</div>
              <span class="text-muted" style="font-size:.85rem">Visíveis na landing page</span>
            </div>
            <div class="plan-cards-grid" id="planCards"><p class="text-muted">Carregando...</p></div>
          </div>
        </div>
      </div>

      <div id="panel-export" class="admin-panel hidden">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">📤 Exportar Dados</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
          <div class="card">
            <div class="card-title">👥 Usuários</div>
            <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:16px;line-height:1.6">Exporta todos os usuários (sem senhas) em JSON.</p>
            <button class="btn btn-primary" id="btnExportUsers" style="width:100%">📥 Baixar usuarios.json</button>
          </div>
          <div class="card">
            <div class="card-title">🔗 Todos os Links</div>
            <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:16px;line-height:1.6">Exporta todos os links com contadores de cliques.</p>
            <button class="btn btn-primary" id="btnExportLinks" style="width:100%">📥 Baixar links.json</button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
