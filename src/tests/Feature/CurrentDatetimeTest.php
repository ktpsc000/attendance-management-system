<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CurrentDatetimeTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //現在日時取得
    public function test_current_datetime_is_displayed()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee(now()->format('Y年n月j日'));
    }
}
