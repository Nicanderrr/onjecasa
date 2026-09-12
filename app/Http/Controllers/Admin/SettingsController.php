<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $loginImage = $this->settingValue('admin_login_image');
        $cashierLoginImage = $this->settingValue('cashier_login_image');
        $cashierLoginOverlay = $this->intSetting('cashier_login_overlay', 72);
        $cashierLoginOverlayColor = $this->settingValue('cashier_login_overlay_color') ?: 'green';
        $loginOverlay = $this->intSetting('admin_login_overlay', 72);
        $loginOverlayColor = $this->settingValue('admin_login_overlay_color') ?: 'red';
        $pinImage = $this->settingValue('admin_pin_image');
        $pinOverlay = $this->intSetting('admin_pin_overlay', 72);
        $pinOverlayColor = $this->settingValue('admin_pin_overlay_color') ?: 'green';
        $darkMode = $this->settingValue('admin_dark_mode') === '1';
        $mouseTrailEnabled = $this->settingValue('global_mouse_trail') !== '0';
        $systemName = $this->settingValue('system_name') ?: 'POS';
        $sidebarLogo = $this->settingValue('sidebar_logo');
        return view('admin.settings.index', compact('loginImage', 'cashierLoginImage', 'cashierLoginOverlay', 'cashierLoginOverlayColor', 'loginOverlay', 'loginOverlayColor', 'pinImage', 'pinOverlay', 'pinOverlayColor', 'darkMode', 'mouseTrailEnabled', 'systemName', 'sidebarLogo'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'login_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm,ogg,mov', 'max:20480'],
            'cashier_login_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm,ogg,mov', 'max:20480'],
            'cashier_login_overlay' => ['nullable', 'integer', 'min:40', 'max:85'],
            'cashier_login_overlay_color' => ['nullable', 'in:red,blue,green,amber,slate,purple'],
            'login_overlay' => ['nullable', 'integer', 'min:40', 'max:85'],
            'login_overlay_color' => ['nullable', 'in:red,blue,green,amber,slate,purple'],
            'pin_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm,ogg,mov', 'max:20480'],
            'pin_overlay' => ['nullable', 'integer', 'min:40', 'max:85'],
            'pin_overlay_color' => ['nullable', 'in:red,blue,green,amber,slate,purple'],
            'dark_mode' => ['nullable', 'in:0,1'],
            'global_mouse_trail' => ['nullable', 'in:0,1'],
            'system_name' => ['nullable', 'string', 'max:30'],
            'sidebar_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $user = $request->user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->avatar_path = $this->storeProfileAvatar($request, $user->avatar_path);
        $user->save();

        if ($request->hasFile('login_image')) {
            $oldImage = $this->settingValue('admin_login_image');
            $dir = public_path('assets/admin/img/settings');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $file = $request->file('login_image');
            $fileName = 'login-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            DB::table('pos_settings')->updateOrInsert(
                ['key' => 'admin_login_image'],
                ['value' => $fileName, 'updated_at' => now(), 'created_at' => now()]
            );

            if (!empty($oldImage)) {
                $oldPath = $dir . DIRECTORY_SEPARATOR . $oldImage;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        if ($request->hasFile('cashier_login_image')) {
            $oldImage = $this->settingValue('cashier_login_image');
            $dir = public_path('assets/admin/img/settings');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $file = $request->file('cashier_login_image');
            $fileName = 'cashier-login-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            DB::table('pos_settings')->updateOrInsert(
                ['key' => 'cashier_login_image'],
                ['value' => $fileName, 'updated_at' => now(), 'created_at' => now()]
            );

            if (!empty($oldImage)) {
                $oldPath = $dir . DIRECTORY_SEPARATOR . $oldImage;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'cashier_login_overlay'],
            [
                'value' => (string) ((int) ($data['cashier_login_overlay'] ?? 72)),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'cashier_login_overlay_color'],
            [
                'value' => (string) ($data['cashier_login_overlay_color'] ?? 'green'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'admin_login_overlay'],
            [
                'value' => (string) ((int) ($data['login_overlay'] ?? 72)),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'admin_login_overlay_color'],
            [
                'value' => (string) ($data['login_overlay_color'] ?? 'red'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        if ($request->hasFile('pin_image')) {
            $oldImage = $this->settingValue('admin_pin_image');
            $dir = public_path('assets/admin/img/settings');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $file = $request->file('pin_image');
            $fileName = 'pin-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            DB::table('pos_settings')->updateOrInsert(
                ['key' => 'admin_pin_image'],
                ['value' => $fileName, 'updated_at' => now(), 'created_at' => now()]
            );

            if (!empty($oldImage)) {
                $oldPath = $dir . DIRECTORY_SEPARATOR . $oldImage;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'admin_pin_overlay'],
            [
                'value' => (string) ((int) ($data['pin_overlay'] ?? 72)),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'admin_pin_overlay_color'],
            [
                'value' => (string) ($data['pin_overlay_color'] ?? 'green'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'admin_dark_mode'],
            [
                'value' => $request->boolean('dark_mode') ? '1' : '0',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::table('pos_settings')->updateOrInsert(
            ['key' => 'global_mouse_trail'],
            [
                'value' => $request->boolean('global_mouse_trail') ? '1' : '0',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::table('pos_settings')->updateOrInsert(['key' => 'system_name'], ['value' => (string)($data['system_name'] ?? 'POS'), 'updated_at' => now(), 'created_at' => now()]);

        if ($request->hasFile('sidebar_logo')) {
            $oldLogo = DB::table('pos_settings')->where('key', 'sidebar_logo')->value('value');
            $dir = public_path('assets/admin/img/settings');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $file = $request->file('sidebar_logo');
            $fileName = 'logo-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            DB::table('pos_settings')->updateOrInsert(
                ['key' => 'sidebar_logo'],
                ['value' => $fileName, 'updated_at' => now(), 'created_at' => now()]
            );
            if (!empty($oldLogo)) {
                $oldPath = $dir . DIRECTORY_SEPARATOR . $oldLogo;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        AuditTrail::record('settings_updated', 'Updated POS settings', [
            'auditable_type' => 'settings',
            'properties' => [
                'user' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password_changed' => !empty($data['password']),
                ],
                'appearance' => [
                    'login_media_changed' => $request->hasFile('login_image'),
                    'cashier_login_media_changed' => $request->hasFile('cashier_login_image'),
                    'cashier_login_overlay' => (int) ($data['cashier_login_overlay'] ?? 72),
                    'cashier_login_overlay_color' => $data['cashier_login_overlay_color'] ?? 'green',
                    'login_overlay' => (int) ($data['login_overlay'] ?? 72),
                    'login_overlay_color' => $data['login_overlay_color'] ?? 'red',
                    'otp_media_changed' => $request->hasFile('pin_image'),
                    'pin_overlay' => (int) ($data['pin_overlay'] ?? 72),
                    'pin_overlay_color' => $data['pin_overlay_color'] ?? 'green',
                    'dark_mode' => $request->boolean('dark_mode'),
                    'mouse_trail' => $request->boolean('global_mouse_trail'),
                    'system_name' => $data['system_name'] ?? 'POS',
                    'sidebar_logo_changed' => $request->hasFile('sidebar_logo'),
                ],
            ],
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated');
    }

    private function settingValue(string $key): ?string
    {
        if (!Schema::hasTable('pos_settings')) {
            return null;
        }

        $value = DB::table('pos_settings')->where('key', $key)->value('value');

        return $value !== null ? (string) $value : null;
    }

    private function intSetting(string $key, int $default): int
    {
        $value = $this->settingValue($key);

        return is_numeric($value) ? (int) $value : $default;
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
}
