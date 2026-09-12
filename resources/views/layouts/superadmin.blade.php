<!doctype html>
<html lang="en" data-theme="light">
@php
  $layoutLogoSetting = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'sidebar_logo')->value('value');
  $layoutLogoUrl = $layoutLogoSetting
    ? asset('assets/admin/img/settings/' . $layoutLogoSetting)
    : asset('assets/adminhmd/images/brand/logo/logo-icon.svg');
@endphp
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title', 'Superadmin - NewPOS')</title>
  <link rel="icon" href="{{ $layoutLogoUrl }}">
  <link rel="apple-touch-icon" href="{{ $layoutLogoUrl }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
  <style>
    .super-sidebar {
      background:
        radial-gradient(circle at top, rgba(34, 197, 94, 0.16), transparent 35%),
        linear-gradient(180deg, #020617 0%, #0f172a 46%, #111827 100%);
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 18px 0 42px rgba(15, 23, 42, 0.18);
      color: #e2e8f0;
    }
    .super-brand {
      color: #fff;
      border-color: rgba(255, 255, 255, 0.1);
      background: linear-gradient(135deg, rgba(15, 23, 42, .95), rgba(30, 41, 59, .9));
    }
    .super-brand:hover { color: #fff; }
    .super-chip {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      margin-top: .4rem;
      color: #94a3b8;
      font-size: .78rem;
      font-weight: 800;
    }
    .super-link {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: .82rem .95rem;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: .95rem;
      background: rgba(15, 23, 42, .44);
      color: #dbeafe;
      font-weight: 800;
      text-decoration: none;
    }
    .super-link:hover,
    .super-link.active {
      color: #fff;
      border-color: rgba(148, 163, 184, .18);
      background: rgba(30, 41, 59, .72);
      text-decoration: none;
    }
    .super-link i { color: #93c5fd; }
    .super-switch {
      display: grid;
      gap: .5rem;
      margin: 1rem .9rem 0;
    }
    .super-shell .page-heading .page-icon,
    .super-shell .section-title i {
      background: #dcfce7;
      color: #166534;
    }
    .super-sidebar .brand-mark {
      border-radius: 1.35rem;
    }
    .super-sidebar .brand-icon {
      width: 5.5rem;
      height: 5.5rem;
      padding: .45rem;
      border-radius: 1.3rem;
      background: #fff !important;
      background-image: none !important;
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.75);
      overflow: hidden;
      isolation: isolate;
    }
    .super-sidebar .brand-icon img {
      width: 100%;
      height: 100%;
      display: block;
      border-radius: .95rem;
      object-fit: contain;
      background: #fff !important;
      opacity: 1;
      mix-blend-mode: normal;
    }
    .super-sidebar .brand-title {
      font-family: inherit;
      font-size: 1.35rem;
      letter-spacing: -0.03em;
    }
    .super-sidebar .brand-subtitle {
      font-size: .78rem;
      font-weight: 800;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #94a3b8;
    }
    .super-sidebar .sidebar-header {
      padding: 1.25rem 1rem 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .super-sidebar .sidebar-nav {
      display: grid;
      gap: .9rem;
      padding: 1rem .9rem 1.2rem;
    }
    .super-sidebar .admin-nav-group {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1.25rem;
      background: rgba(2, 6, 23, 0.28);
    }
    .super-sidebar .admin-nav-group[open] {
      background: rgba(2, 6, 23, 0.42);
      border-color: rgba(148, 163, 184, 0.16);
    }
    .super-sidebar .admin-nav-group-summary {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:.75rem;
      padding:.95rem 1rem;
      cursor:pointer;
      list-style:none;
      user-select:none;
    }
    .super-sidebar .admin-nav-group-summary::-webkit-details-marker { display:none; }
    .super-sidebar .admin-nav-group-meta { display:flex; align-items:center; gap:.75rem; min-width:0; }
    .super-sidebar .admin-nav-group-symbol {
      width: 2.55rem;
      height: 2.55rem;
      display:inline-grid;
      place-items:center;
      border-radius:.95rem;
      border:1px solid rgba(255,255,255,.08);
      background: linear-gradient(135deg, rgba(245,158,11,.18), rgba(34,197,94,.2));
      color:#e2e8f0;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.05);
      flex:0 0 auto;
    }
    .super-sidebar .admin-nav-group-symbol svg { width:1.05rem; height:1.05rem; }
    .super-sidebar .admin-nav-group-title {
      margin:0;
      font-size:.78rem;
      font-weight:800;
      letter-spacing:.18em;
      text-transform:uppercase;
      color:#cbd5e1;
    }
    .super-sidebar .admin-nav-group-count {
      display:inline-flex;
      align-items:center;
      border-radius:999px;
      border:1px solid rgba(255,255,255,.1);
      padding:.25rem .55rem;
      font-size:.68rem;
      font-weight:800;
      color:#94a3b8;
      background:rgba(15,23,42,.6);
    }
    .super-sidebar .admin-nav-group-icon {
      width:1rem;
      height:1rem;
      color:#64748b;
      transition:transform .18s ease, color .18s ease;
      flex:0 0 auto;
    }
    .super-sidebar .admin-nav-group[open] .admin-nav-group-icon {
      transform: rotate(180deg);
      color:#e2e8f0;
    }
    .super-sidebar .admin-nav-group-links {
      display:grid;
      gap:.6rem;
      padding:.9rem .9rem 1rem;
      border-top:1px solid rgba(255,255,255,.06);
    }
    .super-sidebar .admin-nav-link {
      display:flex;
      align-items:center;
      gap:.75rem;
      padding:.8rem .95rem;
      border-radius:.95rem;
      border:1px solid transparent;
      background: rgba(15,23,42,.2);
      color:#cbd5e1;
      font-size:.92rem;
      font-weight:700;
      transition: transform .16s ease, background .16s ease, border-color .16s ease, color .16s ease;
      text-decoration:none;
    }
    .super-sidebar .admin-nav-link:hover {
      color:#fff;
      background:rgba(30,41,59,.72);
      border-color:rgba(148,163,184,.18);
      transform:translateX(2px);
      text-decoration:none;
    }
    .super-sidebar .admin-nav-link-active {
      color:#fff;
      background:linear-gradient(135deg, rgba(37,99,235,.24), rgba(15,118,110,.22));
      border-color:rgba(96,165,250,.35);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
    }
    .super-sidebar .sidebar-user {
      margin: 0 1rem 1rem;
      padding: 1rem;
      display: grid;
      justify-items: center;
      gap: .25rem;
      text-align: center;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 1.15rem;
      background: rgba(15,23,42,.48);
    }
    .super-sidebar .sidebar-user strong { color: #fff; font-size: 1rem; line-height: 1.1; }
    .super-sidebar .sidebar-user small { color: #cbd5e1; font-size: .84rem; }
    .super-sidebar .sidebar-user-avatar {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      object-fit: cover;
      background: #fff;
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.75);
    }
    .super-sidebar .sidebar-footer {
      display:flex;
      align-items:center;
      gap:.65rem;
      margin-top:auto;
      margin-inline:1rem;
      padding:1rem 0;
      border-top:1px solid rgba(255,255,255,.08);
      color:#cbd5e1;
      font-size:.9rem;
      white-space:nowrap;
    }
    .super-card {
      border: 1px solid var(--admin-border);
      border-radius: 8px;
      background: var(--admin-surface);
      box-shadow: var(--admin-shadow-sm);
    }
    .super-card-pad { padding: 1rem; }
    .super-actions { display: flex; flex-wrap: wrap; gap: .55rem; }
    .super-bars {
      height: 250px;
      display: flex;
      align-items: end;
      gap: .5rem;
      padding: 1rem;
      border: 1px solid var(--admin-border);
      border-radius: 8px;
      background: var(--admin-surface-soft);
    }
    .super-bar {
      display: grid;
      grid-template-rows: auto 1fr auto;
      gap: .4rem;
      align-items: end;
      justify-items: center;
      flex: 1 1 0;
      height: 100%;
      color: var(--admin-muted);
      font-size: .76rem;
      font-weight: 800;
    }
    .super-bar-fill {
      width: 100%;
      min-height: 8px;
      border-radius: 8px 8px 0 0;
      background: linear-gradient(180deg, #f59e0b, #2563eb);
    }
    @media (max-width: 991.98px) {
      .admin-sidebar { width: min(18rem, calc(100vw - 48px)); transform: translateX(-100%); }
      .admin-main { margin-left: 0; width: 100%; }
      body.sidebar-open .admin-sidebar { transform: translateX(0); }
      body.sidebar-open .sidebar-backdrop { display: block; }
    }
  </style>
  @stack('styles')
</head>
@php
  $user = auth()->user()?->fresh();
  $systemName = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? 'NewPOS';
  $avatar = $user?->avatarUrl();
  $sidebarLogoUrl = $layoutLogoUrl;
  $navGroups = [
    [
      'label' => 'Overview',
      'icon' => 'overview',
      'links' => [
        [
          'label' => 'Command Center',
          'route' => route('superadmin.dashboard'),
          'pattern' => 'superadmin.dashboard*',
        ],
      ],
    ],
    [
      'label' => 'Governance',
      'icon' => 'broadcast',
      'links' => [
        [
          'label' => 'Users & Roles',
          'route' => route('superadmin.users'),
          'pattern' => 'superadmin.users*',
        ],
        [
          'label' => 'Audit Logs',
          'route' => route('superadmin.audit'),
          'pattern' => 'superadmin.audit',
        ],
        [
          'label' => 'Security Center',
          'route' => route('superadmin.security'),
          'pattern' => 'superadmin.security',
        ],
      ],
    ],
    [
      'label' => 'System',
      'icon' => 'system',
      'links' => [
        [
          'label' => 'System Settings',
          'route' => route('superadmin.settings'),
          'pattern' => 'superadmin.settings',
        ],
        [
          'label' => 'Maintenance',
          'route' => route('superadmin.maintenance'),
          'pattern' => 'superadmin.maintenance',
        ],
      ],
    ],
  ];
  $navLinkClass = function (array $link): string {
      return 'admin-nav-link'.(request()->routeIs($link['pattern'] ?? '') ? ' admin-nav-link-active' : '');
  };
  $navGroupIsActive = function (array $group): bool {
      foreach ($group['links'] as $link) {
          if (! empty($link['pattern']) && request()->routeIs($link['pattern'])) {
              return true;
          }
      }

      return false;
  };
@endphp
<body class="super-shell">
  <x-shared.mouse-trail />
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>
    <aside class="admin-sidebar super-sidebar" id="superSidebar" aria-label="Superadmin navigation">
      <div class="sidebar-header">
        <div class="sidebar-user">
          <img class="sidebar-user-avatar" src="{{ $avatar }}" alt="{{ $user?->name ?? 'Superadmin' }}">
          <strong>{{ $user?->name }}</strong>
          <small>{{ $user?->email }}</small>
        </div>
      </div>

      <div class="super-switch">
        <a class="super-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-shop"></i> POS Admin</a>
        <a class="super-link" href="{{ route('cashier.pages.show', 'dashboard') }}"><i class="bi bi-bag-check"></i> Cashier Side</a>
      </div>

      <nav class="sidebar-nav">
        @foreach ($navGroups as $group)
          @continue(empty($group['links']))
          <details class="admin-nav-group" @if ($navGroupIsActive($group)) open @endif>
            <summary class="admin-nav-group-summary">
              <div class="admin-nav-group-meta">
                <span class="admin-nav-group-symbol" aria-hidden="true">
                  @switch($group['icon'])
                    @case('overview')
                      <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h5V5H5v7ZM14 19h5v-7h-5v7ZM14 10h5V5h-5v5ZM5 19h5v-5H5v5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                      @break
                    @case('broadcast')
                      <svg viewBox="0 0 24 24" fill="none"><path d="M12 18a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8"/><path d="M7.76 7.76a6 6 0 0 1 8.48 0M5.64 5.64a9 9 0 0 1 12.72 0M3.52 3.52a12 12 0 0 1 16.96 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                      @break
                    @case('system')
                      <svg viewBox="0 0 24 24" fill="none"><path d="M10.8 4.36a1 1 0 0 1 2.4 0l.22 1.16a1 1 0 0 0 .82.79l1.2.18a1 1 0 0 1 .56 1.7l-.86.85a1 1 0 0 0-.29.88l.21 1.2a1 1 0 0 1-1.45 1.05L12.56 12a1 1 0 0 0-.93 0l-1.05.55a1 1 0 0 1-1.45-1.05l.2-1.2a1 1 0 0 0-.28-.88l-.87-.86a1 1 0 0 1 .56-1.69l1.2-.19a1 1 0 0 0 .82-.78l.24-1.19Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 15.5v4M7 18.5h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                      @break
                  @endswitch
                </span>
                <p class="admin-nav-group-title">{{ $group['label'] }}</p>
                <span class="admin-nav-group-count">{{ count($group['links']) }}</span>
              </div>
              <svg class="admin-nav-group-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </summary>
            <div class="admin-nav-group-links">
              @foreach ($group['links'] as $link)
                <a class="{{ $navLinkClass($link) }}" href="{{ $link['route'] }}">
                  {{ $link['label'] }}
                </a>
              @endforeach
            </div>
          </details>
        @endforeach
      </nav>

      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span class="sidebar-footer-text">Ready for service</span>
      </div>
    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="superSidebar" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
          <div class="ms-3 d-none d-md-flex align-items-center gap-2"><span class="page-icon"><i class="bi bi-shield-lock"></i></span><strong>Superadmin</strong></div>
          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme"><i class="bi bi-moon-stars" data-theme-icon></i></button>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i> Sign out</button></form>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="@yield('page-icon', 'bi bi-shield-lock')" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">@yield('page-eyebrow', 'Full Control')</p>
                <h1 class="h3 mb-1">@yield('page-title', 'Superadmin')</h1>
                <p class="text-muted mb-0">@yield('page-description', 'Control system access, security, audit logs, and maintenance.')</p>
              </div>
            </div>
            <div class="heading-actions">@yield('page-actions')</div>
          </div>

          @yield('content')
        </div>
      </main>
    </div>
  </div>

  <div class="toast-stack">
    @if(session('success'))<div class="toast-note success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="toast-note error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="toast-note error">{{ $errors->first() }}</div>@endif
  </div>

  @include('components.admin-ai-assistant')

  <script src="{{ asset('assets/adminhmd/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/adminhmd/js/main.js') }}"></script>
  <script src="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
