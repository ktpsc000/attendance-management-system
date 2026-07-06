<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BreakTest extends TestCase
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

    // 休憩ボタンが正しく機能する
    public function test_break_start()
    {
        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)->get('/attendance');
        $this->post('/attendance/break-start');
        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    // 休憩は一日に何回でもできる
    public function test_break_can_be_started_multiple_times()
    {
        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        $this->post('/attendance/break-start');
        $this->post('/attendance/break-end');

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');
    }

    // 休憩戻ボタンが正しく機能する
    public function test_break_end()
    {
        $user = User::factory()
                ->working()
                ->create();

            Attendance::factory()
                ->working()
                ->create([
                    'user_id' => $user->id,
                ]);

            $this->actingAs($user);

            $this->post('/attendance/break-start');
            $this->post('/attendance/break-end');

            $response = $this->get('/attendance');

            $response->assertSee('出勤中');
    }

    // 休憩戻は一日に何回でもできる
    public function test_break_end_can_be_used_multiple_times()
    {
        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        $this->post('/attendance/break-start');
        $this->post('/attendance/break-end');

        $response = $this->post('/attendance/break-start');
        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');
    }

    // 休憩時刻が勤怠一覧画面で確認できる
    public function test_break_time_displayed_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 7, 9, 0));

        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        Carbon::setTestNow(Carbon::create(2026, 7, 7, 12, 0));
        $this->post('/attendance/break-start');

        Carbon::setTestNow(Carbon::create(2026, 7, 7, 13, 0));
        $this->post('/attendance/break-end');

        $response = $this->get('/attendance/list');
        $response->assertSee('1:00');
    }
}
