<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            AuditTrail::record('login_failed', 'Failed login attempt for ' . $data['email'], [
                'properties' => ['email' => $data['email']],
            ]);

            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if ($user->role === 'admin') {
            $request->session()->put('pending_admin_user_id', $user->id);
            $request->session()->put('pending_admin_user_name', $user->name);

            return redirect()->route('admin.pincode.show');
        }

        Auth::login($user);
        $request->session()->regenerate();
        AuditTrail::record('login', $user->name . ' logged in as cashier');

        return redirect()->route('cashier.pages.show', 'dashboard');
    }

    public function showAdminPincode(Request $request): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (! $request->session()->has('pending_admin_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.admin-pincode', [
            'adminName' => $request->session()->get('pending_admin_user_name', 'Admin'),
        ]);
    }

    public function verifyAdminPincode(Request $request): RedirectResponse
    {
        $request->validate([
            'admin_pincode' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        $pendingId = $request->session()->get('pending_admin_user_id');
        if (! $pendingId) {
            return redirect()->route('login');
        }

        $user = User::find($pendingId);
        if (! $user || $user->role !== 'admin' || ! $user->pincode_hash || ! Hash::check($request->admin_pincode, $user->pincode_hash)) {
            AuditTrail::record('admin_pincode_failed', 'Failed admin PIN attempt', [
                'auditable_type' => 'user',
                'auditable_id' => $pendingId,
                'properties' => ['pending_admin_user_id' => $pendingId],
            ]);

            return back()->withErrors(['admin_pincode' => 'Incorrect Authentication Credentials']);
        }

        $request->session()->forget(['pending_admin_user_id', 'pending_admin_user_name']);
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
}
