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
  <title>@yield('title', 'Admin - NewPOS')</title>
  <link rel="icon" href="{{ $layoutLogoUrl }}">
  <link rel="apple-touch-icon" href="{{ $layoutLogoUrl }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
  <style>
    .page-heading .h3, .page-heading h1 {
      margin: 0;
      font-size: 1.35rem;
      line-height: 1.2;
    }
    .page-heading .eyebrow {
      font-size: .68rem;
      line-height: 1.2;
    }
    .page-heading .text-muted {
      font-size: .84rem;
      line-height: 1.45;
    }
    .topbar-tools { flex-wrap: wrap; }
    .topbar-search { flex: 1 1 280px; width: auto; min-width: 220px; }
    .profile-button .avatar-img { border-radius: 50%; }
    .dashboard-content { padding-bottom: 2rem; }
    .sidebar-user .avatar-img { object-fit: cover; }
    .sidebar-user-avatar {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      object-fit: cover;
      background: #fff;
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.75);
    }
    .brand-icon img { width: 100%; height: 100%; display: block; object-fit: contain; border-radius: 1rem; background: #fff; }

    .admin-sidebar {
      background:
        radial-gradient(circle at top, rgba(59, 130, 246, 0.16), transparent 35%),
        linear-gradient(180deg, #020617 0%, #0f172a 46%, #111827 100%);
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 18px 0 42px rgba(15, 23, 42, 0.18);
      color: #e2e8f0;
    }

    .sidebar-header {
      padding: 1.25rem 1rem 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

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

    .brand-mark:hover,
    .brand-mark:focus {
      color: #fff;
    }

    .brand-icon {
      width: 5.5rem;
      height: 5.5rem;
      display: inline-grid;
      place-items: center;
      overflow: hidden;
      padding: .45rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.2rem;
      background: #fff;
      box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.09), 0 20px 40px -28px rgba(15, 23, 42, 0.75);
    }

    .brand-copy {
      display: grid;
      place-items: center;
      gap: .1rem;
      text-align: center;
    }

    .brand-copy .sidebar-identity-label {
      color: #93c5fd;
      font-size: .72rem;
      font-weight: 800;
      letter-spacing: .18em;
      text-transform: uppercase;
    }

    .brand-title {
      font-family: Constantia, Georgia, serif;
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 0;
      color: #fff;
    }

    .brand-status {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      margin-top: .35rem;
      color: #cbd5e1;
      font-size: .78rem;
      font-weight: 600;
    }

    .brand-subtitle {
      font-size: .78rem;
      font-weight: 800;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #94a3b8;
    }

    .sidebar-nav {
      display: grid;
      gap: .9rem;
      padding: 1rem .9rem 1.2rem;
    }

    .admin-nav-group {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1.25rem;
      background: rgba(2, 6, 23, 0.28);
    }

    .admin-nav-group[open] {
      background: rgba(2, 6, 23, 0.42);
      border-color: rgba(148, 163, 184, 0.16);
    }

    .admin-nav-group-summary {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      padding: .95rem 1rem;
      cursor: pointer;
      list-style: none;
      user-select: none;
    }

    .admin-nav-group-summary::-webkit-details-marker {
      display: none;
    }

    .admin-nav-group-meta {
      display: flex;
      align-items: center;
      gap: .75rem;
      min-width: 0;
    }

    .admin-nav-group-symbol {
      width: 2.55rem;
      height: 2.55rem;
      display: inline-grid;
      place-items: center;
      border-radius: .95rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(59, 130, 246, 0.2));
      color: #e2e8f0;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
      flex: 0 0 auto;
    }

    .admin-nav-group-symbol svg {
      width: 1.05rem;
      height: 1.05rem;
    }

    .admin-nav-group-title {
      margin: 0;
      font-size: .78rem;
      font-weight: 800;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #cbd5e1;
    }

    .admin-nav-group-count {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: .25rem .55rem;
      font-size: .68rem;
      font-weight: 800;
      color: #94a3b8;
      background: rgba(15, 23, 42, 0.6);
    }

    .admin-nav-group-icon {
      width: 1rem;
      height: 1rem;
      color: #64748b;
      transition: transform .18s ease, color .18s ease;
      flex: 0 0 auto;
    }

    .admin-nav-group[open] .admin-nav-group-icon {
      transform: rotate(180deg);
      color: #e2e8f0;
    }

    .admin-nav-group-links {
      display: grid;
      gap: .6rem;
      padding: .9rem .9rem 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .admin-nav-link {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: .8rem .95rem;
      border-radius: .95rem;
      border: 1px solid transparent;
      background: rgba(15, 23, 42, 0.2);
      color: #cbd5e1;
      font-size: .92rem;
      font-weight: 700;
      transition: transform .16s ease, background .16s ease, border-color .16s ease, color .16s ease;
    }

    .admin-nav-link:hover {
      color: #fff;
      background: rgba(30, 41, 59, 0.72);
      border-color: rgba(148, 163, 184, 0.18);
      transform: translateX(2px);
    }

    .admin-nav-link-active {
      color: #fff;
      background: linear-gradient(135deg, rgba(37, 99, 235, 0.24), rgba(15, 118, 110, 0.22));
      border-color: rgba(96, 165, 250, 0.35);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .sidebar-user {
      margin: 0 1rem 1rem;
      padding: 1rem;
      display: grid;
      justify-items: center;
      gap: .25rem;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1.15rem;
      background: rgba(15, 23, 42, 0.48);
    }

    .sidebar-user strong {
      color: #fff;
      font-size: 1rem;
      line-height: 1.1;
    }

    .sidebar-user small {
      color: #cbd5e1;
      font-size: .84rem;
    }

    .sidebar-footer {
      display: flex;
      align-items: center;
      gap: .65rem;
      margin-top: auto;
      margin-inline: 1rem;
      padding: 1rem 0;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      color: #cbd5e1;
      font-size: .9rem;
      white-space: nowrap;
    }

    .admin-main {
      margin-left: 18rem;
      min-height: 100vh;
      width: calc(100% - 18rem);
    }

    body.sidebar-mini .admin-main {
      margin-left: 84px;
      width: calc(100% - 84px);
    }

    body.sidebar-mini .admin-sidebar {
      width: 84px;
    }

    body.sidebar-mini .sidebar-header,
    body.sidebar-mini .sidebar-footer {
      margin-inline: .6rem;
      padding-inline: 0;
    }

    body.sidebar-mini .brand-mark {
      padding: .85rem .55rem;
      gap: .6rem;
    }

    body.sidebar-mini .brand-icon {
      width: 3.9rem;
      height: 3.9rem;
      padding: .25rem;
      border-radius: .9rem;
    }

    body.sidebar-mini .brand-copy,
    body.sidebar-mini .admin-nav-group-title,
    body.sidebar-mini .admin-nav-group-count,
    body.sidebar-mini .admin-nav-group-icon,
    body.sidebar-mini .sidebar-status-text,
    body.sidebar-mini .sidebar-footer-text {
      display: none;
    }

    body.sidebar-mini .sidebar-nav {
      gap: .6rem;
      padding-inline: .6rem;
    }

    body.sidebar-mini .admin-nav-group-summary {
      justify-content: center;
      padding: .8rem .55rem;
    }

    body.sidebar-mini .admin-nav-group-meta {
      justify-content: center;
    }

    body.sidebar-mini .admin-nav-group-symbol {
      width: 2.8rem;
      height: 2.8rem;
    }

    body.sidebar-mini .admin-nav-group-links {
      display: none;
    }

    body.sidebar-mini .admin-nav-group {
      border-radius: 1rem;
    }

    .page-heading .page-icon {
      background: #dbeafe;
      color: #1d4ed8;
    }

    .page-heading .section-title i {
      background: #dbeafe;
      color: #1d4ed8;
    }

    .admin-tour-launcher {
      align-items: center;
      border: 0;
      border-radius: 999px;
      background: linear-gradient(135deg, #2563eb, #0f766e);
      color: #fff;
      display: inline-flex;
      font-size: .82rem;
      font-weight: 800;
      gap: .45rem;
      justify-content: center;
      padding: .68rem 1rem;
      box-shadow: 0 12px 24px rgba(37, 99, 235, .22);
    }

    .admin-tour-launcher:hover {
      color: #fff;
      transform: translateY(-1px);
    }

    .admin-tour-highlight {
      border: 3px solid #f59e0b;
      border-radius: 1rem;
      box-shadow: 0 0 0 9999px rgba(15, 23, 42, .62), 0 22px 70px rgba(15, 23, 42, .26);
      display: none;
      pointer-events: none;
      position: fixed;
      transition: all .2s ease;
      z-index: 10050;
    }

    .admin-tour-highlight.is-active {
      display: block;
    }

    .admin-tour-card {
      background: var(--admin-surface, #fff);
      border: 1px solid #dbe4ef;
      border-radius: 8px;
      box-shadow: 0 28px 80px rgba(15, 23, 42, .28);
      color: var(--admin-text, #0f172a);
      display: none;
      max-width: min(390px, calc(100vw - 28px));
      padding: 1.25rem;
      position: fixed;
      width: 390px;
      z-index: 10060;
    }

    .admin-tour-card.is-active {
      display: block;
    }

    .admin-tour-kicker {
      color: #2563eb;
      font-size: .68rem;
      font-weight: 800;
      letter-spacing: .18em;
      text-transform: uppercase;
    }

    .admin-tour-card h3 {
      font-size: 1.25rem;
      font-weight: 800;
      line-height: 1.15;
      margin: .55rem 0 .45rem;
    }

    .admin-tour-card p {
      color: var(--admin-muted, #64748b);
      font-size: .9rem;
      line-height: 1.55;
      margin: 0;
    }

    .admin-tour-actions {
      align-items: center;
      display: flex;
      gap: .75rem;
      justify-content: space-between;
      margin-top: 1.1rem;
    }

    .admin-tour-skip {
      background: transparent;
      border: 0;
      color: var(--admin-muted, #64748b);
      font-size: .8rem;
      font-weight: 800;
      padding: 0;
    }

    .admin-tour-next {
      align-items: center;
      background: linear-gradient(135deg, #2563eb, #0f766e);
      border: 0;
      border-radius: 999px;
      color: #fff;
      display: inline-flex;
      font-size: .8rem;
      font-weight: 800;
      gap: .45rem;
      justify-content: center;
      padding: .7rem 1rem;
    }

    @media (min-width: 1024px) {
      .admin-sidebar {
        width: 18rem;
      }

      .admin-sidebar-nav {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
      }
    }

    @media (max-width: 991.98px) {
      .admin-sidebar {
        width: min(18rem, calc(100vw - 48px));
        transform: translateX(-100%);
      }

      .admin-main {
        margin-left: 0;
        width: 100%;
      }

      body.sidebar-open {
        overflow: hidden;
      }

      body.sidebar-open .admin-sidebar {
        transform: translateX(0);
      }

      body.sidebar-open .sidebar-backdrop {
        display: block;
      }

      .admin-tour-card {
        bottom: 1rem !important;
        left: .85rem !important;
        max-height: 48vh;
        overflow-y: auto;
        right: .85rem !important;
        top: auto !important;
        width: auto;
      }
    }

    @media (max-width: 575.98px) {
      .admin-nav-group-summary,
      .admin-nav-link,
      .sidebar-user,
      .brand-mark {
        border-radius: .9rem;
      }

      .brand-icon {
        width: 4.9rem;
        height: 4.9rem;
        border-radius: 1rem;
      }

      .admin-tour-card h3 {
        font-size: 1.08rem;
      }

      .admin-tour-card p {
        font-size: .82rem;
        line-height: 1.45;
      }
    }
  </style>
  @stack('styles')
</head>
@php
  $user = auth()->user()?->fresh();
  $userName = $user?->name ?? 'Admin';
  $userEmail = $user?->email ?? 'admin@example.com';
  $userAvatar = $user?->avatarUrl();
  $systemName = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? 'NewPOS';
  $sidebarLogoUrl = $layoutLogoUrl;

  $navLinkClass = function (string $pattern): string {
      return 'admin-nav-link'.(request()->routeIs($pattern) ? ' admin-nav-link-active' : '');
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
                  'route' => route('admin.dashboard'),
                  'pattern' => 'admin.dashboard',
              ],
              [
                  'label' => 'AI Assistant',
                  'route' => route('admin.ai.index'),
                  'pattern' => 'admin.ai.*',
              ],
          ],
      ],
      [
          'label' => 'Inventory',
          'icon' => 'editorial',
          'links' => [
              [
                  'label' => 'Products',
                  'route' => route('admin.products.index'),
                  'pattern' => 'admin.products.*',
              ],
              [
                  'label' => 'Categories',
                  'route' => route('admin.categories.index'),
                  'pattern' => 'admin.categories.*',
              ],
          ],
      ],
      [
          'label' => 'Users',
          'icon' => 'users',
          'links' => [
              [
                  'label' => 'Cashiers',
                  'route' => route('admin.staff.index'),
                  'pattern' => 'admin.staff.*',
              ],
          ],
      ],
      [
          'label' => 'Operations',
          'icon' => 'broadcast',
          'links' => [
              [
                  'label' => 'Orders',
                  'route' => route('admin.orders.index'),
                  'pattern' => 'admin.orders.*',
              ],
              [
                  'label' => 'Payments',
                  'route' => route('admin.payments.index'),
                  'pattern' => 'admin.payments.*',
              ],
              [
                  'label' => 'Receipts',
                  'route' => route('admin.receipts.index'),
                  'pattern' => 'admin.receipts.*',
              ],
              [
                  'label' => 'Shifts',
                  'route' => route('admin.shifts.index'),
                  'pattern' => 'admin.shifts.*',
              ],
          ],
      ],
      [
          'label' => 'Reports',
          'icon' => 'presentation',
          'links' => [
              [
                  'label' => 'Orders Reports',
                  'route' => route('admin.orders-reports.index'),
                  'pattern' => 'admin.orders-reports.*',
              ],
              [
                  'label' => 'Payments Reports',
                  'route' => route('admin.payments-reports.index'),
                  'pattern' => 'admin.payments-reports.*',
              ],
              [
                  'label' => 'Sales',
                  'route' => route('admin.sales.index'),
                  'pattern' => 'admin.sales.*',
              ],
              [
                  'label' => 'Audit Trail',
                  'route' => route('admin.audit-trails.index'),
                  'pattern' => 'admin.audit-trails.*',
              ],
          ],
      ],
      [
          'label' => 'System',
          'icon' => 'system',
          'links' => [
              [
                  'label' => 'Settings',
                  'route' => route('admin.settings.index'),
                  'pattern' => 'admin.settings.*',
              ],
          ],
      ],
  ];
@endphp
<body>
  <x-shared.mouse-trail />
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
      <div class="sidebar-header">
        <a class="brand-mark" href="{{ route('admin.dashboard') }}" aria-label="{{ $systemName }} dashboard" data-admin-tour-target="brand">
          <span class="brand-icon">
            <img src="{{ $sidebarLogoUrl }}" alt="{{ $systemName }}">
          </span>
          <span class="brand-copy">
            <span class="brand-title">{{ $systemName }}</span>
            <span class="brand-status">
              <span class="status-dot"></span>
              <span>Command center online</span>
            </span>
          </span>
        </a>
      </div>

      <nav class="sidebar-nav">
        @foreach ($navGroups as $group)
          @continue(empty($group['links']))
          <details class="admin-nav-group" @if ($navGroupIsActive($group)) open @endif data-admin-tour-target="{{ \Illuminate\Support\Str::slug($group['label']) }}">
            <summary class="admin-nav-group-summary">
              <div class="admin-nav-group-meta">
                <span class="admin-nav-group-symbol" aria-hidden="true">
                  @switch($group['icon'])
                    @case('overview')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h5V5H5v7ZM14 19h5v-7h-5v7ZM14 10h5V5h-5v5ZM5 19h5v-5H5v5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                      </svg>
                      @break
                    @case('editorial')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M6 5.75A2.75 2.75 0 0 1 8.75 3h7.5A2.75 2.75 0 0 1 19 5.75v12.5A2.75 2.75 0 0 1 16.25 21h-7.5A2.75 2.75 0 0 1 6 18.25V5.75Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                      </svg>
                      @break
                    @case('broadcast')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 18a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M7.76 7.76a6 6 0 0 1 8.48 0M5.64 5.64a9 9 0 0 1 12.72 0M3.52 3.52a12 12 0 0 1 16.96 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                      </svg>
                      @break
                    @case('presentation')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7.75A2.75 2.75 0 0 1 6.75 5h10.5A2.75 2.75 0 0 1 20 7.75v5.5A2.75 2.75 0 0 1 17.25 16H6.75A2.75 2.75 0 0 1 4 13.25v-5.5Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 16v3M8.5 19h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                      </svg>
                      @break
                    @case('users')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M16.5 19.5v-1.2a3.3 3.3 0 0 0-3.3-3.3H10.8a3.3 3.3 0 0 0-3.3 3.3v1.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M12 12.2a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M18.8 19.5v-1a2.7 2.7 0 0 0-1.9-2.6M15.9 6.6a3.1 3.1 0 0 1 0 6.1" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                      </svg>
                      @break
                    @case('system')
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M10.8 4.36a1 1 0 0 1 2.4 0l.22 1.16a1 1 0 0 0 .82.79l1.2.18a1 1 0 0 1 .56 1.7l-.86.85a1 1 0 0 0-.29.88l.21 1.2a1 1 0 0 1-1.45 1.05L12.56 12a1 1 0 0 0-.93 0l-1.05.55a1 1 0 0 1-1.45-1.05l.2-1.2a1 1 0 0 0-.28-.88l-.87-.86a1 1 0 0 1 .56-1.69l1.2-.19a1 1 0 0 0 .82-.78l.24-1.19Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M12 15.5v4M7 18.5h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                      </svg>
                      @break
                  @endswitch
                </span>
                <p class="admin-nav-group-title">{{ $group['label'] }}</p>
                <span class="admin-nav-group-count">{{ count($group['links']) }}</span>
              </div>
              <svg class="admin-nav-group-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </summary>

            <div class="admin-nav-group-links">
              @foreach ($group['links'] as $link)
                <a class="{{ $link['pattern'] ? $navLinkClass($link['pattern']) : 'admin-nav-link' }}" href="{{ $link['route'] }}">
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

    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Toggle sidebar">
            <span></span>
            <span></span>
            <span></span>
          </button>

          <form class="d-none d-md-flex ms-3 flex-grow-1" role="search" data-admin-tour-target="search">
            <input class="form-control search-input" type="search" placeholder="Search products, orders, reports" aria-label="Search">
          </form>

          <div class="navbar-actions ms-auto">
            <button class="admin-tour-launcher" id="adminTourLauncher" type="button">
              <i class="bi bi-compass"></i>
              <span>Guide</span>
            </button>

            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme" data-admin-tour-target="theme">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>

            <div class="dropdown" data-admin-tour-target="profile">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="avatar-img avatar-sm" src="{{ $userAvatar }}" alt="{{ $userName }}">
                <span class="profile-name d-none d-sm-inline">{{ $userName }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end profile-menu">
                <li>
                  <div class="profile-menu-card">
                    <img class="avatar-img avatar-sm profile-menu-avatar" src="{{ $userAvatar }}" alt="{{ $userName }}">
                    <div>
                      <strong>{{ $userName }}</strong>
                      <span>{{ $userEmail }}</span>
                    </div>
                  </div>
                </li>
                <li><hr class="dropdown-divider profile-menu-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item profile-menu-logout" type="submit">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Sign out</span>
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <div class="page-heading" data-admin-tour-target="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="@yield('page-icon', 'bi bi-speedometer2')" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">@yield('page-eyebrow', 'Overview')</p>
                <h1 class="h3 mb-1">@yield('page-title', 'Dashboard')</h1>
                <p class="text-muted mb-0">@yield('page-description', 'Monitor sales, stock, orders, and operations from one workspace.')</p>
              </div>
            </div>
            <div class="heading-actions">
              @hasSection('page-actions')
                @yield('page-actions')
              @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-repeat"></i> Refresh</a>
                <a class="btn btn-primary btn-sm" href="{{ route('admin.orders.create') }}"><i class="bi bi-plus-circle"></i> New Order</a>
              @endif
            </div>
          </div>

          @yield('content')
        </div>
      </main>
    </div>
  </div>

  @include('components.admin-ai-assistant')

  <div class="admin-tour-highlight" id="adminTourHighlight" aria-hidden="true"></div>
  <div class="admin-tour-card" id="adminTourCard" role="dialog" aria-modal="false" aria-labelledby="adminTourTitle">
    <span class="admin-tour-kicker" id="adminTourCount">Guide</span>
    <h3 id="adminTourTitle">Welcome to NewPOS admin</h3>
    <p id="adminTourText">A quick guide will show the main areas used to manage products, orders, staff, payments, and reports.</p>
    <div class="admin-tour-actions">
      <button type="button" class="admin-tour-skip" id="adminTourSkip">Skip</button>
      <button type="button" class="admin-tour-next" id="adminTourNext">Start</button>
    </div>
  </div>

  <div class="toast-stack">
    @if(session('success'))
      <div class="toast-note success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="toast-note error">{{ $errors->first() }}</div>
    @endif
  </div>

  <script>
    window.adminHMDUser = {
      name: @json($userName),
      avatar: @json($userAvatar)
    };
  </script>
  <script src="{{ asset('assets/adminhmd/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/adminhmd/js/main.js') }}"></script>
  <script src="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>
  <script>
    (() => {
      const storageKey = 'newpos_admin_tour_seen_v1';
      const card = document.getElementById('adminTourCard');
      const highlight = document.getElementById('adminTourHighlight');
      const title = document.getElementById('adminTourTitle');
      const text = document.getElementById('adminTourText');
      const count = document.getElementById('adminTourCount');
      const next = document.getElementById('adminTourNext');
      const skip = document.getElementById('adminTourSkip');
      const launcher = document.getElementById('adminTourLauncher');
      let current = 0;

      if (!card || !highlight || !title || !text || !count || !next || !skip || !launcher) return;

      const steps = [
        { target: '[data-admin-tour-target="brand"]', title: 'Admin command center', text: 'Use this panel to manage inventory, cashier accounts, paid orders, payments, receipts, reports, and POS settings.' },
        { target: '[data-admin-tour-target="overview"]', title: 'Overview', text: 'Start at the dashboard for sales totals, order activity, product counts, cashier activity, and fast links into daily work.' },
        { target: '[data-admin-tour-target="inventory"]', title: 'Inventory', text: 'Create products, import product lists, update categories, adjust stock, and keep sale prices current.' },
        { target: '[data-admin-tour-target="users"]', title: 'Cashier users', text: 'Manage cashier staff records and cashier access for the people who process sales.' },
        { target: '[data-admin-tour-target="operations"]', title: 'Operations', text: 'Review orders, payments, and receipts. Admins can also create paid orders and print customer receipts.' },
        { target: '[data-admin-tour-target="reports"]', title: 'Reports and audit', text: 'Use reports to review order/payment history, sales totals, and audit trail activity.' },
        { target: '[data-admin-tour-target="system"]', title: 'System settings', text: 'Update the admin profile, PIN, login appearance, dark mode, system name, sidebar logo, and other interface defaults.' },
        { target: '[data-admin-tour-target="search"]', title: 'Quick search', text: 'Use the top search box to quickly look for products, orders, and report content from admin pages that support filtering.' },
        { target: '[data-admin-tour-target="theme"]', title: 'Display mode', text: 'Switch between light and dark mode from this button.' },
        { target: '[data-admin-tour-target="profile"]', title: 'Profile menu', text: 'Open your profile menu to confirm the signed-in admin account or sign out.' },
        { target: '#ai-assistant-toggle', title: 'NewPOS AI assistant', text: 'Use the AI assistant for questions about stock, orders, payments, sales trends, staff, reports, and next operational actions.' },
      ].filter((step) => document.querySelector(step.target));

      function isVisibleTarget(element) {
        if (!element) return false;
        const rect = element.getBoundingClientRect();
        const styles = window.getComputedStyle(element);
        return rect.width > 0 && rect.height > 0 && styles.display !== 'none' && styles.visibility !== 'hidden';
      }

      function placeTour(target) {
        if (!isVisibleTarget(target)) {
          highlight.classList.remove('is-active');
          return;
        }

        const rect = target.getBoundingClientRect();
        const padding = 8;
        highlight.style.left = `${Math.max(8, rect.left - padding)}px`;
        highlight.style.top = `${Math.max(8, rect.top - padding)}px`;
        highlight.style.width = `${Math.min(window.innerWidth - 16, rect.width + padding * 2)}px`;
        highlight.style.height = `${rect.height + padding * 2}px`;
        highlight.classList.add('is-active');

        if (window.matchMedia('(max-width: 1024px)').matches) {
          card.style.left = '';
          card.style.top = '';
          return;
        }

        const cardWidth = Math.min(390, window.innerWidth - 28);
        let left = rect.right + 18;
        let top = rect.top;

        if (left + cardWidth > window.innerWidth - 14) left = Math.max(14, rect.left - cardWidth - 18);
        if (top + 245 > window.innerHeight) top = Math.max(14, window.innerHeight - 260);

        card.style.left = `${left}px`;
        card.style.top = `${top}px`;
      }

      function renderStep() {
        if (!steps.length) return closeTour(false);

        const step = steps[current];
        const target = document.querySelector(step.target);
        if (!target) return closeTour(false);

        title.textContent = step.title;
        text.textContent = step.text;
        count.textContent = `${current + 1} of ${steps.length}`;
        next.textContent = current === steps.length - 1 ? 'Finish' : 'Next';
        card.classList.add('is-active');

        if (isVisibleTarget(target)) {
          target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
        }

        window.setTimeout(() => placeTour(target), 260);
      }

      function openTour(force = false) {
        if (!force && localStorage.getItem(storageKey)) return;
        current = 0;
        renderStep();
      }

      function closeTour(markSeen = true) {
        card.classList.remove('is-active');
        highlight.classList.remove('is-active');
        if (markSeen) localStorage.setItem(storageKey, '1');
      }

      next.addEventListener('click', () => {
        if (current >= steps.length - 1) {
          closeTour(true);
          return;
        }

        current += 1;
        renderStep();
      });

      skip.addEventListener('click', () => closeTour(true));
      launcher.addEventListener('click', () => openTour(true));
      window.addEventListener('resize', () => {
        if (card.classList.contains('is-active')) renderStep();
      });
      window.setTimeout(() => openTour(false), 900);
    })();
  </script>
  <script>
    document.addEventListener('input', (event) => {
      const input = event.target;
      if (!input.matches('[data-table-filter]')) return;

      const selector = input.getAttribute('data-table-filter');
      const table = document.querySelector(selector);
      if (!table) return;

      const term = input.value.trim().toLowerCase();
      const rows = table.querySelectorAll('tbody tr[data-filter-row]');
      const emptyRow = table.querySelector('tbody tr[data-filter-empty]');
      let visibleCount = 0;

      rows.forEach((row) => {
        const match = !term || row.textContent.toLowerCase().includes(term);
        row.style.display = match ? '' : 'none';
        if (match) visibleCount += 1;
      });

      if (emptyRow) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
