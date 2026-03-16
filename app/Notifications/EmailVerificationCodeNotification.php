<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu codigo de verificacao')
            ->greeting('Ola' . (!empty($notifiable->name) ? ', ' . $notifiable->name : '') . '!')
            ->line('Use o codigo abaixo para confirmar o seu e-mail:')
            ->line($this->code)
            ->line('Este codigo expira em 15 minutos.')
            ->line('Se voce nao criou uma conta, nenhuma acao e necessaria.');
    }
}
