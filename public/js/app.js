/* ============================================================
   WhatsLink — app.js  (Laravel backend)
   ============================================================ */

// ── CSRF Token (necessário para Laravel) ─────────────────────
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// ── API helper ────────────────────────────────────────────────
async function api(method, endpoint, body = null) {
  try {
    const opts = {
      method,
      credentials: 'include',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(`/api/${endpoint}`, opts);
    const data = await res.json();
    if (res.status === 401 && !window.location.pathname.endsWith('/login')) {
      window.location.href = '/login';
      return null;
    }
    return data;
  } catch (e) {
    return { ok: false, error: 'Erro de conexão com o servidor.' };
  }
}

// ── Auth helpers ──────────────────────────────────────────────
async function getSession()            { const r = await api('GET', 'auth/me'); return r?.ok ? r.data : null; }
async function doLogin(email, pass)    { return api('POST', 'auth/login',    { email, password: pass }); }
async function doRegister(n,e,p,p2)   { return api('POST', 'auth/register', { name:n, email:e, password:p, password2:p2 }); }
async function doLogout()              { await api('POST', 'auth/logout'); window.location.href = '/login'; }

// ── Links helpers ──────────────────────────────────────────────
async function getLinks(q = '')        { const r = await api('GET', `links${q ? '?q='+encodeURIComponent(q) : ''}`); return r?.ok ? r.data : []; }
async function createLink(data)        { return api('POST',   'links',       data); }
async function updateLink(id, data)    { return api('PUT',    `links/${id}`, data); }
async function removeLink(id)          { return api('DELETE', `links/${id}`); }
async function registerClick(id)       { return api('POST',   `links/click/${id}`); }

// ── Plans helpers ──────────────────────────────────────────────
async function getPlans()              { const r = await api('GET', 'plans'); return r?.ok ? r.data : []; }
async function savePlan(data, id)      { return id ? api('PUT', `plans/${id}`, data) : api('POST', 'plans', data); }
async function deletePlan(id)          { return api('DELETE', `plans/${id}`); }
async function togglePlan(id)          { return api('POST',   `plans/${id}/toggle`); }
async function subscribePlan(id, cpfCnpj) { return api('POST', `plans/${id}/subscribe`, { cpf_cnpj: cpfCnpj }); }

// ── Utils ─────────────────────────────────────────────────────
function escHtml(s)  { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function timeAgo(d)  {
  const m = Math.floor((Date.now() - new Date(d)) / 60000);
  if (m < 1)   return 'agora mesmo';
  if (m < 60)  return `${m}min atrás`;
  const h = Math.floor(m / 60);
  if (h < 24)  return `${h}h atrás`;
  const dy = Math.floor(h / 24);
  if (dy < 7)  return `${dy}d atrás`;
  return new Date(d).toLocaleDateString('pt-BR');
}
function formatPhone(raw) {
  const d = raw.replace(/\D/g,'');
  if (d.length <= 2)  return d;
  if (d.length <= 6)  return `(${d.slice(0,2)}) ${d.slice(2)}`;
  if (d.length <= 10) return `(${d.slice(0,2)}) ${d.slice(2,6)}-${d.slice(6)}`;
  return `(${d.slice(0,2)}) ${d.slice(2,7)}-${d.slice(7,11)}`;
}
function buildWaLink(phone, text) {
  const d = phone.replace(/\D/g,'');
  const f = d.startsWith('55') ? d : `55${d}`;
  return `https://wa.me/${f}?text=${encodeURIComponent(text)}`;
}
function buildTrackUrl(linkId) {
  return `${window.location.origin}/redirect?lid=${encodeURIComponent(linkId)}`;
}
function qrUrl(text) {
  return `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(text)}`;
}
function toast(msg, type = '') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg; t.className = `show ${type}`;
  clearTimeout(t._t); t._t = setTimeout(() => { t.className = ''; }, 3000);
}
function insertFormat(textarea, type) {
  if (!textarea) return;
  const chars = { bold:'*', italic:'_', strike:'~', mono:'`' };
  const c = chars[type]; if (!c) return;
  const s = textarea.selectionStart, e = textarea.selectionEnd;
  textarea.value = textarea.value.substring(0,s) + c + textarea.value.substring(s,e) + c + textarea.value.substring(e);
  textarea.selectionStart = s+1; textarea.selectionEnd = e+1;
  textarea.focus(); textarea.dispatchEvent(new Event('input'));
}

// ═══════════════════════════════════════════════════════════
//  LANDING PAGE
// ═══════════════════════════════════════════════════════════
async function initLanding() {
  const plans = await getPlans();
  const container = document.getElementById('pricingGrid');
  if (!container) return;
  container.innerHTML = plans.map(p => `
    <div class="pricing-card ${p.highlighted ? 'highlighted' : ''}">
      ${p.highlighted ? '<div class="pricing-badge">⭐ Mais popular</div>' : ''}
      <div class="pricing-name">${escHtml(p.name)}</div>
      <div class="pricing-price">
        ${p.price == 0 ? '<span class="price-val">Grátis</span>'
          : `<span class="price-curr">R$</span><span class="price-val">${Number(p.price).toFixed(2).replace('.',',')}</span><span class="price-per">/mês</span>`}
      </div>
      <ul class="pricing-features">
        ${(p.features||[]).map(f => `<li>✓ ${escHtml(f)}</li>`).join('')}
      </ul>
      <a href="login.html" class="btn ${p.highlighted ? 'btn-primary' : 'btn-secondary'} btn-full">${escHtml(p.cta)}</a>
    </div>`).join('');
}

// ═══════════════════════════════════════════════════════════
//  LOGIN PAGE
// ═══════════════════════════════════════════════════════════
async function initLogin() {
  const s = await getSession();
  if (s) { window.location.href = s.role === 'admin' ? '/admin' : '/dashboard'; return; }

  // Tabs
  const tabLogin = document.getElementById('tabLogin');
  const tabReg   = document.getElementById('tabRegister');
  const fLogin   = document.getElementById('formLogin');
  const fReg     = document.getElementById('formRegister');
  const errL     = document.getElementById('errLogin');
  const errR     = document.getElementById('errReg');

  function switchTab(t) {
    const isL = t === 'login';
    tabLogin.classList.toggle('active',  isL);
    tabReg.classList.toggle('active',   !isL);
    fLogin.classList.toggle('hidden',   !isL);
    fReg.classList.toggle('hidden',      isL);
    errL.classList.add('hidden'); errR.classList.add('hidden');
  }
  tabLogin.addEventListener('click', () => switchTab('login'));
  tabReg.addEventListener('click',   () => switchTab('register'));

  fLogin.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = fLogin.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Entrando...';
    const r = await doLogin(document.getElementById('loginEmail').value.trim(), document.getElementById('loginPass').value);
    btn.disabled = false; btn.textContent = 'Entrar na conta';
    if (!r?.ok) { errL.textContent = r?.error || 'Erro ao entrar.'; errL.classList.remove('hidden'); return; }
    window.location.href = r.data.role === 'admin' ? '/admin' : '/dashboard';
  });

  fReg.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = fReg.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Criando conta...';
    const r = await doRegister(
      document.getElementById('regName').value.trim(),
      document.getElementById('regEmail').value.trim(),
      document.getElementById('regPass').value,
      document.getElementById('regPass2').value
    );
    btn.disabled = false; btn.textContent = 'Criar conta grátis';
    if (!r?.ok) { errR.textContent = r?.error || 'Erro ao criar conta.'; errR.classList.remove('hidden'); return; }
    window.location.href = '/dashboard';
  });
}

// ═══════════════════════════════════════════════════════════
//  DASHBOARD (usuário)
// ═══════════════════════════════════════════════════════════
async function initDashboard() {
  const session = await getSession();
  if (!session) return;
  if (session.role === 'admin') { window.location.href = '/admin'; return; }

  document.getElementById('navName').textContent   = session.name;
  document.getElementById('navAvatar').textContent = session.name.charAt(0).toUpperCase();
  document.getElementById('btnLogout').addEventListener('click', doLogout);

  // Sidebar
  const panels = { overview: 'panelOverview', create: 'panelCreate', list: 'panelList', billing: 'panelBilling' };
  function showPanel(name) {
    document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
    document.querySelector(`.sidebar-item[data-panel="${name}"]`)?.classList.add('active');
    Object.entries(panels).forEach(([k, id]) => document.getElementById(id)?.classList.toggle('hidden', k !== name));
    if (name === 'list')     renderList();
    if (name === 'overview') renderOverview();
    if (name === 'billing')  renderBilling();
  }
  window._showPanel = showPanel;
  document.querySelectorAll('.sidebar-item').forEach(item => item.addEventListener('click', () => showPanel(item.dataset.panel)));

  // ── Overview ──────────────────────────────────────────────
  async function renderOverview() {
    const links = await getLinks();
    const hour  = new Date().getHours();
    const greet = hour < 12 ? 'Bom dia' : hour < 18 ? 'Boa tarde' : 'Boa noite';
    document.getElementById('dashGreeting').textContent = `${greet}, ${session.name.split(' ')[0]}! 👋`;
    updateStats(links);

    const recentEl = document.getElementById('dashRecentLinks');
    const recent   = links.slice(0, 5);
    if (!recent.length) {
      recentEl.innerHTML = '<p class="text-muted" style="font-size:.875rem">Você ainda não criou nenhum link. <a href="#" onclick="window._showPanel(\'create\');return false;" style="color:var(--green-dark);font-weight:600">Criar agora →</a></p>';
    } else {
      recentEl.innerHTML = recent.map(l => {
        const tUrl = buildTrackUrl(l.id);
        return `<div class="dash-recent-row">
          <div class="link-item-icon" style="width:34px;height:34px;font-size:.9rem;flex-shrink:0">🔗</div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(l.label)}</div>
            <div style="font-size:.78rem;color:var(--text-muted)">📱 +55 ${escHtml(l.phone)} · ${timeAgo(l.created_at)} · 📊 ${l.clicks||0} cliques</div>
          </div>
          <button class="btn btn-ghost btn-icon" onclick="window.open('${escHtml(l.url)}','_blank')">🚀</button>
          <button class="btn btn-ghost btn-icon" onclick="navigator.clipboard.writeText('${escHtml(tUrl)}').then(()=>toast('Link copiado!','success'))">📋</button>
        </div>`;}).join('');
    }
  }

  function updateStats(links) {
    const today = new Date().toDateString();
    const el = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v; };
    el('statTotal',  links.length);
    el('statClicks', links.reduce((s,l) => s+(l.clicks||0), 0));
    el('statToday',  links.filter(l => new Date(l.created_at).toDateString() === today).length);
  }

  renderOverview();

  // ── Create form ───────────────────────────────────────────
  const phoneInput = document.getElementById('phoneInput');
  const msgInput   = document.getElementById('msgInput');
  const charCount  = document.getElementById('charCount');
  const previewUrl = document.getElementById('previewUrl');
  const waBubble   = document.getElementById('waBubble');
  const waContact  = document.getElementById('waContact');

  phoneInput.addEventListener('input', e => { e.target.value = formatPhone(e.target.value); updatePreview(); });
  msgInput.addEventListener('input',   ()  => { charCount.textContent = `${msgInput.value.length} / 1000`; updatePreview(); });

  document.querySelectorAll('.format-btn').forEach(btn => {
    btn.addEventListener('click', () => insertFormat(msgInput, btn.dataset.format));
  });

  function updatePreview() {
    const phone = phoneInput.value, msg = msgInput.value;
    const digits = phone.replace(/\D/g,'');
    previewUrl.textContent = digits.length >= 10 ? buildWaLink(phone, msg) : 'Preencha o número para ver o link...';
    waContact.textContent  = digits.length >= 10 ? `+55 ${phone}` : 'Número';
    waBubble.textContent   = msg || 'Sua mensagem aparecerá aqui...';
    waBubble.classList.toggle('empty', !msg);
  }

  document.getElementById('btnCopyPreview').addEventListener('click', () => {
    const l = previewUrl.textContent;
    if (l.startsWith('https')) navigator.clipboard.writeText(l).then(() => toast('Link copiado!', 'success'));
  });

  document.getElementById('formCreate').addEventListener('submit', async (e) => {
    e.preventDefault();
    const phone = phoneInput.value.trim();
    const msg   = msgInput.value.trim();
    const label = document.getElementById('labelInput').value.trim() || `Link ${new Date().toLocaleDateString('pt-BR')}`;
    if (phone.replace(/\D/g,'').length < 10) { toast('Número inválido.', 'error'); return; }
    if (!msg) { toast('Digite uma mensagem.', 'error'); return; }

    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Criando...';
    const r = await createLink({ label, phone, message: msg, url: buildWaLink(phone, msg) });
    btn.disabled = false; btn.textContent = '✚ Criar link';

    if (!r?.ok) { toast(r?.error || 'Erro ao criar link.', 'error'); return; }
    toast('Link criado com sucesso!', 'success');
    e.target.reset(); charCount.textContent = '0 / 1000'; updatePreview();
    showPanel('list');
  });

  // ── List ──────────────────────────────────────────────────
  document.getElementById('searchInput').addEventListener('input', debounce(renderList, 300));

  async function renderList() {
    const q         = document.getElementById('searchInput').value;
    const container = document.getElementById('linksList');
    container.innerHTML = '<div class="empty-state"><div class="empty-icon">⏳</div><p>Carregando...</p></div>';
    const links = await getLinks(q);

    if (!links.length) {
      container.innerHTML = `<div class="empty-state"><div class="empty-icon">🔗</div><h3>${q?'Nenhum resultado':'Nenhum link criado ainda'}</h3><p>${q?'Tente outra busca.':'Crie seu primeiro link!'}</p></div>`;
      return;
    }

    container.innerHTML = links.map(link => {
      const tUrl = buildTrackUrl(link.id);
      return `<div class="link-item" id="item-${link.id}">
        <div class="link-item-icon">🔗</div>
        <div class="link-item-body">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <span class="link-item-label">${escHtml(link.label)}</span>
            <span class="track-badge">📊 ${link.clicks||0} clique${(link.clicks||0)!==1?'s':''}</span>
          </div>
          <div class="link-item-phone">📱 +55 ${escHtml(link.phone)}</div>
          <div class="link-item-msg">${escHtml(link.message)}</div>
          <div class="link-track-url" title="Clique para copiar o link rastreável"
               onclick="navigator.clipboard.writeText('${escHtml(tUrl)}').then(()=>toast('Link rastreável copiado!','success'))">
            <span class="track-icon">📡</span>${escHtml(tUrl)}
          </div>
          <div class="link-item-date">Criado ${timeAgo(link.created_at)}</div>
          <div class="edit-fields hidden" id="edit-${link.id}">
            <input  class="form-input" id="eLbl-${link.id}" value="${escHtml(link.label)}" placeholder="Nome do link">
            <input  class="form-input" id="ePh-${link.id}"  value="${escHtml(link.phone)}" placeholder="(00) 00000-0000">
            <div class="format-toolbar format-toolbar-sm">
              <button type="button" class="format-btn" onclick="insertFormat(document.getElementById('eMsg-${link.id}'),'bold')" title="Negrito"><strong>B</strong></button>
              <button type="button" class="format-btn" onclick="insertFormat(document.getElementById('eMsg-${link.id}'),'italic')" title="Itálico"><em>I</em></button>
              <button type="button" class="format-btn" onclick="insertFormat(document.getElementById('eMsg-${link.id}'),'strike')" title="Tachado"><s>S</s></button>
              <button type="button" class="format-btn" onclick="insertFormat(document.getElementById('eMsg-${link.id}'),'mono')" title="Mono"><code>M</code></button>
            </div>
            <textarea class="form-input" id="eMsg-${link.id}" rows="3">${escHtml(link.message)}</textarea>
            <div class="edit-actions">
              <button class="btn btn-primary" onclick="saveEdit('${link.id}')">Salvar</button>
              <button class="btn btn-secondary" onclick="cancelEdit('${link.id}')">Cancelar</button>
            </div>
          </div>
        </div>
        <div class="link-item-actions">
          <button class="btn btn-ghost btn-icon" title="Abrir WhatsApp" onclick="openLink('${link.id}','${escHtml(link.url)}')">🚀</button>
          <button class="btn btn-ghost btn-icon" title="Copiar link rastreável" onclick="copyLink('${link.id}')">📋</button>
          <button class="btn btn-ghost btn-icon" title="Duplicar" onclick="dupLink('${link.id}')">🔁</button>
          <button class="btn btn-ghost btn-icon" title="QR Code" onclick="showQR('${link.id}')">📷</button>
          <button class="btn btn-ghost btn-icon" title="Editar" onclick="startEdit('${link.id}')">✏️</button>
          <button class="btn btn-danger btn-icon" title="Excluir" onclick="delLink('${link.id}')">🗑️</button>
        </div>
      </div>`;}).join('');

    links.forEach(l => {
      const ph = document.getElementById(`ePh-${l.id}`);
      if (ph) ph.addEventListener('input', e => { e.target.value = formatPhone(e.target.value); });
    });
  }

  // ── Billing / Planos ────────────────────────────────────────
  let pendingPlanId = null;

  async function renderBilling() {
    const myPlan = session.plan_name || 'Gratuito';
    document.getElementById('currentPlanLabel').innerHTML = `Você está no plano <strong>${escHtml(myPlan)}</strong>.`;

    const plans = await getPlans();
    const cont  = document.getElementById('billingPlanCards');
    cont.innerHTML = plans.map(p => {
      const isCurrent = p.name === myPlan;
      const isFree    = Number(p.price) <= 0;
      return `<div class="plan-card ${p.highlighted?'plan-highlighted':''}">
        ${p.highlighted?'<div class="plan-popular-badge">⭐ Mais popular</div>':''}
        <div class="plan-name">${escHtml(p.name)}</div>
        <div class="plan-price">${isFree?'Grátis':'R$ '+Number(p.price).toFixed(2).replace('.',',')+'/mês'}</div>
        <ul class="plan-features-list">${(p.features||[]).map(f=>`<li>✓ ${escHtml(f)}</li>`).join('')}</ul>
        <button class="btn ${isCurrent?'btn-secondary':'btn-primary'} btn-full" ${isCurrent?'disabled':''} onclick="window._startSubscribe('${p.id}')">
          ${isCurrent ? 'Plano atual' : (isFree ? 'Mudar para este plano' : (p.cta||'Assinar'))}
        </button>
      </div>`;
    }).join('');
  }

  window._startSubscribe = async (planId) => {
    const plans = await getPlans();
    const plan  = plans.find(p => p.id === planId);
    if (!plan) return;

    if (Number(plan.price) <= 0) {
      toast('Plano gratuito ainda não pode ser trocado automaticamente. Fale com o suporte.', 'error');
      return;
    }

    pendingPlanId = planId;
    document.getElementById('cpfInput').value = '';
    document.getElementById('cpfModal').classList.remove('hidden');
  };

  document.getElementById('cpfModalClose')?.addEventListener('click', () => document.getElementById('cpfModal').classList.add('hidden'));
  document.getElementById('boletoModalClose')?.addEventListener('click', () => document.getElementById('boletoModal').classList.add('hidden'));

  document.getElementById('btnConfirmSubscribe')?.addEventListener('click', async () => {
    const cpf = document.getElementById('cpfInput').value.trim();
    if (!cpf) { toast('Informe o CPF ou CNPJ.', 'error'); return; }

    const btn = document.getElementById('btnConfirmSubscribe');
    btn.disabled = true; btn.textContent = 'Gerando boleto...';
    const r = await subscribePlan(pendingPlanId, cpf);
    btn.disabled = false; btn.textContent = 'Gerar boleto';

    if (!r?.ok) { toast(r?.error || 'Erro ao gerar boleto.', 'error'); return; }

    document.getElementById('cpfModal').classList.add('hidden');
    document.getElementById('boletoDueDate').textContent = r.data.due_date || '-';
    document.getElementById('boletoAmount').textContent  = 'R$ ' + Number(r.data.amount).toFixed(2).replace('.',',');
    document.getElementById('boletoLink').href = r.data.boleto_url || r.data.invoice_url || '#';
    document.getElementById('boletoModal').classList.remove('hidden');
  });

  // ── QR Modal ──────────────────────────────────────────────
  let _qrData = null;
  window.showQR = async (id) => {
    const links = await getLinks();
    const link  = links.find(l => l.id === id);
    if (!link) return;
    _qrData = { ...link, trackUrl: buildTrackUrl(link.id) };
    document.getElementById('qrTitle').textContent   = link.label;
    document.getElementById('qrImg').src             = qrUrl(_qrData.trackUrl);
    document.getElementById('qrLinkUrl').textContent = _qrData.trackUrl;
    document.getElementById('qrModal').classList.remove('hidden');
  };
  document.getElementById('qrModalClose')?.addEventListener('click', () => document.getElementById('qrModal').classList.add('hidden'));
  document.getElementById('qrModal')?.addEventListener('click', e => { if (e.target.id==='qrModal') document.getElementById('qrModal').classList.add('hidden'); });

  document.getElementById('btnShareQrLink')?.addEventListener('click', () => {
    if (!_qrData) return;
    if (navigator.share) navigator.share({ title: _qrData.label, url: _qrData.trackUrl }).catch(()=>{});
    else navigator.clipboard.writeText(_qrData.trackUrl).then(() => toast('Link copiado!', 'success'));
  });
  document.getElementById('btnShareQrText')?.addEventListener('click', () => {
    if (!_qrData) return;
    if (navigator.share) navigator.share({ title: _qrData.label, text: _qrData.message }).catch(()=>{});
    else navigator.clipboard.writeText(_qrData.message).then(() => toast('Mensagem copiada!', 'success'));
  });
  document.getElementById('btnSaveQr')?.addEventListener('click', () => {
    if (!_qrData) return;
    fetch(qrUrl(_qrData.trackUrl)).then(r=>r.blob()).then(blob=>{
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = `qr-${_qrData.label.replace(/\s+/g,'-').toLowerCase()}.png`;
      a.click(); URL.revokeObjectURL(a.href);
      toast('QR Code salvo!', 'success');
    }).catch(()=>toast('Erro ao salvar.','error'));
  });

  // ── Actions ───────────────────────────────────────────────
  window.openLink = async (id, url) => {
    await registerClick(id);
    window.open(url, '_blank');
    renderOverview();
  };
  window.copyLink = (id) => {
    navigator.clipboard.writeText(buildTrackUrl(id)).then(() => toast('Link rastreável copiado!', 'success'));
  };
  window.dupLink = async (id) => {
    const links = await getLinks();
    const orig  = links.find(l => l.id === id); if (!orig) return;
    const r = await createLink({ label: `${orig.label} (cópia)`, phone: orig.phone, message: orig.message, url: orig.url });
    if (r?.ok) { toast('Link duplicado!', 'success'); renderList(); renderOverview(); }
  };
  window.delLink = async (id) => {
    if (!confirm('Excluir este link?')) return;
    await removeLink(id);
    toast('Link excluído.', ''); renderList(); renderOverview();
  };
  window.startEdit = (id) => {
    document.querySelectorAll('.edit-fields').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.link-item').forEach(el => el.classList.remove('editing'));
    document.getElementById(`edit-${id}`)?.classList.remove('hidden');
    document.getElementById(`item-${id}`)?.classList.add('editing');
  };
  window.cancelEdit = (id) => {
    document.getElementById(`edit-${id}`)?.classList.add('hidden');
    document.getElementById(`item-${id}`)?.classList.remove('editing');
  };
  window.saveEdit = async (id) => {
    const lbl = document.getElementById(`eLbl-${id}`).value.trim();
    const ph  = document.getElementById(`ePh-${id}`).value.trim();
    const msg = document.getElementById(`eMsg-${id}`).value.trim();
    if (ph.replace(/\D/g,'').length < 10) { toast('Número inválido.', 'error'); return; }
    if (!msg) { toast('Mensagem vazia.', 'error'); return; }
    const r = await updateLink(id, { label: lbl, phone: ph, message: msg, url: buildWaLink(ph, msg) });
    if (r?.ok) { toast('Salvo!', 'success'); renderList(); }
  };

  updatePreview();
}

// ═══════════════════════════════════════════════════════════
//  ADMIN PAGE
// ═══════════════════════════════════════════════════════════
async function initAdmin() {
  const session = await getSession();
  if (!session) return;
  if (session.role !== 'admin') { window.location.href = 'dashboard.html'; return; }

  document.getElementById('navName').textContent   = session.name;
  document.getElementById('navAvatar').textContent = session.name.charAt(0).toUpperCase();
  document.getElementById('btnLogout').addEventListener('click', doLogout);

  document.querySelectorAll('.sidebar-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
      document.querySelectorAll('.admin-panel').forEach(p => p.classList.add('hidden'));
      item.classList.add('active');
      const panel = item.dataset.panel;
      document.getElementById(`panel-${panel}`).classList.remove('hidden');
      if (panel==='overview') renderOverview();
      if (panel==='users')    renderUsers();
      if (panel==='links')    renderAdminLinks();
      if (panel==='plans')    renderPlans();
    });
  });

  renderOverview();

  // ── Overview ──────────────────────────────────────────────
  async function renderOverview() {
    const r = await api('GET', 'admin/stats');
    if (!r?.ok) return;
    const d = r.data;
    document.getElementById('ovUsers').textContent  = d.users;
    document.getElementById('ovLinks').textContent  = d.links;
    document.getElementById('ovClicks').textContent = d.clicks;
    document.getElementById('ovToday').textContent  = d.today;

    const max = d.top[0]?.clicks || 1;
    document.getElementById('topLinksList').innerHTML = d.top.length === 0
      ? '<p class="text-muted">Nenhum clique registrado ainda.</p>'
      : d.top.map(l=>`<div class="top-link-row">
          <div class="top-link-info"><span class="top-link-label">${escHtml(l.label)}</span><span class="top-link-owner text-muted">${escHtml(l.owner_name)}</span></div>
          <div class="top-link-bar-wrap"><div class="top-link-bar" style="width:${Math.max(4,(l.clicks||0)/max*100)}%"></div></div>
          <span class="top-link-count">${l.clicks||0}</span>
        </div>`).join('');

    document.getElementById('recentUsersList').innerHTML = d.recent.map(u=>`
      <div class="recent-user-row">
        <div class="mini-avatar">${u.name.charAt(0).toUpperCase()}</div>
        <div><div style="font-weight:600;font-size:.9rem">${escHtml(u.name)}</div><div class="text-muted">${escHtml(u.email)}</div></div>
        <div style="margin-left:auto;text-align:right">
          <span class="badge ${u.role==='admin'?'badge-admin':'badge-user'}">${u.role}</span>
          <div class="text-muted" style="font-size:.75rem">${timeAgo(u.created_at)}</div>
        </div>
      </div>`).join('');
  }

  // ── Users ─────────────────────────────────────────────────
  let currentPeriod = 'all';
  document.getElementById('periodFilter')?.addEventListener('click', e => {
    const btn = e.target.closest('.period-btn'); if (!btn) return;
    document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); currentPeriod = btn.dataset.period; renderUsers();
  });
  document.getElementById('searchUsers')?.addEventListener('input', debounce(renderUsers, 300));

  async function renderUsers() {
    const q = document.getElementById('searchUsers')?.value || '';
    const r = await api('GET', `admin/users?period=${currentPeriod}&q=${encodeURIComponent(q)}`);
    if (!r?.ok) return;
    const users = r.data;
    const plans = await getPlans();
    const planNames = plans.map(p => p.name);

    // Plan stats
    const planCounts = {};
    users.forEach(u => { const p = u.plan_name||'Gratuito'; planCounts[p]=(planCounts[p]||0)+1; });
    const statsEl = document.getElementById('userPlanStats');
    if (statsEl) {
      statsEl.innerHTML = `<div class="plan-stat-chip"><div><div class="plan-stat-num">${users.length}</div><div class="plan-stat-lbl">Total</div></div></div>
        ${Object.entries(planCounts).map(([n,c])=>`<div class="plan-stat-chip"><div><div class="plan-stat-num">${c}</div><div class="plan-stat-lbl">${escHtml(n)}</div></div></div>`).join('')}`;
    }

    document.getElementById('usersTableBody').innerHTML = !users.length
      ? `<tr><td colspan="7" class="text-muted" style="text-align:center;padding:32px">Nenhum cadastro.</td></tr>`
      : users.map(u => {
          const planOpts = planNames.map(n => `<option value="${escHtml(n)}" ${n===(u.plan_name||'Gratuito')?'selected':''}>${escHtml(n)}</option>`).join('');
          return `<tr>
            <td><div style="display:flex;align-items:center;gap:10px">
              <div class="mini-avatar">${u.name.charAt(0).toUpperCase()}</div>
              <div><div style="font-weight:600">${escHtml(u.name)}</div><div class="text-muted" style="font-size:.8rem">${escHtml(u.email)}</div></div>
            </div></td>
            <td><select class="plan-select" onchange="changeUserPlan(${u.id},this.value)">${planOpts}</select></td>
            <td><strong>${u.link_count}</strong></td>
            <td>${u.total_clicks}</td>
            <td class="text-muted" style="font-size:.83rem">${new Date(u.created_at).toLocaleDateString('pt-BR')}</td>
            <td><span class="badge ${u.active?'badge-active':'badge-inactive'}">${u.active?'Ativo':'Inativo'}</span></td>
            <td><div style="display:flex;gap:6px">
              <button class="btn btn-ghost btn-icon" title="Ver links" onclick="adminViewLinks(${u.id})">🔗</button>
              <button class="btn btn-ghost btn-icon" title="${u.active?'Desativar':'Ativar'}" onclick="toggleUser(${u.id},${u.active})">${u.active?'🔒':'🔓'}</button>
              <button class="btn btn-danger btn-icon" onclick="deleteUser(${u.id})">🗑️</button>
            </div></td>
          </tr>`;}).join('');
  }

  window.changeUserPlan = async (id, name) => {
    await api('PUT', `admin/users/${id}`, { plan_name: name });
    toast(`Plano alterado para ${name}`, 'success'); renderOverview();
  };
  window.toggleUser = async (id, active) => {
    await api('PUT', `admin/users/${id}`, { active: !active });
    renderUsers(); toast('Status atualizado.', 'success');
  };
  window.deleteUser = async (id) => {
    if (!confirm('Excluir usuário e todos os seus links?')) return;
    await api('DELETE', `admin/users/${id}`);
    renderUsers(); renderOverview(); toast('Usuário excluído.', '');
  };
  window.adminViewLinks = (uid) => {
    document.querySelectorAll('.sidebar-item').forEach(i=>i.classList.remove('active'));
    document.querySelectorAll('.admin-panel').forEach(p=>p.classList.add('hidden'));
    document.querySelector('[data-panel="links"]').classList.add('active');
    document.getElementById('panel-links').classList.remove('hidden');
    document.getElementById('filterUser').value = uid;
    renderAdminLinks();
  };

  // ── All Links ─────────────────────────────────────────────
  document.getElementById('searchLinks')?.addEventListener('input', debounce(renderAdminLinks, 300));
  document.getElementById('filterUser')?.addEventListener('change', renderAdminLinks);

  async function renderAdminLinks() {
    const q    = document.getElementById('searchLinks')?.value || '';
    const uid  = document.getElementById('filterUser')?.value  || '';
    const r    = await api('GET', `admin/links?q=${encodeURIComponent(q)}&uid=${uid}`);
    if (!r?.ok) return;
    const links = r.data;

    // Popula filtro de usuários (uma vez)
    const filterEl = document.getElementById('filterUser');
    if (filterEl && filterEl.options.length <= 1) {
      const ru = await api('GET', 'admin/users');
      if (ru?.ok) ru.data.forEach(u => {
        const o = document.createElement('option'); o.value = u.id; o.textContent = u.name; filterEl.appendChild(o);
      });
    }

    document.getElementById('linksTableBody').innerHTML = links.map(l=>`
      <tr>
        <td><div style="font-weight:600">${escHtml(l.label)}</div>
          <div class="text-muted" style="font-size:.8rem;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(l.message)}</div></td>
        <td><div style="color:var(--green-dark);font-weight:500">📱 +55 ${escHtml(l.phone)}</div></td>
        <td>${escHtml(l.owner_name)}</td>
        <td><div style="display:flex;align-items:center;gap:8px">
          <div class="mini-bar-wrap"><div class="mini-bar" style="width:${Math.min(100,(l.clicks||0)*10)}%"></div></div>
          <strong>${l.clicks||0}</strong></div></td>
        <td class="text-muted">${timeAgo(l.created_at)}</td>
        <td><div style="display:flex;gap:6px">
          <button class="btn btn-ghost btn-icon" onclick="window.open('${escHtml(l.url)}','_blank')">🚀</button>
          <button class="btn btn-danger btn-icon" onclick="adminDeleteLink('${l.id}')">🗑️</button>
        </div></td>
      </tr>`).join('') || `<tr><td colspan="6" class="text-muted" style="text-align:center;padding:32px">Nenhum link.</td></tr>`;
  }

  window.adminDeleteLink = async (id) => {
    if (!confirm('Excluir este link?')) return;
    await api('DELETE', `admin/links/${id}`);
    renderAdminLinks(); renderOverview(); toast('Link excluído.','');
  };

  // ── Plans ─────────────────────────────────────────────────
  let editingPlanId = null;

  async function renderPlans() {
    const plans = await getPlans();
    const cont  = document.getElementById('planCards');
    cont.innerHTML = plans.map(p=>`
      <div class="plan-card ${p.highlighted?'plan-highlighted':''}">
        ${p.highlighted?'<div class="plan-popular-badge">⭐ Destaque</div>':''}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <div><div class="plan-name">${escHtml(p.name)}</div>
            <div class="plan-price">${p.price==0?'Grátis':'R$ '+Number(p.price).toFixed(2).replace('.',',')+'/mês'}</div>
          </div>
          <span class="badge ${p.active?'badge-active':'badge-inactive'}">${p.active?'Ativo':'Inativo'}</span>
        </div>
        <div style="font-size:.83rem;color:var(--text-muted);margin-bottom:6px"><strong>Links:</strong> ${p.max_links===-1||p.max_links==-1?'Ilimitados':p.max_links}</div>
        <ul class="plan-features-list">${(p.features||[]).map(f=>`<li>✓ ${escHtml(f)}</li>`).join('')}</ul>
        <div class="plan-card-actions">
          <button class="btn btn-secondary" style="flex:1" onclick="editPlan('${p.id}')">✏️ Editar</button>
          <button class="btn btn-ghost btn-icon" onclick="tglPlan('${p.id}')">${p.active?'🔒':'🔓'}</button>
          <button class="btn btn-danger btn-icon" onclick="delPlan('${p.id}')">🗑️</button>
        </div>
      </div>`).join('');
  }

  window.editPlan = async (id) => {
    const plans = await getPlans();
    const p = plans.find(p=>p.id===id); if (!p) return;
    editingPlanId = id;
    document.getElementById('planFormTitle').textContent = 'Editar plano';
    document.getElementById('planName').value       = p.name;
    document.getElementById('planPrice').value      = p.price;
    document.getElementById('planMaxLinks').value   = p.max_links;
    document.getElementById('planCta').value        = p.cta||'';
    document.getElementById('planOrder').value      = p.sort_order||1;
    document.getElementById('planHighlighted').checked = !!p.highlighted;
    document.getElementById('planFeatures').value   = (p.features||[]).join('\n');
    document.getElementById('planForm').scrollIntoView({ behavior:'smooth' });
  };
  window.tglPlan = async (id) => { await togglePlan(id); renderPlans(); toast('Plano atualizado.','success'); };
  window.delPlan = async (id) => {
    if (!confirm('Excluir este plano?')) return;
    await deletePlan(id); renderPlans(); toast('Plano excluído.','');
  };

  document.getElementById('planForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
      name:        document.getElementById('planName').value.trim(),
      price:       parseFloat(document.getElementById('planPrice').value)||0,
      maxLinks:    parseInt(document.getElementById('planMaxLinks').value)||5,
      cta:         document.getElementById('planCta').value.trim()||'Assinar',
      order:       parseInt(document.getElementById('planOrder').value)||1,
      highlighted: document.getElementById('planHighlighted').checked,
      features:    document.getElementById('planFeatures').value.split('\n').map(s=>s.trim()).filter(Boolean),
    };
    if (!data.name) { toast('Nome do plano obrigatório.','error'); return; }
    const r = await savePlan(data, editingPlanId);
    if (!r?.ok) { toast(r?.error||'Erro.','error'); return; }
    editingPlanId = null;
    document.getElementById('planFormTitle').textContent = '✚ Novo plano';
    e.target.reset(); renderPlans(); toast('Plano salvo!','success');
  });
  document.getElementById('btnCancelPlan')?.addEventListener('click', () => {
    editingPlanId = null;
    document.getElementById('planForm').reset();
    document.getElementById('planFormTitle').textContent = '✚ Novo plano';
  });

  // ── Export ────────────────────────────────────────────────
  document.getElementById('btnExportUsers')?.addEventListener('click', async () => {
    const r = await api('GET', 'admin/users');
    if (r?.ok) downloadJSON(r.data, 'whatslink-usuarios.json');
  });
  document.getElementById('btnExportLinks')?.addEventListener('click', async () => {
    const r = await api('GET', 'admin/links');
    if (r?.ok) downloadJSON(r.data, 'whatslink-links.json');
  });

  function downloadJSON(data, name) {
    const a = Object.assign(document.createElement('a'), {
      href: URL.createObjectURL(new Blob([JSON.stringify(data,null,2)],{type:'application/json'})),
      download: name,
    });
    a.click(); toast('Exportado!','success');
  }
}

// ── Format helper (msg formatting toolbar) ───────────────────
// já definido acima como insertFormat()

// ── Debounce ──────────────────────────────────────────────────
function debounce(fn, ms) {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

// ── Route ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  const page = document.body.dataset.page;
  if (page === 'landing')   initLanding();
  if (page === 'login')     initLogin();
  if (page === 'dashboard') initDashboard();
  if (page === 'admin')     initAdmin();
});
