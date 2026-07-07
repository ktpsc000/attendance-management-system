<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_会員登録後、認証メールが送信される()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);


        $user = User::where('email', 'test@test.com')->first();


        $this->assertNotNull($user);


        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_認証画面の「認証はこちらから」ボタン押下後、メール認証サイトへ()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
        $response->assertSee('http://localhost:8025', false);
    }

    public function test_メール認証後プロフィール画面へ遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);
        $url = URL::temporarySignedRoute('verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($url);
        $response->assertRedirectContains('/attendance');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
