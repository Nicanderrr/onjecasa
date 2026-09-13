<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditTrailVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_audit_trail_hides_superadmin_pos_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        DB::table('pos_audit_trails')->insert([
            [
                'user_id' => $admin->id,
                'user_name' => 'Regular Admin',
                'user_role' => 'admin',
                'event' => 'product_updated',
                'description' => 'Admin changed a product',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'user_name' => 'Priority Superadmin',
                'user_role' => 'superadmin',
                'event' => 'login',
                'description' => 'Superadmin entered the system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-trails.index'))
            ->assertOk()
            ->assertSee('Admin changed a product')
            ->assertDontSee('Superadmin entered the system')
            ->assertDontSee('Priority Superadmin');
    }

    public function test_superadmin_can_view_superadmin_and_pos_audit_activity(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);

        DB::table('pos_audit_trails')->insert([
            'user_id' => $superadmin->id,
            'user_name' => 'Priority Superadmin',
            'user_role' => 'superadmin',
            'event' => 'login',
            'description' => 'Superadmin entered the POS side',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('superadmin_audit_logs')->insert([
            'user_id' => $superadmin->id,
            'action' => 'updated_security_settings',
            'subject_type' => null,
            'subject_id' => null,
            'properties' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($superadmin)
            ->get(route('superadmin.audit', ['source' => 'pos']))
            ->assertOk()
            ->assertSee('Superadmin entered the POS side');

        $this->actingAs($superadmin)
            ->get(route('superadmin.audit', ['source' => 'superadmin']))
            ->assertOk()
            ->assertSee('Updated security settings');
    }
}
