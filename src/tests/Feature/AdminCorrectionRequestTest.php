<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminCorrectionRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // 承認待ちの修正申請が全て表示されている
    public function test_admin_can_view_pending_requests()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        $request = CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'request_clock_in_at' => now()->setTime(9,0),
            'request_clock_out_at' => now()->setTime(18,0),
            'remarks' => '修正申請',
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('修正申請');
        $response->assertSee($user->name);
    }

    // 承認済みの修正申請が全て表示されている
    public function test_admin_can_view_approved_requests()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'request_clock_in_at' => now()->setTime(9,0),
            'request_clock_out_at' => now()->setTime(18,0),
            'remarks' => '承認済み申請',
            'status' => CorrectionRequest::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
        $response->assertSee('承認済み申請');
    }

    // 修正申請の詳細内容が正しく表示されている
    public function test_admin_can_view_request_detail()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        $request = CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'request_clock_in_at' => now()->setTime(10,0),
            'request_clock_out_at' => now()->setTime(19,0),
            'remarks' => '時間変更',
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/stamp_correction_request/approve/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('時間変更');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    // 修正申請の承認処理が正しく行われる
    public function test_admin_can_approve_request()
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $user = User::where('email', 'user1@example.com')->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->first();

        $request = CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'request_clock_in_at' => now()->setTime(10,0),
            'request_clock_out_at' => now()->setTime(19,0),
            'remarks' => '変更理由',
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/stamp_correction_request/approve/{$request->id}");

        $attendance->refresh();

        $this->assertEquals('10:00', $attendance->clock_in_at->format('H:i'));
        $this->assertEquals('19:00', $attendance->clock_out_at->format('H:i'));
        $this->assertEquals('変更理由', $attendance->remarks);
    }

}
