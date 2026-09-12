<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showUserOtpLogin(): View
    {
        return view('auth.user-otp-login', [
            'cashierLoginMedia' => $this->authSplashMedia('cashier_login_image'),
            'cashierOverlayStyle' => $this->overlayStyle('cashier_login_overlay', 'cashier_login_overlay_color', '13,148,136'),
            'brandLogo' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'favicon' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'systemName' => $this->settingValue('system_name') ?: 'POS',
            'otpEmail' => session('user_otp_email'),
            'testOtp' => session('pending_user_otp'),
            'otpExpiresAt' => optional(session('pending_user_otp_expires_at'))->format('H:i'),
            'showOtpModal' => session('show_user_otp_modal', false),
            'otpMailNotice' => session('otp_mail_notice'),
        ]);
    }

    public function show(): View
    {
        return view('auth.login', [
            'splashMedia' => $this->authSplashMedia('admin_login_image'),
            'overlayStyle' => $this->overlayStyle('admin_login_overlay', 'admin_login_overlay_color', '185,28,28'),
            'brandLogo' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'favicon' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'systemName' => $this->settingValue('system_name') ?: 'POS',
        ]);
    }

    public function sendUserOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! in_array($user->role, ['cashier'], true)) {
            AuditTrail::record('user_otp_failed', 'OTP requested for invalid user login email', [
                'properties' => ['email' => $data['email']],
            ]);

            return back()->withErrors(['email' => 'No active user account was found for that email.'])->withInput();
        }

        if (! $user->is_active) {
            AuditTrail::record('login_blocked', 'Inactive user attempted OTP login', [
                'properties' => ['email' => $data['email']],
            ]);

            return back()->withErrors(['email' => 'This account has been disabled by an administrator.'])->withInput();
        }

        if ($user->login_bypass_enabled) {
            Auth::login($user);
            $request->session()->regenerate();
            AuditTrail::record('login_bypass', $user->name . ' logged in as cashier using bypass');

            return redirect()->route('cashier.pages.show', 'dashboard');
        }

        $otpMailNotice = $this->issueUserOtp($user, $request);

        return back()
            ->withInput(['email' => $user->email])
            ->with('user_otp_email', $user->email)
            ->with('show_user_otp_modal', true)
            ->with('otp_mail_notice', $otpMailNotice);
    }

    public function verifyUserOtp(Request $request): RedirectResponse
    {
        $request->merge([
            'otp' => trim((string) $request->input('otp', '')),
        ]);

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $pendingId = $request->session()->get('pending_user_otp_user_id');
        $user = $pendingId ? User::find($pendingId) : null;
        $otpHash = $request->session()->get('pending_user_otp_hash');
        $expiresAt = $request->session()->get('pending_user_otp_expires_at');
        $isExpired = ! $expiresAt || now()->greaterThan($expiresAt);

        if (! $user || $user->role !== 'cashier' || ! $user->is_active || ! $otpHash || $isExpired || ! Hash::check($request->otp, $otpHash)) {
            AuditTrail::record('user_otp_failed', 'Failed user OTP attempt', [
                'auditable_type' => 'user',
                'auditable_id' => $pendingId,
                'properties' => ['expired' => $isExpired],
            ]);

            return back()
                ->withErrors(['otp' => $isExpired ? 'This OTP has expired. Please request a new code.' : 'Incorrect OTP code'])
                ->with('show_user_otp_modal', true)
                ->with('user_otp_email', $request->session()->get('pending_user_otp_email'))
                ->with('otp_mail_notice', $this->mailDeliveryNotice());
        }

        $request->session()->forget([
            'pending_user_otp_user_id',
            'pending_user_otp_email',
            'pending_user_otp',
            'pending_user_otp_hash',
            'pending_user_otp_expires_at',
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        AuditTrail::record('login', $user->name . ' logged in as cashier using OTP');

        return redirect()->route('cashier.pages.show', 'dashboard');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $password = (string) ($data['password'] ?? '');
        $bypassEnabled = (bool) ($user?->login_bypass_enabled);

        if (! $user || (! $bypassEnabled && ($password === '' || ! Hash::check($password, $user->password)))) {
            AuditTrail::record('login_failed', 'Failed login attempt for ' . $data['email'], [
                'properties' => ['email' => $data['email']],
            ]);

            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if (! $user->is_active) {
            AuditTrail::record('login_blocked', 'Inactive account attempted to sign in', [
                'properties' => ['email' => $data['email']],
            ]);

            return back()->withErrors(['email' => 'This account has been disabled by an administrator.'])->withInput();
        }

        if (! in_array($user->role, ['admin', 'superadmin'], true)) {
            AuditTrail::record('login_failed', 'Non-admin account attempted admin login', [
                'properties' => ['email' => $data['email'], 'role' => $user->role],
            ]);

            return back()->withErrors(['email' => 'Use the staff login page for this account.'])->withInput();
        }

        if ($user->role === 'superadmin') {
            Auth::login($user);
            $request->session()->regenerate();
            AuditTrail::record($bypassEnabled ? 'login_bypass' : 'login', $user->name . ' logged in as superadmin' . ($bypassEnabled ? ' using bypass' : ''));

            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role === 'admin') {
            if ($bypassEnabled) {
                Auth::login($user);
                $request->session()->regenerate();
                AuditTrail::record('login_bypass', $user->name . ' logged in as admin using bypass');

                return redirect()->route('admin.dashboard');
            }

            $request->session()->put('pending_admin_user_id', $user->id);
            $request->session()->put('pending_admin_user_name', $user->name);
            $otpMailNotice = $this->issueAdminOtp($user, $request);

            return redirect()->route('admin.otp.show')->with('otp_mail_notice', $otpMailNotice);
        }

        return back()->withErrors(['email' => 'Use the staff login page for this account.'])->withInput();
    }

    public function showAdminOtp(Request $request): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $pendingId = $request->session()->get('pending_admin_user_id');
        if (! $pendingId) {
            return redirect()->route('admin.login');
        }

        $user = User::find($pendingId);
        if (! $user || $user->role !== 'admin') {
            $request->session()->forget([
                'pending_admin_user_id',
                'pending_admin_user_name',
                'pending_admin_otp',
                'pending_admin_otp_hash',
                'pending_admin_otp_expires_at',
            ]);

            return redirect()->route('admin.login');
        }

        if (! $request->session()->has('pending_admin_otp_hash')) {
            $this->issueAdminOtp($user, $request);
        }

        return view('auth.admin-otp', [
            'adminName' => $request->session()->get('pending_admin_user_name', 'Admin'),
            'adminEmail' => $user->email,
            'testOtp' => $request->session()->get('pending_admin_otp'),
            'otpExpiresAt' => optional($request->session()->get('pending_admin_otp_expires_at'))->format('H:i'),
            'splashMedia' => $this->authSplashMedia('admin_pin_image', 'assets/adminhmd/images/png/dasher-ai.png'),
            'overlayStyle' => $this->overlayStyle('admin_pin_overlay', 'admin_pin_overlay_color', '16,185,129'),
            'brandLogo' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'favicon' => $this->settingAsset('sidebar_logo', 'logo.png'),
            'systemName' => $this->settingValue('system_name') ?: 'POS',
            'otpMailNotice' => session('otp_mail_notice') ?: $this->mailDeliveryNotice(),
        ]);
    }

    public function verifyAdminOtp(Request $request): RedirectResponse
    {
        $request->merge([
            'admin_otp' => trim((string) $request->input('admin_otp', '')),
        ]);

        $request->validate([
            'admin_otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $pendingId = $request->session()->get('pending_admin_user_id');
        if (! $pendingId) {
            return redirect()->route('admin.login');
        }

        $user = User::find($pendingId);
        $otpHash = $request->session()->get('pending_admin_otp_hash');
        $expiresAt = $request->session()->get('pending_admin_otp_expires_at');
        $isExpired = ! $expiresAt || now()->greaterThan($expiresAt);

        if (! $user || $user->role !== 'admin' || ! $otpHash || $isExpired || ! Hash::check($request->admin_otp, $otpHash)) {
            AuditTrail::record('admin_otp_failed', 'Failed admin OTP attempt', [
                'auditable_type' => 'user',
                'auditable_id' => $pendingId,
                'properties' => [
                    'pending_admin_user_id' => $pendingId,
                    'expired' => $isExpired,
                ],
            ]);

            return back()->withErrors(['admin_otp' => $isExpired ? 'This OTP has expired. Please sign in again.' : 'Incorrect OTP code']);
        }

        $request->session()->forget([
            'pending_admin_user_id',
            'pending_admin_user_name',
            'pending_admin_otp',
            'pending_admin_otp_hash',
            'pending_admin_otp_expires_at',
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        AuditTrail::record('login', $user->name . ' logged in as admin');

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $userName = $request->user()?->name;
        if ($userName) {
            AuditTrail::record('logout', $userName . ' logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function authSplashImage(string $key, string $fallback = 'assets/adminhmd/images/png/dasher-ui-bootstrap-5.jpg'): string
    {
        return $this->authSplashMedia($key, $fallback)['url'];
    }

    private function authSplashMedia(string $key, string $fallback = 'assets/adminhmd/images/png/dasher-ui-bootstrap-5.jpg'): array
    {
        if (!Schema::hasTable('pos_settings')) {
            return [
                'url' => asset($fallback),
                'isVideo' => false,
            ];
        }

        $media = DB::table('pos_settings')->where('key', $key)->value('value');
        if (!empty($media)) {
            return [
                'url' => asset('assets/admin/img/settings/' . $media),
                'isVideo' => $this->isVideoFile((string) $media),
            ];
        }

        return [
            'url' => asset($fallback),
            'isVideo' => $this->isVideoFile($fallback),
        ];
    }

    private function settingAsset(string $key, string $fallback): string
    {
        if (!Schema::hasTable('pos_settings')) {
            return asset($fallback);
        }

        $value = DB::table('pos_settings')->where('key', $key)->value('value');

        return !empty($value)
            ? asset('assets/admin/img/settings/' . $value)
            : asset($fallback);
    }

    private function settingValue(string $key): ?string
    {
        if (!Schema::hasTable('pos_settings')) {
            return null;
        }

        $value = DB::table('pos_settings')->where('key', $key)->value('value');

        return $value !== null ? (string) $value : null;
    }

    private function issueAdminOtp(User $user, Request $request): ?string
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $request->session()->put('pending_admin_otp', $otp);
        $request->session()->put('pending_admin_otp_hash', Hash::make($otp));
        $request->session()->put('pending_admin_otp_expires_at', $expiresAt);

        return $this->sendOtpEmail($user, 'Admin login OTP', "Your admin login OTP is {$otp}. This code expires in 10 minutes.");
    }

    private function issueUserOtp(User $user, Request $request): ?string
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $request->session()->put('pending_user_otp_user_id', $user->id);
        $request->session()->put('pending_user_otp_email', $user->email);
        $request->session()->put('pending_user_otp', $otp);
        $request->session()->put('pending_user_otp_hash', Hash::make($otp));
        $request->session()->put('pending_user_otp_expires_at', $expiresAt);

        return $this->sendOtpEmail($user, 'Login OTP', "Your login OTP is {$otp}. This code expires in 10 minutes.");
    }

    private function sendOtpEmail(User $user, string $subject, string $body): ?string
    {
        $notice = $this->mailDeliveryNotice();

        try {
            Mail::raw(
                $body,
                function ($message) use ($user, $subject) {
                    $message->to($user->email, $user->name)->subject($subject);
                }
            );
        } catch (\Throwable $e) {
            Log::warning('OTP email could not be sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'mailer' => config('mail.default'),
                'message' => $e->getMessage(),
            ]);

            return 'Email delivery failed. The testing code is shown on this page while mail settings are fixed.';
        }

        return $notice;
    }

    private function mailDeliveryNotice(): ?string
    {
        $mailer = (string) config('mail.default');

        if (in_array($mailer, ['log', 'array'], true)) {
            return "Email is currently in {$mailer} mode, so OTPs are not delivered to inboxes. The testing code is shown on this page.";
        }

        return null;
    }

    private function isVideoFile(string $path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'mov'], true);
    }

    private function overlayStyle(string $strengthKey, string $colorKey, string $fallbackRgb): string
    {
        if (!Schema::hasTable('pos_settings')) {
            $strength = 72;
            $rgb = $fallbackRgb;
        } else {
            $strengthValue = DB::table('pos_settings')->where('key', $strengthKey)->value('value');
            $colorValue = DB::table('pos_settings')->where('key', $colorKey)->value('value');

            $strength = is_numeric($strengthValue) ? (int) $strengthValue : 72;
            $rgb = match ((string) $colorValue) {
                'red' => '185,28,28',
                'blue' => '37,99,235',
                'green' => '16,185,129',
                'amber' => '217,119,6',
                'slate' => '51,65,85',
                'purple' => '124,58,237',
                default => $fallbackRgb,
            };
        }

        $alpha = max(0.4, min(0.85, $strength / 100));

        return "background: linear-gradient(160deg, rgba({$rgb}, {$alpha}), rgba(2, 6, 23, 0.55));";
    }
}
