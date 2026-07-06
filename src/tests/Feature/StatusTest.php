<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use DatabaseMigrations;

    //勤務外
    public function test_status_off_duty()
    {
        $user = User::factory()->create();

        Attendance::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }


    //出勤中
    public function test_status_working()
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

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }


    //休憩中
    public function test_status_break()
    {
        $user = User::factory()
            ->break()
            ->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }


    //退勤済
    public function test_status_finished()
    {
        $user = User::factory()
            ->working()
            ->create();

        Attendance::factory()
            ->finished()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
