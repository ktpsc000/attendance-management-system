<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CorrectionRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_clock_in_after_clock_out()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '18:00',
                'clock_out_at' => '09:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['13:00'],
                'remarks' => '修正理由',
            ]);

        $response->assertSessionHasErrors([
            'clock_in_at' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_break_start_after_clock_out()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['19:00'],
                'break_end_at' => ['19:30'],
                'remarks' => '修正理由',
            ]);

        $response->assertSessionHasErrors([
            'break_start_at.0' => '休憩時間が不適切な値です',
        ]);
    }

    //休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_break_end_after_clock_out()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['19:00'],
                'remarks' => '修正理由',
            ]);

        $response->assertSessionHasErrors([
            'break_end_at.0' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    //備考欄が未入力の場合のエラーメッセージが表示される
    public function test_remarks_required()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['13:00'],
                'remarks' => '',
            ]);

        $response->assertSessionHasErrors([
            'remarks' => '備考を記入してください',
        ]);
    }

    //修正申請処理が実行される
    public function test_correction_request_is_displayed_for_admin()
    {
        $user = User::find(1);
        $admin = User::find(3);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['13:00'],
                'remarks' => '修正申請',
            ]);

        $request = CorrectionRequest::first();

        $this->assertNotNull($request);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list');

        $response->assertSee($user->name);

        $response = $this->get("/admin/stamp_correction_request/approve/{$request->id}");

        $response->assertSee($user->name);
    }

    //「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_pending_requests_are_displayed()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['13:00'],
                'remarks' => 'テスト申請',
            ]);

        $response = $this->get('/stamp_correction_request/list?tab=pending');
        $response->assertSee('承認待ち');
        $response->assertSee('テスト申請');
    }

    //「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_approved_requests_are_displayed()
    {
        $user = User::find(1);
        $admin = User::find(3);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->actingAs($user)
            ->post("/attendance/detail/{$attendance->id}", [
                'clock_in_at' => '09:00',
                'clock_out_at' => '18:00',
                'break_start_at' => ['12:00'],
                'break_end_at' => ['13:00'],
                'remarks' => '承認テスト',
            ]);

        $request = CorrectionRequest::latest()->first();

        $this->actingAs($admin)
            ->post("/admin/stamp_correction_request/approve/{$request->id}");

        $response = $this->actingAs($user)
            ->get('/stamp_correction_request/list?tab=approved');

        $response->assertSee('承認済み');
        $response->assertSee('承認テスト');
    }

    //各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_request_detail_link()
    {
        $user = User::find(1);

        $attendance = Attendance::where('user_id', $user->id)->first();

        CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'request_clock_in_at' => '09:00',
            'request_clock_out_at' => '18:00',
            'remarks' => '修正',
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)
            ->get('/stamp_correction_request/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
