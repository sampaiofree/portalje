<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Throwable;

class PhoneVerificationService
{
    public function __construct(private readonly BotConversa $botConversa)
    {
    }

    public function start(User $user, string $phone): void
    {
        $webhookUrl = trim((string) Config::get('services.botconversa.verification_webhook_url', ''));
        $requestPhone = trim((string) Config::get('services.botconversa.verification_request_phone', ''));

        if ($webhookUrl === '' || $requestPhone === '') {
            throw ValidationException::withMessages([
                'telefone_pessoal_1' => ['A verificacao de WhatsApp nao esta configurada no ambiente.'],
            ]);
        }

        $code = $user->issuePhoneVerificationCode($phone);

        try {
            $response = $this->botConversa->enviar_webhook($webhookUrl, [
                'whatsapp' => $phone,
                'codigo' => $code,
                'nome' => (string) $user->name,
                'email' => (string) $user->email,
            ]);
        } catch (Throwable $exception) {
            $user->clearPhoneVerificationCode();

            throw ValidationException::withMessages([
                'telefone_pessoal_1' => ['Nao foi possivel iniciar a verificacao do WhatsApp agora.'],
            ]);
        }

        $successful = (bool) ($response['successful'] ?? false);
        $status = (int) ($response['status'] ?? 0);

        if (!$successful && !in_array($status, [200, 201, 202, 204], true)) {
            $user->clearPhoneVerificationCode();

            throw ValidationException::withMessages([
                'telefone_pessoal_1' => ['Nao foi possivel iniciar a verificacao do WhatsApp agora.'],
            ]);
        }
    }

    public function requestPhone(): string
    {
        return preg_replace('/\D/', '', (string) Config::get('services.botconversa.verification_request_phone', '')) ?: '';
    }

    public function requestText(): string
    {
        return 'Solicito meu codigo de verificacao';
    }

    public function requestUrl(): ?string
    {
        $phone = $this->requestPhone();

        if ($phone === '') {
            return null;
        }

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($this->requestText());
    }
}
