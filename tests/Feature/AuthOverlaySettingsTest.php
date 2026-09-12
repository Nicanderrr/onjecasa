<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthOverlaySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_independent_login_and_otp_overlay_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'login_overlay' => 47,
            'login_overlay_color' => 'purple',
            'cashier_login_overlay' => 61,
            'cashier_login_overlay_color' => 'blue',
            'pin_overlay' => 83,
            'pin_overlay_color' => 'red',
            'dark_mode' => 0,
            'system_name' => 'NewPOS',
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertSame('47', DB::table('pos_settings')->where('key', 'admin_login_overlay')->value('value'));
        $this->assertSame('purple', DB::table('pos_settings')->where('key', 'admin_login_overlay_color')->value('value'));
        $this->assertSame('61', DB::table('pos_settings')->where('key', 'cashier_login_overlay')->value('value'));
        $this->assertSame('blue', DB::table('pos_settings')->where('key', 'cashier_login_overlay_color')->value('value'));
        $this->assertSame('83', DB::table('pos_settings')->where('key', 'admin_pin_overlay')->value('value'));
        $this->assertSame('red', DB::table('pos_settings')->where('key', 'admin_pin_overlay_color')->value('value'));
    }

    public function test_admin_login_page_renders_saved_hue_and_strength_over_the_image(): void
    {
        $this->setSetting('admin_login_overlay', '47');
        $this->setSetting('admin_login_overlay_color', 'purple');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('rgba(124,58,237, 0.47)', false)
            ->assertSee('.auth-splash > :not(.auth-media-video):not(.auth-hue-overlay)', false);
    }

    public function test_cashier_login_page_renders_saved_login_image(): void
    {
        $this->setSetting('cashier_login_image', 'cashier-login-test.jpg');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('assets/admin/img/settings/cashier-login-test.jpg', false);
    }

    public function test_cashier_login_page_renders_saved_hue_and_strength_over_the_media(): void
    {
        $this->setSetting('cashier_login_overlay', '61');
        $this->setSetting('cashier_login_overlay_color', 'blue');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('rgba(37,99,235, 0.61)', false)
            ->assertSee('.user-login-visual > :not(.user-login-media-video):not(.user-login-overlay)', false);
    }

    public function test_cashier_login_page_renders_saved_login_video(): void
    {
        $this->setSetting('cashier_login_image', 'cashier-login-test.mp4');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<video class="user-login-media-video"', false)
            ->assertSee('assets/admin/img/settings/cashier-login-test.mp4', false);
    }

    public function test_admin_login_page_renders_saved_login_video_as_background_media(): void
    {
        $this->setSetting('admin_login_image', 'admin-login-test.mp4');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('<video class="auth-media-video"', false)
            ->assertSee('.auth-splash > :not(.auth-media-video):not(.auth-hue-overlay)', false)
            ->assertSee('assets/admin/img/settings/admin-login-test.mp4', false);
    }

    public function test_otp_page_renders_saved_red_hue_and_strength_over_the_image(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->setSetting('admin_pin_overlay', '83');
        $this->setSetting('admin_pin_overlay_color', 'red');

        $this->withSession([
            'pending_admin_user_id' => $admin->id,
            'pending_admin_user_name' => $admin->name,
        ])->get(route('admin.otp.show'))
            ->assertOk()
            ->assertSee('rgba(185,28,28, 0.83)', false)
            ->assertSee('.pin-splash > :not(.auth-media-video):not(.auth-hue-overlay)', false);
    }

    public function test_admin_otp_page_renders_saved_video_as_background_media(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->setSetting('admin_pin_image', 'admin-otp-test.mp4');

        $this->withSession([
            'pending_admin_user_id' => $admin->id,
            'pending_admin_user_name' => $admin->name,
        ])->get(route('admin.otp.show'))
            ->assertOk()
            ->assertSee('<video class="auth-media-video"', false)
            ->assertSee('.pin-splash > :not(.auth-media-video):not(.auth-hue-overlay)', false)
            ->assertSee('assets/admin/img/settings/admin-otp-test.mp4', false);
    }

    private function setSetting(string $key, string $value): void
    {
        DB::table('pos_settings')->insert([
            'key' => $key,
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
