<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        [$dateFrom, $dateTo, $dateFilters] = $this->dateRange($request);

        $orders = DB::table('pos_orders')->whereBetween('created_at', [$dateFrom, $dateTo]);
        $payments = DB::table('pos_payments')->whereBetween('created_at', [$dateFrom, $dateTo]);

        $metrics = [
            ['label' => 'System Users', 'value' => User::count(), 'hint' => User::where('role', 'superadmin')->count() . ' superadmin(s)', 'icon' => 'bi-people', 'tone' => 'metric-primary', 'route' => route('superadmin.users')],
            ['label' => 'POS Products', 'value' => DB::table('pos_products')->count(), 'hint' => number_format((int) DB::table('pos_products')->sum('stock')) . ' stock units', 'icon' => 'bi-box-seam', 'tone' => 'metric-success', 'route' => route('admin.products.index')],
            ['label' => 'POS Sales', 'value' => (clone $orders)->count(), 'hint' => number_format((float) (clone $payments)->sum('amount'), 2) . ' collected', 'icon' => 'bi-receipt', 'tone' => 'metric-warning', 'route' => route('admin.sales.index')],
            ['label' => 'Audit Events', 'value' => DB::table('pos_audit_trails')->count() + $this->superadminAuditQuery()->count(), 'hint' => 'POS and superadmin logs', 'icon' => 'bi-shield-check', 'tone' => 'metric-danger', 'route' => route('superadmin.audit')],
        ];

        $recentLogs = $this->superadminAuditQuery()->orderByDesc('id')->limit(8)->get();
        $posAudit = DB::table('pos_audit_trails')->orderByDesc('id')->limit(8)->get();
        $settings = $this->settings();
        $quickLinks = $this->quickLinks();
        $dailyActivity = $this->dailyActivity($dateFrom, $dateTo);

        return view('superadmin.dashboard', compact('metrics', 'recentLogs', 'posAudit', 'settings', 'quickLinks', 'dailyActivity', 'dateFilters'));
    }

    public function users(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $metrics = [
            'total' => User::count(),
            'superadmins' => User::where('role', 'superadmin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'cashiers' => User::where('role', 'cashier')->count(),
            'active' => User::where('is_active', true)->count(),
            'bypass' => User::where('login_bypass_enabled', true)->count(),
        ];

        return view('superadmin.users', compact('users', 'metrics', 'search'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:pos_staff,email'],
            'role' => ['required', 'in:cashier,admin,superadmin'],
            'password' => ['required_unless:login_bypass_enabled,1', 'nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'login_bypass_enabled' => ['nullable', 'boolean'],
        ]);

        $user = DB::transaction(function () use ($request, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? Str::random(40)),
                'role' => $data['role'],
                'is_active' => $request->boolean('is_active', true),
                'login_bypass_enabled' => $request->boolean('login_bypass_enabled'),
                'pincode_hash' => null,
            ]);

            if ($data['role'] === 'cashier') {
                DB::table('pos_staff')->insert([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'number' => 'STAFF-' . strtoupper(Str::random(6)),
                    'email' => $user->email,
                    'pincode' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $user;
        });

        $this->audit($request, 'created_user', 'user', $user->id, $user->only(['name', 'email', 'role', 'is_active', 'login_bypass_enabled']));

        return redirect()->route('superadmin.users')->with('success', 'User account created.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id), Rule::unique('pos_staff', 'email')->ignore($user->id, 'user_id')],
            'role' => ['required', 'in:cashier,admin,superadmin'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'login_bypass_enabled' => ['nullable', 'boolean'],
        ]);

        if ($user->id === $request->user()->id && ($data['role'] !== 'superadmin' || ! $request->boolean('is_active'))) {
            return back()->with('error', 'You cannot remove your own superadmin access.');
        }

        $before = $user->only(['name', 'email', 'role', 'is_active', 'login_bypass_enabled']);

        DB::transaction(function () use ($request, $user, $data) {
            $oldRole = $user->role;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $data['role'];
            $user->is_active = $request->boolean('is_active');
            $user->login_bypass_enabled = $request->boolean('login_bypass_enabled');
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if ($data['role'] === 'cashier') {
                $user->pincode_hash = null;
            }
            $user->save();

            if ($data['role'] === 'cashier') {
                $staff = DB::table('pos_staff')->where('user_id', $user->id)->first();
                if ($staff) {
                    DB::table('pos_staff')->where('id', $staff->id)->update([
                        'name' => $user->name,
                        'email' => $user->email,
                        'pincode' => null,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('pos_staff')->insert([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'number' => 'STAFF-' . strtoupper(Str::random(6)),
                        'email' => $user->email,
                        'pincode' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($oldRole === 'cashier') {
                DB::table('pos_staff')->where('user_id', $user->id)->delete();
            }
        });

        $this->audit($request, 'updated_user', 'user', $user->id, [
            'before' => $before,
            'after' => $user->fresh()->only(['name', 'email', 'role', 'is_active', 'login_bypass_enabled']),
            'password_changed' => !empty($data['password']),
        ]);

        return back()->with('success', 'User account updated.');
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $snapshot = $user->only(['id', 'name', 'email', 'role']);
        DB::table('pos_staff')->where('user_id', $user->id)->delete();
        $user->delete();
        $this->audit($request, 'deleted_user', 'user', null, ['deleted_user' => $snapshot]);

        return back()->with('success', 'User account deleted.');
    }

    public function auditLogs(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $source = $request->query('source', 'superadmin');

        if ($source === 'pos') {
            $logs = DB::table('pos_audit_trails')
                ->when($search !== '', fn ($query) => $query
                    ->where('event', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_role', 'like', "%{$search}%"))
                ->orderByDesc('id')
                ->paginate(50)
                ->withQueryString();
        } else {
            $logs = $this->superadminAuditQuery()
                ->when($search !== '', fn ($query) => $query
                    ->where('action', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%"))
                ->orderByDesc('superadmin_audit_logs.id')
                ->paginate(50)
                ->withQueryString();
        }

        $summary = [
            'superadmin' => DB::table('superadmin_audit_logs')->count(),
            'pos' => DB::table('pos_audit_trails')->count(),
            'today' => DB::table('superadmin_audit_logs')->whereDate('created_at', today())->count() + DB::table('pos_audit_trails')->whereDate('created_at', today())->count(),
        ];

        return view('superadmin.audit', compact('logs', 'summary', 'search', 'source'));
    }

    public function security(): View
    {
        $settings = $this->settings();
        $adminUsers = User::whereIn('role', ['admin', 'superadmin'])->orderByDesc('id')->get();
        $sessions = DB::table('sessions')->whereNotNull('user_id')->orderByDesc('last_activity')->limit(50)->get();

        return view('superadmin.security', compact('settings', 'adminUsers', 'sessions'));
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $settings = [
            'ai_enabled' => $request->boolean('ai_enabled'),
            'maintenance_enabled' => $request->boolean('maintenance_enabled'),
            'maintenance_note' => (string) $request->input('maintenance_note', ''),
        ];

        $this->setSettings($settings);
        $this->audit($request, 'updated_security_settings', null, null, $settings);

        return back()->with('success', 'Security settings updated.');
    }

    public function settingsPage(): View
    {
        $settings = $this->settings();
        $posSettings = DB::table('pos_settings')->pluck('value', 'key');

        return view('superadmin.settings', compact('settings', 'posSettings'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $user->avatar_path = $this->storeProfileAvatar($request, $user->avatar_path);
        $user->save();

        $this->audit($request, 'updated_profile_photo', 'user', $user->id, [
            'avatar_changed' => $request->hasFile('avatar'),
        ]);

        return back()->with('success', 'Profile photo updated.');
    }

    public function maintenance(): View
    {
        $settings = $this->settings();

        return view('superadmin.maintenance', compact('settings'));
    }

    public function clearCache(Request $request): RedirectResponse
    {
        Artisan::call('optimize:clear');
        $this->audit($request, 'cleared_application_cache');

        return back()->with('success', 'Application cache cleared.');
    }

    public function toggleMaintenance(Request $request): RedirectResponse
    {
        $settings = $this->settings();
        $settings['maintenance_enabled'] = $request->boolean('maintenance_enabled');
        $this->setSettings($settings);
        $this->audit($request, 'toggled_maintenance_flag', null, null, ['enabled' => $settings['maintenance_enabled']]);

        return back()->with('success', 'Maintenance flag updated.');
    }

    private function quickLinks(): array
    {
        return [
            ['label' => 'POS Admin Dashboard', 'route' => route('admin.dashboard'), 'description' => 'Sales, stock, and cashier operations'],
            ['label' => 'Products', 'route' => route('admin.products.index'), 'description' => 'Inventory and barcode catalog'],
            ['label' => 'Users & Roles', 'route' => route('superadmin.users'), 'description' => 'Create, reset, promote, or deactivate accounts'],
            ['label' => 'Audit Logs', 'route' => route('superadmin.audit'), 'description' => 'Review superadmin and POS activity'],
            ['label' => 'Security Center', 'route' => route('superadmin.security'), 'description' => 'Policy switches and active sessions'],
            ['label' => 'Maintenance', 'route' => route('superadmin.maintenance'), 'description' => 'Clear app caches and maintenance flag'],
        ];
    }

    private function dateRange(Request $request): array
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : now()->startOfDay()->subDays(6);
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : now()->endOfDay();

        if ($dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        return [$dateFrom, $dateTo, ['date_from' => $dateFrom->toDateString(), 'date_to' => $dateTo->toDateString()]];
    }

    private function dailyActivity(Carbon $dateFrom, Carbon $dateTo)
    {
        $start = $dateFrom->copy()->startOfDay();
        $end = $dateTo->copy()->endOfDay();
        $days = (int) max(0, min(60, $start->diffInDays($end)));
        $superadmin = DB::table('superadmin_audit_logs')->selectRaw('DATE(created_at) as day, COUNT(*) as total')->whereBetween('created_at', [$start, $end])->groupBy('day')->pluck('total', 'day');
        $pos = DB::table('pos_audit_trails')->selectRaw('DATE(created_at) as day, COUNT(*) as total')->whereBetween('created_at', [$start, $end])->groupBy('day')->pluck('total', 'day');

        return collect(range(0, $days))->map(function (int $offset) use ($start, $superadmin, $pos) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            return ['label' => $date->format('D'), 'total' => (int) ($superadmin[$key] ?? 0) + (int) ($pos[$key] ?? 0)];
        });
    }

    private function settings(): array
    {
        $raw = DB::table('pos_settings')->where('key', 'superadmin_security')->value('value');
        $settings = $raw ? json_decode($raw, true) : null;

        return is_array($settings) ? array_merge($this->defaultSettings(), $settings) : $this->defaultSettings();
    }

    private function setSettings(array $settings): void
    {
        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'superadmin_security'],
            ['value' => json_encode(array_merge($this->defaultSettings(), $settings)), 'updated_at' => now(), 'created_at' => now()]
        );
    }

    private function storeProfileAvatar(Request $request, ?string $currentAvatar): ?string
    {
        if (! $request->hasFile('avatar')) {
            return $currentAvatar;
        }

        $dir = public_path('assets/admin/img/profiles');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $request->file('avatar');
        $fileName = 'avatar-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $fileName);

        if (!empty($currentAvatar)) {
            $oldPath = $dir . DIRECTORY_SEPARATOR . $currentAvatar;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $fileName;
    }

    private function defaultSettings(): array
    {
        return ['ai_enabled' => true, 'maintenance_enabled' => false, 'maintenance_note' => ''];
    }

    private function superadminAuditQuery()
    {
        return DB::table('superadmin_audit_logs')
            ->leftJoin('users', 'users.id', '=', 'superadmin_audit_logs.user_id')
            ->select('superadmin_audit_logs.*', 'users.name as user_name', 'users.email as user_email');
    }

    private function audit(Request $request, string $action, ?string $subjectType = null, ?int $subjectId = null, array $properties = []): void
    {
        DB::table('superadmin_audit_logs')->insert([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties ? json_encode($properties) : null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
