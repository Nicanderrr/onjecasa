<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin - NewPOS</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Poppins:300,400,500,600,700|Nunito:300,400,600,700|Source+Sans+3:300,400,600,700" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendor/nucleo/css/nucleo.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/css/argon.css?v=1.0.0') }}" rel="stylesheet">
  <style>
    :root { --brand:#16a34a; --brand2:#166534; --ink:#111827; --muted:#6b7280; --line:#dceee2; }
    body { background:#f4f8f5; }
    #magic-trail-canvas { position:fixed; inset:0; width:100%; height:100%; display:block; pointer-events:none; z-index:5000; }
    #sidenav-main { width:265px !important; background:linear-gradient(180deg,#ffffff,#f6fbf7)!important; border-right:1px solid var(--line); box-shadow:8px 0 30px rgba(22,101,52,.08); padding-top:6px; }
    #sidenav-main .navbar-brand { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    #sidenav-main .navbar-brand h2 { margin:0; font-weight:800; letter-spacing:.02em; color:#0f172a; }
    .brand-rotor { width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 8px 18px rgba(22,101,52,.25); animation:brand-rotate 8s linear infinite; }
    @keyframes brand-rotate { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
    #sidenav-main .navbar-nav { margin-top:.3rem!important; gap:6px; }
    #sidenav-main .nav-link { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:12px; color:var(--ink)!important; font-weight:700; font-size:.9rem; border:1px solid transparent; transition:all .2s ease; }
    #sidenav-main .nav-link i { width:26px; height:26px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #d8efe0; color:#1f7a42!important; font-size:.92rem; }
    #sidenav-main .nav-link:hover { background:#eefbf1; border-color:#d2ebdb; transform:translateX(2px); }
    #sidenav-main .nav-link.active { background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff!important; box-shadow:0 8px 20px rgba(22,101,52,.22); }
    #sidenav-main .nav-link.active i { background:rgba(255,255,255,.16); border-color:rgba(255,255,255,.25); color:#fff!important; }
    .main-content { margin-left:265px; }
    .topbar-shell { background:#0f172a; border-radius:14px; padding:12px; margin-top:-18px; margin-bottom:12px; box-shadow:0 12px 28px rgba(15,23,42,.28); }
    .topbar-inner { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .topbar-title { color:#fff; margin:0; font-weight:800; letter-spacing:.02em; }
    .topbar-tools { display:flex; align-items:center; gap:8px; }
    .topbar-search { min-width:260px; max-width:360px; width:32vw; }
    .topbar-search .form-control { height:38px; border-radius:10px; border:1px solid rgba(255,255,255,.25); background:rgba(255,255,255,.08); color:#fff; }
    .quick-btn { border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.08); color:#fff; border-radius:10px; padding:8px 10px; font-size:12px; font-weight:700; }
    .card, .table, .alert, .btn { animation:fadeUp .28s ease; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(6px);} to {opacity:1; transform:none;} }
    .table-responsive { border-radius:12px; border:1px solid #e4efe7; }
    .table thead th { position:sticky; top:0; background:#eff7f2; z-index:1; font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:#4b5563; }
    .table tbody tr:hover { background:#f8fcf9; }
    .status-chip { border-radius:999px; padding:4px 10px; font-size:11px; font-weight:700; display:inline-block; }
    .status-chip.paid { background:#dcfce7; color:#166534; }
    .toast-stack { position:fixed; right:20px; top:20px; z-index:20000; display:flex; flex-direction:column; gap:10px; }
    .toast-note { min-width:280px; max-width:420px; padding:11px 12px; border-radius:12px; color:#fff; box-shadow:0 14px 30px rgba(0,0,0,.2); font-size:13px; }
    .toast-note.success { background:linear-gradient(135deg,#16a34a,#166534); }
    .toast-note.error { background:linear-gradient(135deg,#dc2626,#991b1b); }
    body { font-family: var(--app-font, "Open Sans", sans-serif); font-size: var(--app-font-size, 15px); }
    body.dark-mode { background:#0b1220; color:#e5e7eb; }
    body.dark-mode #sidenav-main { background:linear-gradient(180deg,#0f172a,#111827)!important; border-right:1px solid #1f2937; box-shadow:8px 0 30px rgba(0,0,0,.35); }
    body.dark-mode #sidenav-main .navbar-brand h2 { color:#f3f4f6; }
    body.dark-mode #sidenav-main .nav-link { color:#d1d5db!important; }
    body.dark-mode #sidenav-main .nav-link i { background:#111827; border-color:#374151; color:#86efac!important; }
    body.dark-mode #sidenav-main .nav-link:hover { background:#172033; border-color:#2a3851; }
    body.dark-mode .main-content { color:#e5e7eb; }
    body.dark-mode .topbar-shell { background:#020617; box-shadow:0 12px 28px rgba(0,0,0,.5); }
    body.dark-mode .card, body.dark-mode .table-responsive, body.dark-mode .panel { background:#111827; border-color:#1f2937!important; color:#e5e7eb; }
    body.dark-mode .card-header, body.dark-mode .panel-h { border-color:#1f2937!important; }
    body.dark-mode .table thead th { background:#0f172a; color:#9ca3af; }
    body.dark-mode .table tbody td { border-color:#1f2937!important; }
    body.dark-mode .table tbody tr:hover { background:#0f172a; }
    body.dark-mode .form-control, body.dark-mode .custom-range { background:#0f172a; color:#e5e7eb; border-color:#374151; }
    body.dark-mode .form-control:focus { background:#0f172a; color:#fff; border-color:#22c55e; }
    body.dark-mode .text-muted { color:#9ca3af!important; }
    body.dark-mode .header.bg-gradient-dark { filter: brightness(.88); }
    @media (max-width:991.98px){ .main-content{margin-left:0;} #sidenav-main{width:100%!important;box-shadow:none;} .topbar-search{display:none;} }
  </style>
</head>
@php
  $settingsMap = \Illuminate\Support\Facades\DB::table('pos_settings')
    ->whereIn('key', ['admin_dark_mode','theme_preset','font_family','font_size','sidebar_color','system_name','sidebar_logo'])
    ->pluck('value', 'key');
  $adminDarkMode = ($settingsMap['admin_dark_mode'] ?? '0') === '1';
  $themePreset = $settingsMap['theme_preset'] ?? 'emerald';
  $fontFamilyKey = $settingsMap['font_family'] ?? 'open_sans';
  $fontSizePx = (int) ($settingsMap['font_size'] ?? 15);
  $fontSizePx = max(13, min(19, $fontSizePx));
  $sidebarColor = $settingsMap['sidebar_color'] ?? 'default';

  $themeMap = [
    'emerald' => ['#16a34a', '#166534'],
    'amber' => ['#f59e0b', '#b45309'],
    'rose' => ['#e11d48', '#9f1239'],
    'ocean' => ['#0ea5e9', '#0369a1'],
    'slate' => ['#64748b', '#334155'],
  ];
  $colors = $themeMap[$themePreset] ?? $themeMap['emerald'];

  $fontMap = [
    'open_sans' => '"Open Sans", sans-serif',
    'poppins' => '"Poppins", sans-serif',
    'source_sans' => '"Source Sans 3", sans-serif',
    'nunito' => '"Nunito", sans-serif',
    'system' => 'system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
  ];
  $fontCss = $fontMap[$fontFamilyKey] ?? $fontMap['open_sans'];

  $sidebarMap = [
    'default' => 'linear-gradient(180deg,#ffffff,#f6fbf7)',
    'midnight' => 'linear-gradient(180deg,#0f172a,#111827)',
    'forest' => 'linear-gradient(180deg,#052e16,#14532d)',
    'wine' => 'linear-gradient(180deg,#4c0519,#881337)',
    'indigo' => 'linear-gradient(180deg,#1e1b4b,#312e81)',
  ];
  $sidebarBg = $sidebarMap[$sidebarColor] ?? $sidebarMap['default'];
  $systemName = $settingsMap['system_name'] ?? 'POS';
  $sidebarLogo = $settingsMap['sidebar_logo'] ?? null;
@endphp
<body class="{{ $adminDarkMode ? 'dark-mode' : '' }}" style="--brand: {{ $colors[0] }}; --brand2: {{ $colors[1] }}; --app-font: {!! $fontCss !!}; --app-font-size: {{ $fontSizePx }}px;">
<canvas id="magic-trail-canvas"></canvas>

<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main" style="background: {{ $sidebarBg }} !important;">
  <div class="container-fluid">
    <a class="navbar-brand pt-0" href="{{ route('admin.dashboard') }}">
      @if(!empty($sidebarLogo))
        <img src="{{ asset('assets/admin/img/settings/'.$sidebarLogo) }}" alt="Logo" style="width:34px;height:34px;border-radius:8px;object-fit:cover;box-shadow:0 8px 18px rgba(22,101,52,.25);">
      @else
        <span class="brand-rotor"><i class="fas fa-certificate"></i></span>
      @endif
      <h2>{{ $systemName }}</h2>
    </a>
    <div class="collapse navbar-collapse show">
      <ul class="navbar-nav mt--4">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="ni ni-tv-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.staff.index') }}"><i class="fas fa-user-tie"></i> Employees</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}"><i class="fas fa-list"></i> Products</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="fas fa-bookmark"></i> Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}"><i class="ni ni-cart"></i> Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.payments.index') }}"><i class="ni ni-credit-card"></i> Payments</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.receipts.index') }}"><i class="fas fa-file-invoice-dollar"></i> Receipts</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders-reports.index') }}"><i class="fas fa-funnel-dollar"></i> Orders Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.payments-reports.index') }}"><i class="ni ni-credit-card"></i> Payments Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.index') }}"><i class="fas fa-dollar-sign"></i> Sales</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.audit-trails.index') }}"><i class="fas fa-history"></i> Audit Trail</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.ai.index') }}"><i class="fas fa-robot"></i> AI Assistant</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> Settings</a></li>
      </ul>
      <hr>
      <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="nav-link btn btn-link p-0"><i class="fas fa-sign-out-alt text-danger"></i> Log Out</button>
      </form>
    </div>
  </div>
</nav>

<div class="main-content">
  <nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid"><a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block">Admin Panel</a></div>
  </nav>
  @php
    $heroImage = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'admin_hero_image')->value('value');
    $heroOverlayRaw = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'admin_hero_overlay')->value('value');
    $heroOverlayPct = is_numeric($heroOverlayRaw) ? max(40, min(85, (int) $heroOverlayRaw)) : 72;
    $heroOverlayAlpha = $heroOverlayPct / 100;
    $heroStyle = $heroImage
      ? "background-image: linear-gradient(rgba(15, 23, 42, {$heroOverlayAlpha}), rgba(15, 23, 42, {$heroOverlayAlpha})), url('" . asset('assets/admin/img/settings/' . $heroImage) . "') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important;"
      : "";
  @endphp
  <div id="admin-hero" class="header bg-gradient-dark pb-8 pt-5 pt-md-8" style="{{ $heroStyle }}"></div>
  <div class="container-fluid mt--7">
    <div class="topbar-shell">
      <div class="topbar-inner">
        <h4 class="topbar-title">Operations Console</h4>
        <div class="topbar-tools">
          <div class="topbar-search"><input class="form-control" id="globalQuickSearch" placeholder="Quick search orders, products, staff..."></div>
          <a class="quick-btn" href="{{ route('admin.orders.create') }}"><i class="fas fa-plus"></i> New Order</a>
          <a class="quick-btn" href="{{ route('admin.products.create') }}"><i class="fas fa-box-open"></i> Add Product</a>
        </div>
      </div>
    </div>

    @yield('content')
  </div>
</div>

@include('components.admin-ai-assistant')

<div class="toast-stack">
  @if(session('success')) <div class="toast-note success">{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="toast-note error">{{ $errors->first() }}</div> @endif
</div>

<script src="{{ asset('assets/admin/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/argon.js?v=1.0.0') }}"></script>
<script>
(() => {
  document.querySelectorAll('#sidenav-main .nav-link').forEach((a) => {
    const href = a.getAttribute('href') || '';
    if (href && window.location.href.startsWith(href)) a.classList.add('active');
  });
  setTimeout(() => { document.querySelectorAll('.toast-note').forEach(t => t.remove()); }, 4200);
  const qs = document.getElementById('globalQuickSearch');
  qs?.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('table tbody tr').forEach((tr) => {
      tr.style.display = q === '' || tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
})();

(() => {
  const canvas = document.getElementById('magic-trail-canvas');
  if (!canvas || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const ctx = canvas.getContext('2d');
  const particles = [];
  let pointer = { x: innerWidth/2, y: innerHeight/2, moved: false };
  let hue = 120;
  const maxParticles = 80;
  const resize = () => {
    const dpr = Math.min(devicePixelRatio || 1, 2);
    canvas.width = Math.floor(innerWidth * dpr);
    canvas.height = Math.floor(innerHeight * dpr);
    canvas.style.width = innerWidth + 'px';
    canvas.style.height = innerHeight + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  };
  const spawn = (x, y) => {
    for (let i = 0; i < 2; i++) particles.push({ x, y, vx:(Math.random()-0.5)*1.8, vy:(Math.random()-0.5)*1.8, life:1, size:2+Math.random()*3, hue:hue+Math.random()*28 });
    if (particles.length > maxParticles) particles.splice(0, particles.length - maxParticles);
  };
  const step = () => {
    ctx.clearRect(0, 0, innerWidth, innerHeight);
    if (pointer.moved) { hue = (hue + 0.8) % 180; spawn(pointer.x, pointer.y); }
    for (let i = particles.length - 1; i >= 0; i--) {
      const p = particles[i];
      p.x += p.vx; p.y += p.vy; p.vx *= .985; p.vy *= .985; p.life -= .018; p.size *= .995;
      if (p.life <= .02) { particles.splice(i, 1); continue; }
      const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.size * 4);
      g.addColorStop(0, `hsla(${p.hue},95%,56%,${p.life})`); g.addColorStop(1, `hsla(${p.hue},95%,56%,0)`);
      ctx.fillStyle = g; ctx.beginPath(); ctx.arc(p.x, p.y, p.size * 4, 0, Math.PI * 2); ctx.fill();
    }
    requestAnimationFrame(step);
  };
  addEventListener('mousemove', (e) => { pointer.x = e.clientX; pointer.y = e.clientY; pointer.moved = true; }, { passive:true });
  addEventListener('resize', resize, { passive:true });
  resize(); step();
})();
</script>
</body>
</html>
