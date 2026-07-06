<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 7, 7, 9, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    //自分が行った勤怠情報が全て表示されている
    public function test_all_attendance_records_are_displayed()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today()->subDays(1),
            'clock_in_at' => today()->subDays(1)->setTime(9,0),
            'clock_out_at' => today()->subDays(1)->setTime(18,0),
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in_at' => today()->setTime(9,0),
            'clock_out_at' => today()->setTime(18,0),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    //勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_current_month_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance/list');
        $response->assertSee('2026/07');
    }

    //「前月」を押下した時に表示月の前月の情報が表示される
    public function test_previous_month_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance/list?year=2026&month=6');
        $response->assertSee('2026/06');
    }

    //「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_next_month_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance/list?year=2026&month=8');
        $response->assertSee('2026/08');
    }

    //「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_attendance_detail_page_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->finished()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
    }
}
