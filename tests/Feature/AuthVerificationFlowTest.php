<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_email_code_and_redirects_to_email_verification_screen(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'Maria Teste',
            'email' => 'maria@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::query()->where('email', 'maria@example.com')->firstOrFail();

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email_verified_at);
        $this->assertNotNull($user->email_verification_code_hash);
        $this->assertNotNull($user->email_verification_code_expires_at);

        Notification::assertSentTo($user, EmailVerificationCodeNotification::class, function (EmailVerificationCodeNotification $notification) {
            return preg_match('/^[0-9]{6}$/', $notification->code) === 1;
        });
    }

    public function test_email_code_can_confirm_email_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->unverified()->unverifiedPhone()->create();
        $code = $user->issueEmailVerificationCode();

        $response = $this->actingAs($user)->post(route('verification.code'), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_code_hash);
        $this->assertNull($user->email_verification_code_expires_at);
    }

    public function test_signed_email_verification_redirects_to_dashboard(): void
    {
        $user = User::factory()->unverified()->unverifiedPhone()->create();
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verified_user_is_redirected_to_dashboard_from_email_verification(): void
    {
        $user = User::factory()->unverifiedPhone()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->get(route('verification.notice'))->assertRedirect(route('dashboard'));
        $this->actingAs($user)->post(route('verification.send'))->assertRedirect(route('dashboard'));
        $this->actingAs($user)->post(route('verification.code'), ['code' => '123456'])->assertRedirect(route('dashboard'));
    }

    public function test_resending_email_code_invalidates_previous_code(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->unverifiedPhone()->create();
        $oldCode = $user->issueEmailVerificationCode();

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();

        $user->refresh();

        $this->assertFalse($user->matchesEmailVerificationCode($oldCode));

        Notification::assertSentTo($user, EmailVerificationCodeNotification::class);
    }

    public function test_user_dashboard_requires_verified_email_but_not_verified_phone(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $emailNaoVerificado = User::factory()->unverified()->create();
        $responseEmail = $this->actingAs($emailNaoVerificado)->get(route('dashboard'));
        $responseEmail->assertRedirect(route('verification.notice'));

        auth()->logout();

        $telefoneNaoVerificado = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);
        $responsePhone = $this->actingAs($telefoneNaoVerificado)->get(route('dashboard'));
        $responsePhone->assertOk();

        auth()->logout();

        $verificado = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
        ]);
        $responseOk = $this->actingAs($verificado)->get(route('dashboard'));
        $responseOk->assertOk();
    }

    public function test_phone_verification_screen_redirects_to_dashboard_without_confirming_pending_phone(): void
    {
        $user = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);
        $user->issuePhoneVerificationCode('5562999998888');

        $this->actingAs($user)->get(route('phone.verification.notice'))
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertNull($user->telefone_pessoal_1_verified_at);
        $this->assertSame('5562999998888', $user->telefone_pessoal_1_pending);
    }

    public function test_phone_verification_screen_requires_login(): void
    {
        $this->get(route('phone.verification.notice'))->assertRedirect(route('login'));
    }

    public function test_phone_verification_screen_requires_verified_email(): void
    {
        $user = User::factory()->unverified()->unverifiedPhone()->create();

        $this->actingAs($user)->get(route('phone.verification.notice'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_phone_verification_send_dispatches_botconversa_webhook_and_stores_pending_phone(): void
    {
        config([
            'services.botconversa.verification_webhook_url' => 'https://bot.example/webhook',
            'services.botconversa.verification_request_phone' => '5511954490511',
        ]);

        Http::fake([
            'https://bot.example/webhook' => Http::response(['ok' => true], 200),
        ]);

        $user = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('phone.verification.send'), [
            'telefone_pessoal_1' => '5562999998888',
        ]);

        $response->assertRedirect(route('phone.verification.notice'));

        $user->refresh();

        $this->assertSame('5562999998888', $user->telefone_pessoal_1_pending);
        $this->assertNull($user->telefone_pessoal_1);
        $this->assertNotNull($user->telefone_pessoal_1_verification_code_hash);
        $this->assertNotNull($user->telefone_pessoal_1_verification_code_expires_at);

        Http::assertSent(function (HttpRequest $request) use ($user) {
            $payload = $request->data();
            $user->refresh();

            return $request->url() === 'https://bot.example/webhook'
                && ($payload['whatsapp'] ?? null) === '5562999998888'
                && ($payload['nome'] ?? null) === $user->name
                && ($payload['email'] ?? null) === $user->email
                && preg_match('/^[0-9]{6}$/', (string) ($payload['codigo'] ?? '')) === 1
                && Hash::check((string) $payload['codigo'], (string) $user->telefone_pessoal_1_verification_code_hash);
        });
    }

    public function test_phone_verification_send_accepts_eleven_digit_phone(): void
    {
        config([
            'services.botconversa.verification_webhook_url' => 'https://bot.example/webhook',
            'services.botconversa.verification_request_phone' => '5511954490511',
        ]);

        Http::fake([
            'https://bot.example/webhook' => Http::response(['ok' => true], 200),
        ]);

        $user = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('phone.verification.send'), [
            'telefone_pessoal_1' => '62999998888',
        ]);

        $response->assertRedirect(route('phone.verification.notice'));

        $user->refresh();

        $this->assertSame('62999998888', $user->telefone_pessoal_1_pending);
    }

    public function test_phone_verification_confirmation_promotes_pending_phone(): void
    {
        $user = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);
        $code = $user->issuePhoneVerificationCode('5562999998888');

        $response = $this->actingAs($user)->post(route('phone.verification.confirm'), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();

        $this->assertSame('5562999998888', $user->telefone_pessoal_1);
        $this->assertNotNull($user->telefone_pessoal_1_verified_at);
        $this->assertNull($user->telefone_pessoal_1_pending);
        $this->assertNull($user->telefone_pessoal_1_verification_code_hash);
    }

    public function test_phone_verification_send_fails_cleanly_when_botconversa_fails(): void
    {
        config([
            'services.botconversa.verification_webhook_url' => 'https://bot.example/webhook',
            'services.botconversa.verification_request_phone' => '5511954490511',
        ]);

        Http::fake([
            'https://bot.example/webhook' => Http::response(['error' => 'fail'], 500),
        ]);

        $user = User::factory()->unverifiedPhone()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('phone.verification.notice'))
            ->post(route('phone.verification.send'), [
                'telefone_pessoal_1' => '5562999998888',
            ]);

        $response->assertRedirect(route('phone.verification.notice'));
        $response->assertSessionHasErrors('telefone_pessoal_1');

        $user->refresh();

        $this->assertNull($user->telefone_pessoal_1_pending);
        $this->assertNull($user->telefone_pessoal_1_verification_code_hash);
    }

    public function test_changing_phone_in_settings_keeps_old_phone_until_new_one_is_confirmed(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        config([
            'services.botconversa.verification_webhook_url' => 'https://bot.example/webhook',
            'services.botconversa.verification_request_phone' => '5511954490511',
        ]);

        Http::fake([
            'https://bot.example/webhook' => Http::response(['ok' => true], 200),
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562888887777',
            'telefone_pessoal_1_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('afiliado_configurar_site'))
            ->post(route('afiliado_configurar_site_post'), [
                'telefone_pessoal_1' => '5562999998888',
            ]);

        $response->assertRedirect(route('phone.verification.notice'));

        $user->refresh();

        $this->assertSame('5562888887777', $user->telefone_pessoal_1);
        $this->assertSame('5562999998888', $user->telefone_pessoal_1_pending);
        $this->assertNotNull($user->telefone_pessoal_1_verified_at);
    }
}
