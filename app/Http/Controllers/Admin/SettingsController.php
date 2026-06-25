<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $heroImage = DB::table('pos_settings')->where('key', 'admin_hero_image')->value('value');
        $heroOverlay = DB::table('pos_settings')->where('key', 'admin_hero_overlay')->value('value');
        $heroOverlay = is_numeric($heroOverlay) ? (int) $heroOverlay : 72;
        $darkMode = DB::table('pos_settings')->where('key', 'admin_dark_mode')->value('value');
        $darkMode = (string) $darkMode === '1';
        $themePreset = DB::table('pos_settings')->where('key', 'theme_preset')->value('value') ?: 'emerald';
        $fontFamily = DB::table('pos_settings')->where('key', 'font_family')->value('value') ?: 'open_sans';
        $fontSize = DB::table('pos_settings')->where('key', 'font_size')->value('value') ?: '15';
        $sidebarColor = DB::table('pos_settings')->where('key', 'sidebar_color')->value('value') ?: 'default';
        $systemName = DB::table('pos_settings')->where('key', 'system_name')->value('value') ?: 'POS';
        $sidebarLogo = DB::table('pos_settings')->where('key', 'sidebar_logo')->value('value');
        return view('admin.settings.index', compact('heroImage', 'heroOverlay', 'darkMode', 'themePreset', 'fontFamily', 'fontSize', 'sidebarColor', 'systemName', 'sidebarLogo'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'pincode' => ['nullable', 'string', 'min:4', 'max:10'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'hero_overlay' => ['nullable', 'integer', 'min:40', 'max:85'],
            'dark_mode' => ['nullable', 'in:0,1'],
            'theme_preset' => ['nullable', 'in:emerald,amber,rose,ocean,slate'],
            'font_family' => ['nullable', 'in:open_sans,poppins,source_sans,nunito,system'],
            'font_size' => ['nullable', 'integer', 'min:13', 'max:19'],
            'sidebar_color' => ['nullable', 'in:default,midnight,forest,wine,indigo'],
            'system_name' => ['nullable', 'string', 'max:30'],
            'sidebar_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $user = $request->user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        if (!empty($data['pincode'])) {
            $user->pincode_hash = Hash::make($data['pincode']);
        }
        $user->save();

        if ($request->hasFile('hero_image')) {
            $oldImage = DB::table('pos_settings')->where('key', 'admin_hero_image')->value('value');
            $dir = public_path('assets/admin/img/settings');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $file = $request->file('hero_image');
            $fileName = 'hero-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            DB::table('pos_settings')->updateOrInsert(
                ['key' => 'admin_hero_image'],
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
            ['key' => 'admin_hero_overlay'],
            [
                'value' => (string) ((int) ($data['hero_overlay'] ?? 72)),
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
        DB::table('pos_settings')->updateOrInsert(['key' => 'theme_preset'], ['value' => (string)($data['theme_preset'] ?? 'emerald'), 'updated_at' => now(), 'created_at' => now()]);
        DB::table('pos_settings')->updateOrInsert(['key' => 'font_family'], ['value' => (string)($data['font_family'] ?? 'open_sans'), 'updated_at' => now(), 'created_at' => now()]);
        DB::table('pos_settings')->updateOrInsert(['key' => 'font_size'], ['value' => (string)((int)($data['font_size'] ?? 15)), 'updated_at' => now(), 'created_at' => now()]);
        DB::table('pos_settings')->updateOrInsert(['key' => 'sidebar_color'], ['value' => (string)($data['sidebar_color'] ?? 'default'), 'updated_at' => now(), 'created_at' => now()]);
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
                    'pincode_changed' => !empty($data['pincode']),
                ],
                'appearance' => [
                    'hero_image_changed' => $request->hasFile('hero_image'),
                    'hero_overlay' => (int) ($data['hero_overlay'] ?? 72),
                    'dark_mode' => $request->boolean('dark_mode'),
                    'theme_preset' => $data['theme_preset'] ?? 'emerald',
                    'font_family' => $data['font_family'] ?? 'open_sans',
                    'font_size' => (int) ($data['font_size'] ?? 15),
                    'sidebar_color' => $data['sidebar_color'] ?? 'default',
                    'system_name' => $data['system_name'] ?? 'POS',
                    'sidebar_logo_changed' => $request->hasFile('sidebar_logo'),
                ],
            ],
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated');
    }
}
