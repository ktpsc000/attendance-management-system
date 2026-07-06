<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_name_is_displayed()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($user->name);
    }

    //勤怠詳細画面の「日付」が選択した日付になっている
    public function test_work_date_is_displayed()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($attendance->work_date->format('Y年'));
        $response->assertSee($attendance->work_date->format('n月j日'));
    }

    //「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_clock_time_is_displayed()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($attendance->clock_in_at->format('H:i'));
        $response->assertSee($attendance->clock_out_at->format('H:i'));
    }

    //「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_break_time_is_displayed()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();
        $break = $attendance->breaks()->first();

        $response = $this->actingAs($user)
            ->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($break->break_start_at->format('H:i'));
        $response->assertSee($break->break_end_at->format('H:i'));
    }
}
