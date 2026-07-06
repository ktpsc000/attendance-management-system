<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ClockOutTest extends TestCase
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

    //退勤ボタンが正しく機能する
    public function test_clock_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 7, 18, 0));

        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('退勤');

        $this->post('/attendance/clock-out');

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    //退勤時刻が勤怠一覧画面で確認できる
    public function test_clock_out_time_displayed_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 7, 9, 0));

        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->post('/attendance/clock-in');


        Carbon::setTestNow(Carbon::create(2026, 7, 7, 18, 0));
        $this->post('/attendance/clock-out');

        $response = $this->get('/attendance/list');
        $response->assertSee('18:00');
    }
}
