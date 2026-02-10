<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Throwable;

use App\Models\Dados_portal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        
        Paginator::useBootstrapFive();

        try {
            $dados_portal = Schema::hasTable('portal_informacoes') ? Dados_portal::all() : collect();
        } catch (Throwable $e) {
            $dados_portal = collect();
        }

        View::share('dados_portal', $dados_portal);

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifique seu e-mail')
                ->line('Clique no botão abaixo para verificar seu e-mail.')
                ->action('Verifique seu e-mail', $url);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $url) {

            $urlCompleta = URL::route('password.reset', ['token' => $url, 'email' => $notifiable->getEmailForPasswordReset()]);

            return (new MailMessage)
                ->subject('Redefinição de senha')
                ->line('Você está recebendo este e-mail por que recebemos um pedido de redefinição de senha.')
                ->action('Alterar senha', $urlCompleta)
                ->line('Este link irá expirar em 15 minutos.')
                ->line('Se você não fez esta solicitação, nenhuma ação é necessária.');
        });
    }
}
