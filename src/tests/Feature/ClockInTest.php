<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ClockInTest extends TestCase
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

    // 出勤ボタンが正しく機能する
    public function test_clock_in()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $this->post('/attendance/clock-in');

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    // 出勤は一日一回のみできる
    public function test_cannot_clock_in_twice()
    {
        $user = User::factory()->working()->create();

        Attendance::factory()
            ->finished()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertDontSee('/attendance/clock-in');
        $response->assertSee('退勤済');
    }

    // 出勤時刻が勤怠一覧画面で確認できる
    public function test_clock_in_time_displayed_on_attendance_list()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        $this->post('/attendance/clock-in');

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');

    }
}
