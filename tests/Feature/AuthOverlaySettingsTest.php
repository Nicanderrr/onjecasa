<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthOverlaySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_independent_login_and_pin_overlay_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'login_overlay' => 47,
            'login_overlay_color' => 'purple',
            'pin_overlay' => 83,
            'pin_overlay_color' => 'red',
            'dark_mode' => 0,
            'system_name' => 'NewPOS',
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertSame('47', DB::table('pos_settings')->where('key', 'admin_login_overlay')->value('value'));
        $this->assertSame('purple', DB::table('pos_settings')->where('key', 'admin_login_overlay_color')->value('value'));
        $this->assertSame('83', DB::table('pos_settings')->where('key', 'admin_pin_overlay')->value('value'));
        $this->assertSame('red', DB::table('pos_settings')->where('key', 'admin_pin_overlay_color')->value('value'));
    }

    public function test_login_page_renders_saved_hue_and_strength_over_the_image(): void
    {
        $this->setSetting('admin_login_overlay', '47');
        $this->setSetting('admin_login_overlay_color', 'purple');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('rgba(124,58,237, 0.47)', false)
            ->assertSee('.auth-splash > :not(.auth-hue-overlay)', false);
    }

    public function test_pin_page_renders_saved_red_hue_and_strength_over_the_image(): void
    {
        $this->setSetting('admin_pin_overlay', '83');
        $this->setSetting('admin_pin_overlay_color', 'red');

        $this->withSession([
            'pending_admin_user_id' => 1,
            'pending_admin_user_name' => 'System Admin',
        ])->get(route('admin.pincode.show'))
            ->assertOk()
            ->assertSee('rgba(185,28,28, 0.83)', false)
            ->assertSee('.pin-splash > :not(.auth-hue-overlay)', false);
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
