<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin\UserModel;

class RBACTest extends TestCase
{
    /**
     * Test login restriction
     */
    public function test_login_restriction()
    {
        // Admin (Role 1) - Should Login
        $response = $this->post('/admin/proseslogin', [
            'user' => 'admin',
            'pwd' => 'password'
        ]);
        $response->assertRedirect('/admin/dashboard');

        // Pegawai (Role 3) - Should be Restricted
        $response = $this->post('/admin/proseslogin', [
            'user' => 'pegawai',
            'pwd' => 'password'
        ]);
        $response->assertSessionHas('msg', 'Role Anda tidak diizinkan untuk login!');
    }

    /**
     * Test permission matrix
     */
    public function test_permission_matrix()
    {
        // 1. Admin accessing User Management (Should be 200)
        $admin = UserModel::where('role_id', 1)->first();
        if ($admin) {
            $response = $this->withSession(['user' => $admin])
                             ->get('/admin/user');
            $response->assertStatus(200);

            $response = $this->withSession(['user' => $admin])
                             ->get('/admin/role');
            $response->assertStatus(200);

            $response = $this->withSession(['user' => $admin])
                             ->get('/admin/akses/role');
            $response->assertStatus(200);
        }

        // 2. Staff Gudang accessing User Management (Should be 403)
        $staff = UserModel::where('role_id', 2)->first();
        if ($staff) {
            $response = $this->withSession(['user' => $staff])
                             ->get('/admin/user');
            $response->assertStatus(403);

            $response = $this->withSession(['user' => $staff])
                             ->get('/admin/role');
            $response->assertStatus(403);

            $response = $this->withSession(['user' => $staff])
                             ->get('/admin/akses/role');
            $response->assertStatus(403);
        }
    }

    /**
     * Test audit trail logging
     */
    public function test_audit_trail_logging()
    {
        $admin = UserModel::where('role_id', 1)->first();
        if ($admin) {
            // Simulate adding a user
            $response = $this->withSession(['user' => $admin])
                 ->post('/admin/user', [
                    'username' => 'testuser',
                    'nmlengkap' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 2,
                    'pwd' => 'password123'
                 ]);
            
            // Check if audit log exists
            $this->assertDatabaseHas('tbl_audit_log', [
                'user_id' => $admin->user_id,
                'activity' => 'CREATE',
                'module' => 'User Management'
            ]);
        }
    }
}
