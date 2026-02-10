<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url){

            return (new MailMessage)
                ->subject('Verifique seu e-mail')
                ->line('Clique no botão abaixo para verificar seu e-mail.')
                ->action('Verifique seu e-mail', $url)
                ->line('Se você não criou uma conta nenhuma ação é necessária.');

        });

        ResetPassword::toMailUsing(function ($notifiable, $url){

            return (new MailMessage)
                ->subject('Redefinição de senha')
                ->line('Você está recebendo este e-mail por que recebemos um pedido de redefinição de senha.')
                ->action(('Alterar senha'), $url)
                ->line('Este link irá expirar em 15 minutos.')
                ->line('Se você não fez esta solicitação, nenhuma ação é necessária.');

        });
    }
}
