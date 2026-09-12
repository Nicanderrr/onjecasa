<!doctype html>
<html lang="en" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>User Login - {{ $systemName }}</title>
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/style.css') }}">
  <link rel="icon" href="{{ $favicon }}">
  <style>
    html, body {
      min-height: 100%;
    }

    body.user-login-body {
      min-height: 100vh;
      background: #f8fafc;
      color: #0f172a;
    }

    .user-login-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 420px;
    }

    .user-login-visual {
      position: relative;
      min-height: 100vh;
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      color: #fff;
      background: url('{{ $cashierLoginMedia['url'] }}') center/cover no-repeat;
    }

    .user-login-media-video {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 0;
    }

    .user-login-overlay {
      position: absolute;
      inset: 0;
      z-index: 1;
      {{ $cashierOverlayStyle ?? 'background: linear-gradient(160deg, rgba(13,148,136, 0.72), rgba(2, 6, 23, 0.55));' }}
      pointer-events: none;
    }

    .user-login-visual > :not(.user-login-media-video):not(.user-login-overlay) {
      position: relative;
      z-index: 2;
    }

    .user-login-brand {
      display: inline-flex;
      align-items: center;
      gap: 0.9rem;
      font-weight: 900;
      font-size: 1.15rem;
      color: #fff;
      text-decoration: none;
    }

    .user-login-brand:hover,
    .user-login-brand:focus {
      color: #fff;
    }

    .user-login-brand img {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.14);
      padding: 0.55rem;
    }

    .user-login-heading {
      max-width: 36rem;
    }

    .user-login-heading h1 {
      margin: 0 0 1rem;
      color: #fff;
      font-size: 3rem;
      line-height: 1.05;
      font-weight: 900;
    }

    .user-login-heading p {
      max-width: 30rem;
      margin: 0;
      color: rgba(255, 255, 255, 0.78);
      font-size: 1rem;
      line-height: 1.7;
    }

    .user-login-panel {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 2rem;
      background: var(--admin-surface);
    }

    .user-login-form {
      width: min(100%, 360px);
      margin: 0 auto;
    }

    .user-login-kicker {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.45rem 0.8rem;
      border-radius: 999px;
      background: #ccfbf1;
      color: #0f766e;
      font-size: 0.8rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .user-login-title {
      margin: 1rem 0 0.5rem;
      font-weight: 900;
      letter-spacing: 0;
    }

    .user-login-copy {
      color: var(--admin-muted);
      line-height: 1.65;
      margin-bottom: 1.4rem;
    }

    .otp-test-code {
      border: 1px dashed #0d9488;
      border-radius: 8px;
      background: #f0fdfa;
      color: #115e59;
      padding: 0.85rem 1rem;
      font-size: 0.85rem;
      line-height: 1.45;
    }

    .otp-test-code strong {
      display: block;
      font-size: 1.5rem;
      letter-spacing: 0.2em;
      margin-top: 0.2rem;
    }

    .input-group-alternative {
      border-radius: 8px;
      border: 1px solid var(--admin-border);
      background: var(--admin-surface-soft);
    }

    .input-group-alternative .form-control,
    .input-group-alternative .input-group-text {
      border: 0;
      background: transparent;
    }

    .input-group-alternative .form-control {
      min-height: 48px;
    }

    .btn-user-login {
      width: 100%;
      min-height: 46px;
      border-radius: 8px;
      font-weight: 800;
      background: #0f766e;
      border-color: #0f766e;
    }

    .btn-user-login:hover,
    .btn-user-login:focus {
      background: #115e59;
      border-color: #115e59;
    }

    @media (max-width: 991.98px) {
      .user-login-shell {
        grid-template-columns: 1fr;
      }

      .user-login-visual {
        min-height: 280px;
      }

      .user-login-panel {
        min-height: auto;
      }

      .user-login-heading h1 {
        font-size: 2.25rem;
      }
    }
  </style>
</head>
<body class="user-login-body">
  <x-shared.mouse-trail />

  <main class="user-login-shell">
    <aside class="user-login-visual">
      @if(!empty($cashierLoginMedia['isVideo']))
        <video class="user-login-media-video" src="{{ $cashierLoginMedia['url'] }}" autoplay muted loop playsinline></video>
      @endif
      <div class="user-login-overlay"></div>
      <a class="user-login-brand" href="{{ route('admin.login') }}" aria-label="Open admin login">
        <img src="{{ $brandLogo }}" alt="{{ $systemName }}">
        <span>{{ $systemName }}</span>
      </a>
      <div class="user-login-heading">
        <h1>Staff Login</h1>
        <p>Enter your email and we will send a one-time code to open your cashier workspace.</p>
      </div>
      <div class="small text-white-50">Admin access is available at /admin.</div>
    </aside>

    <section class="user-login-panel">
      <form class="user-login-form" method="POST" action="{{ route('otp.send') }}">
        @csrf
        <div class="user-login-kicker"><i class="bi bi-envelope-check"></i> Email OTP</div>
        <h2 class="user-login-title">Get your code</h2>
        <p class="user-login-copy">Use the email assigned to your cashier account.</p>

        @if($errors->has('email'))
          <div class="alert alert-danger">{{ $errors->first('email') }}</div>
        @endif

        <div class="mb-3">
          <label class="form-label fw-bold" for="email">Email Address</label>
          <div class="input-group input-group-alternative">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $otpEmail) }}" placeholder="cashier@mail.com" required autocomplete="username">
          </div>
        </div>

        <button class="btn btn-primary btn-user-login" type="submit">Send OTP</button>
      </form>
    </section>
  </main>

  <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="{{ route('otp.verify') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-3">We sent a 6-digit code to {{ $otpEmail }}.</p>

            @if($errors->has('otp'))
              <div class="alert alert-danger">{{ $errors->first('otp') }}</div>
            @endif

            @if(!empty($otpMailNotice))
              <div class="alert alert-warning">{{ $otpMailNotice }}</div>
            @endif

            @if(!empty($testOtp))
              <div class="otp-test-code mb-3">
                Testing code shown on page
                <strong>{{ $testOtp }}</strong>
                <span>Expires at {{ $otpExpiresAt }}.</span>
              </div>
            @endif

            <label class="form-label fw-bold" for="otp">OTP Code</label>
            <div class="input-group input-group-alternative">
              <span class="input-group-text"><i class="bi bi-key"></i></span>
              <input id="otp" class="form-control" type="text" name="otp" maxlength="6" minlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter OTP" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-user-login">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/adminhmd/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const shouldShowModal = @json($showOtpModal || $errors->has('otp'));
      const otpModal = document.getElementById('otpModal');

      if (shouldShowModal && otpModal && window.bootstrap) {
        const modal = new bootstrap.Modal(otpModal);
        modal.show();
        otpModal.addEventListener('shown.bs.modal', function () {
          const otpInput = document.getElementById('otp');
          if (otpInput) otpInput.focus();
        }, { once: true });
      }
    });
  </script>
</body>
</html>
