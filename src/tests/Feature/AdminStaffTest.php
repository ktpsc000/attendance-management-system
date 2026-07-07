<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminStaffTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_admin_can_view_all_users()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $user1 = User::where('email', 'user1@example.com')->first();
        $user2 = User::where('email', 'user2@example.com')->first();


        $response = $this->actingAs($admin)
            ->get('/admin/staff/list');


        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee($user1->email);

        $response->assertSee($user2->name);
        $response->assertSee($user2->email);
    }

    //ユーザーの勤怠情報が正しく表示される
    public function test_admin_can_view_user_attendance()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee(
            $attendance->clock_in_at->format('H:i')
        );

        $response->assertSee(
            $attendance->clock_out_at->format('H:i')
        );
    }

    //「前月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_view_previous_month_attendance()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $response = $this->actingAs($admin)
            ->get('/admin/attendance/staff/1?year=2026&month=6');

        $response->assertStatus(200);
        $response->assertSee('2026/06');
    }

    //「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_view_next_month_attendance()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $response = $this->actingAs($admin)
            ->get('/admin/attendance/staff/1?year=2026&month=8');

        $response->assertStatus(200);
        $response->assertSee('2026/08');
    }

    //「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_admin_can_view_attendance_detail()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee($attendance->work_date->format('Y年'));
        $response->assertSee($attendance->work_date->format('n月j日'));
    }
}
