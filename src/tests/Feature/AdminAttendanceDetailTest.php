<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminAttendanceDetailTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠詳細画面に表示されるデータが選択したものになっている
    public function test_admin_can_view_attendance_detail()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $attendance = Attendance::first();

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/{$attendance->id}");

        $response->assertStatus(200);

        $response->assertSee($attendance->user->name);
        $response->assertSee($attendance->work_date->format('Y年'));
        $response->assertSee($attendance->work_date->format('n月j日'));
        $response->assertSee($attendance->clock_in_at->format('H:i'));
        $response->assertSee($attendance->clock_out_at->format('H:i'));
    }

    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_clock_in_after_clock_out_validation()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $attendance = Attendance::first();

        $response = $this->actingAs($admin)
            ->from("/admin/attendance/{$attendance->id}")
            ->post("/admin/attendance/{$attendance->id}",[
                'clock_in_at' => '19:00',
                'clock_out_at' => '18:00',
                'break_start_at' => [],
                'break_end_at' => [],
                'remarks' => '修正',
            ]);

        $response->assertSessionHasErrors([
            'clock_in_at' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_break_start_after_clock_out_validation()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $attendance = Attendance::first();

        $response = $this->actingAs($admin)
            ->from("/admin/attendance/{$attendance->id}")
            ->post("/admin/attendance/{$attendance->id}",[
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['19:00'],
                'break_end_at' => ['19:30'],
                'remarks' => '修正',
            ]);

        $response->assertSessionHasErrors([
            'break_start_at.0' => '休憩時間が不適切な値です'
        ]);
    }

    //休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_break_end_after_clock_out_validation()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $attendance = Attendance::first();

        $response = $this->actingAs($admin)
            ->from("/admin/attendance/{$attendance->id}")
            ->post("/admin/attendance/{$attendance->id}",[
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['17:00'],
                'break_end_at' => ['19:00'],
                'remarks' => '修正',
            ]);

        $response->assertSessionHasErrors([
            'break_end_at.0' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    //備考欄が未入力の場合のエラーメッセージが表示される
    public function test_admin_remarks_required_validation()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $attendance = Attendance::first();

        $response = $this->actingAs($admin)
            ->from("/admin/attendance/{$attendance->id}")
            ->post("/admin/attendance/{$attendance->id}",[
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => [],
                'break_end_at' => [],
                'remarks' => '',
            ]);

        $response->assertSessionHasErrors([
            'remarks' => '備考を記入してください'
        ]);
    }
}
