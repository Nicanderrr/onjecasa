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
  <title>@yield('title', 'Cashier - NewPOS')</title>
  <link rel="icon" href="{{ $layoutLogoUrl }}">
  <link rel="apple-touch-icon" href="{{ $layoutLogoUrl }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
  <style>
    .page-heading .h3, .page-heading h1 { margin: 0; }
    .cashier-badge { display:inline-flex; align-items:center; gap:.5rem; }
    .sidebar-user .avatar-img { object-fit: cover; }
    .brand-icon img { width: 100%; height: 100%; display: block; object-fit: contain; border-radius: .95rem; background: #fff; }
    .cashier-notification { position: relative; }
    .cashier-notification-count {
      position: absolute;
      top: -.25rem;
      right: -.25rem;
      min-width: 1.1rem;
      height: 1.1rem;
      display: inline-grid;
      place-items: center;
      padding: 0 .25rem;
      border-radius: 999px;
      background: #dc2626;
      color: #fff;
      border: 2px solid #fff;
      font-size: .62rem;
      font-weight: 900;
      line-height: 1;
    }
    .cashier-notification-menu {
      width: min(23rem, calc(100vw - 2rem));
      max-height: 26rem;
      overflow-y: auto;
      border-radius: 8px;
    }
    .cashier-notification-item {
      display: grid;
      gap: .15rem;
      padding: .75rem 1rem;
      border-bottom: 1px solid var(--admin-border);
    }
    .cashier-notification-item:last-child { border-bottom: 0; }
    .cashier-notification-item strong { color: var(--admin-text); font-size: .9rem; line-height: 1.2; }
    .cashier-notification-item span { color: var(--admin-muted); font-size: .78rem; }

    .admin-sidebar {
      background:
        radial-gradient(circle at top, rgba(34, 197, 94, 0.16), transparent 35%),
        linear-gradient(180deg, #020617 0%, #0f172a 46%, #111827 100%);
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 18px 0 42px rgba(15, 23, 42, 0.18);
      color: #e2e8f0;
    }

    .sidebar-header { padding: 1.25rem 1rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .brand-mark {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: .9rem;
      padding: 1rem .9rem 1.15rem;
      border-radius: 1.35rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.84), rgba(30, 41, 59, 0.52));
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
      color: #fff;
    }

    .brand-icon {
      width: 5.5rem;
      height: 5.5rem;
      display: inline-grid;
      place-items: center;
      overflow: hidden;
      padding: .45rem;
      border-radius: 1.3rem;
      background: #fff;
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.75);
    }

    html[data-theme="dark"] .brand-icon {
      background: var(--admin-surface);
    }

    .brand-copy { display: grid; place-items: center; gap: .15rem; text-align: center; }
    .brand-title { font-size: 1.35rem; font-weight: 800; letter-spacing: -0.03em; color: #fff; }
    .brand-subtitle { font-size: .78rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #94a3b8; }

    .sidebar-nav { display: grid; gap: .9rem; padding: 1rem .9rem 1.2rem; }
    .admin-nav-group {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1.25rem;
      background: rgba(2, 6, 23, 0.28);
    }
    .admin-nav-group[open] { background: rgba(2, 6, 23, 0.42); border-color: rgba(148, 163, 184, 0.16); }
    .admin-nav-group-summary { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.95rem 1rem; cursor:pointer; list-style:none; user-select:none; }
    .admin-nav-group-summary::-webkit-details-marker { display:none; }
    .admin-nav-group-meta { display:flex; align-items:center; gap:.75rem; min-width:0; }
    .admin-nav-group-symbol {
      width: 2.55rem; height: 2.55rem; display:inline-grid; place-items:center; border-radius:.95rem;
      border:1px solid rgba(255,255,255,.08); background: linear-gradient(135deg, rgba(245,158,11,.18), rgba(34,197,94,.2));
      color:#e2e8f0; box-shadow: inset 0 1px 0 rgba(255,255,255,.05); flex:0 0 auto;
    }
    .admin-nav-group-symbol svg { width:1.05rem; height:1.05rem; }
    .admin-nav-group-title { margin:0; font-size:.78rem; font-weight:800; letter-spacing:.18em; text-transform:uppercase; color:#cbd5e1; }
    .admin-nav-group-count {
      display:inline-flex; align-items:center; border-radius:999px; border:1px solid rgba(255,255,255,.1);
      padding:.25rem .55rem; font-size:.68rem; font-weight:800; color:#94a3b8; background:rgba(15,23,42,.6);
    }
    .admin-nav-group-icon { width:1rem; height:1rem; color:#64748b; transition:transform .18s ease, color .18s ease; flex:0 0 auto; }
    .admin-nav-group[open] .admin-nav-group-icon { transform: rotate(180deg); color:#e2e8f0; }
    .admin-nav-group-links { display:grid; gap:.6rem; padding:.9rem .9rem 1rem; border-top:1px solid rgba(255,255,255,.06); }
    .admin-nav-link {
      display:flex; align-items:center; gap:.75rem; padding:.8rem .95rem; border-radius:.95rem; border:1px solid transparent;
      background: rgba(15,23,42,.2); color:#cbd5e1; font-size:.92rem; font-weight:700;
      transition: transform .16s ease, background .16s ease, border-color .16s ease, color .16s ease;
    }
    .admin-nav-link:hover { color:#fff; background:rgba(30,41,59,.72); border-color:rgba(148,163,184,.18); transform:translateX(2px); }
    .admin-nav-link-active { color:#fff; background:linear-gradient(135deg, rgba(37,99,235,.24), rgba(15,118,110,.22)); border-color:rgba(96,165,250,.35); box-shadow: inset 0 1px 0 rgba(255,255,255,.06); }
    .sidebar-user {
      margin: 0 1rem 1rem; padding: 1rem; display:grid; justify-items:center; gap:.25rem; text-align:center;
      border:1px solid rgba(255,255,255,.08); border-radius:1.15rem; background:rgba(15,23,42,.48);
    }
    .sidebar-user strong { color:#fff; font-size:1rem; line-height:1.1; }
    .sidebar-user small { color:#cbd5e1; font-size:.84rem; }
    .sidebar-footer {
      display:flex; align-items:center; gap:.65rem; margin-top:auto; margin-inline:1rem; padding:1rem 0;
      border-top:1px solid rgba(255,255,255,.08); color:#cbd5e1; font-size:.9rem; white-space:nowrap;
    }
    .cashier-floating-sale {
      position: fixed;
      right: 1.35rem;
      bottom: 1.35rem;
      z-index: 1040;
      display: inline-flex;
      align-items: center;
      gap: .6rem;
      min-height: 3.25rem;
      padding: .85rem 1.1rem;
      border-radius: 999px;
      color: #fff;
      background: linear-gradient(135deg, #0f766e, #16a34a);
      border: 1px solid rgba(255, 255, 255, .16);
      box-shadow: 0 18px 38px rgba(15, 118, 110, .34);
      font-weight: 900;
      text-decoration: none;
      transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
    }
    .cashier-floating-sale:hover,
    .cashier-floating-sale:focus {
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 22px 48px rgba(15, 118, 110, .42);
      background: linear-gradient(135deg, #115e59, #15803d);
    }
    .cashier-floating-sale i { font-size: 1.15rem; }
    .admin-main { margin-left: 18rem; min-height: 100vh; width: calc(100% - 18rem); }
    .page-heading .page-icon, .page-heading .section-title i { background:#dcfce7; color:#166534; }
    @media (min-width: 1024px) {
      .admin-sidebar { width: 18rem; }
      .admin-sidebar-nav { flex:1 1 auto; min-height:0; overflow-y:auto; overscroll-behavior:contain; }
    }
    @media (max-width: 991.98px) {
      .admin-sidebar { width: min(18rem, calc(100vw - 48px)); transform: translateX(-100%); }
      .admin-main { margin-left: 0; width: 100%; }
      body.sidebar-open { overflow: hidden; }
      body.sidebar-open .admin-sidebar { transform: translateX(0); }
      body.sidebar-open .sidebar-backdrop { display:block; }
    }
    @media (max-width: 575.98px) {
      .admin-nav-group-summary, .admin-nav-link, .sidebar-user, .brand-mark { border-radius:.9rem; }
      .brand-icon { width:4.9rem; height:4.9rem; border-radius:1rem; }
      .cashier-floating-sale {
        right: 1rem;
        bottom: 1rem;
        width: 3.35rem;
        height: 3.35rem;
        justify-content: center;
        padding: 0;
      }
      .cashier-floating-sale span { display: none; }
    }
  </style>
  @stack('styles')
</head>
@php
  $user = auth()->user()?->fresh();
  $userName = $user?->name ?? 'Cashier';
  $userEmail = $user?->email ?? 'cashier@example.com';
  $userAvatar = $user?->avatarUrl();
  $systemName = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? 'NewPOS';
  $sidebarLogoUrl = $layoutLogoUrl;
  $cashierLowStockProducts = \Illuminate\Support\Facades\DB::table('pos_products')
      ->whereColumn('stock', '<=', 'low_stock_threshold')
      ->orderBy('stock')
      ->orderBy('name')
      ->limit(8)
      ->get();
  $cashierLowStockCount = \Illuminate\Support\Facades\DB::table('pos_products')
      ->whereColumn('stock', '<=', 'low_stock_threshold')
      ->count();
  $cashierPageIs = function (string $page): bool {
      return request()->routeIs('cashier.pages.show') && request()->route('page') === $page;
  };

  $navLinkClass = function (array $link) use ($cashierPageIs): string {
      $isActive = ! empty($link['page'])
          ? $cashierPageIs($link['page'])
          : request()->routeIs($link['pattern'] ?? '');

      return 'admin-nav-link'.($isActive ? ' admin-nav-link-active' : '');
  };

  $navGroupIsActive = function (array $group): bool {
      foreach ($group['links'] as $link) {
          if (! empty($link['pattern']) && request()->routeIs($link['pattern'])) {
              return true;
          }
      }

      return false;
  };

  $navGroups = [
      [
          'label' => 'Overview',
          'icon' => 'overview',
          'links' => [
              [
                  'label' => 'Dashboard',
                  'route' => route('cashier.pages.show', 'dashboard'),
                  'page' => 'dashboard',
              ],
              [
                  'label' => 'Make Orders',
                  'route' => route('cashier.sales.create'),
                  'pattern' => 'cashier.sales.*',
              ],
          ],
      ],
      [
          'label' => 'Transactions',
          'icon' => 'broadcast',
          'links' => [
              [
                  'label' => 'Payments',
                  'route' => route('cashier.pages.show', 'payments'),
                  'page' => 'payments',
              ],
              [
                  'label' => 'Receipts',
                  'route' => route('cashier.pages.show', 'receipts'),
                  'page' => 'receipts',
              ],
              [
                  'label' => 'Orders',
                  'route' => route('cashier.pages.show', 'orders'),
                  'page' => 'orders',
              ],
          ],
      ],
      [
          'label' => 'Catalog',
          'icon' => 'editorial',
          'links' => [
              [
                  'label' => 'Products',
                  'route' => route('cashier.pages.show', 'products'),
                  'page' => 'products',
              ],
              [
                  'label' => 'Orders Reports',
                  'route' => route('cashier.pages.show', 'orders-reports'),
                  'page' => 'orders-reports',
              ],
              [
                  'label' => 'Payments Reports',
                  'route' => route('cashier.pages.show', 'payments-reports'),
                  'page' => 'payments-reports',
              ],
          ],
      ],
      [
          'label' => 'System',
          'icon' => 'system',
          'links' => [
              [
                  'label' => 'Settings',
                  'route' => route('cashier.pages.show', 'settings'),
                  'page' => 'settings',
              ],
          ],
      ],
  ];
@endphp
<body>
  <x-shared.mouse-trail />
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <aside class="admin-sidebar" id="cashierSidebar" aria-label="Cashier navigation">
      <div class="sidebar-header">
        <a class="brand-mark" href="{{ route('cashier.pages.show', 'dashboard') }}" aria-label="{{ $systemName }} cashier dashboard">
          <span class="brand-icon">
            <img src="{{ $sidebarLogoUrl }}" alt="{{ $systemName }}">
          </span>
          <span class="brand-copy">
            <span class="brand-title">{{ $systemName }}</span>
            <span class="brand-subtitle">Cashier Command Center</span>
          </span>
        </a>
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
                    @case('editorial')
                      <svg viewBox="0 0 24 24" fill="none"><path d="M6 5.75A2.75 2.75 0 0 1 8.75 3h7.5A2.75 2.75 0 0 1 19 5.75v12.5A2.75 2.75 0 0 1 16.25 21h-7.5A2.75 2.75 0 0 1 6 18.25V5.75Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
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

      <div class="sidebar-user">
        <img class="avatar-img avatar-md sidebar-user-avatar" src="{{ $userAvatar }}" alt="{{ $userName }}">
        <strong>{{ $userName }}</strong>
        <small>{{ $userEmail }}</small>
      </div>

      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span class="sidebar-footer-text">Ready for service</span>
      </div>
    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="cashierSidebar" aria-expanded="true" aria-label="Toggle sidebar">
            <span></span>
            <span></span>
            <span></span>
          </button>

          <div class="ms-3 cashier-badge d-none d-md-inline-flex">
            <span class="page-icon"><i class="bi bi-bag-check-fill" aria-hidden="true"></i></span>
            <strong class="mb-0">Cashier Panel</strong>
          </div>

          <div class="navbar-actions ms-auto">
            <div class="dropdown">
              <button class="icon-button cashier-notification" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Low stock notifications" title="Low stock notifications">
                <i class="bi {{ $cashierLowStockCount > 0 ? 'bi-bell-fill' : 'bi-bell' }}" aria-hidden="true"></i>
                @if($cashierLowStockCount > 0)
                  <span class="cashier-notification-count">{{ $cashierLowStockCount > 99 ? '99+' : $cashierLowStockCount }}</span>
                @endif
              </button>
              <div class="dropdown-menu dropdown-menu-end cashier-notification-menu">
                <h6 class="dropdown-header">Low Stock Alerts</h6>
                @forelse($cashierLowStockProducts as $product)
                  <div class="cashier-notification-item">
                    <strong>{{ $product->name }}</strong>
                    <span>{{ $product->code }} · Stock {{ $product->stock }} · Alert at {{ $product->low_stock_threshold }}</span>
                  </div>
                @empty
                  <div class="px-3 py-3 text-muted small">No low-stock products right now.</div>
                @endforelse
                <div class="dropdown-divider"></div>
                <a class="dropdown-item small" href="{{ route('cashier.pages.show', 'products') }}">View products</a>
              </div>
            </div>
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>
            <div class="dropdown">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="avatar-img avatar-sm" src="{{ $userAvatar }}" alt="{{ $userName }}">
                <span class="profile-name d-none d-sm-inline">{{ $userName }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text">{{ $userEmail }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Sign out</button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-bag-check-fill" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">Workspace</p>
                <h1 class="h3 mb-1">Cashier</h1>
                <p class="text-muted mb-0">Process orders, payments, receipts, and product lookups.</p>
              </div>
            </div>
          </div>

          @yield('content')
        </div>
      </main>
    </div>
  </div>

  @unless(request()->routeIs('cashier.sales.create'))
    <a class="cashier-floating-sale" href="{{ route('cashier.sales.create') }}" aria-label="Make sale" title="Make sale">
      <i class="bi bi-cart-plus" aria-hidden="true"></i>
      <span>Make Sale</span>
    </a>
  @endunless

  <div class="toast-stack">
    @if(session('success'))
      <div class="toast-note success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="toast-note error">{{ $errors->first() }}</div>
    @endif
  </div>

  @include('components.admin-ai-assistant')

  <script>
    window.adminHMDUser = {
      name: @json($userName),
      workspace: 'Cashier Workspace',
      avatar: @json($userAvatar)
    };
  </script>
  <script src="{{ asset('assets/adminhmd/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/adminhmd/js/main.js') }}"></script>
  <script src="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
