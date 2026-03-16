<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Codigo_ref;
use App\Models\Leads;
use App\Models\PurchaseEvent;
use App\Models\WhatsappAtendimento;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Notifications\EmailVerificationCodeNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Constantes para os papÃ©is do usuÃ¡rio
    const NIVEL_ACESSO_ADMIN = 'admin';
    const NIVEL_ACESSO_USER = 'user';

    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'email_verification_code_hash',
        'email_verification_code_expires_at',
        'email_verification_code_sent_at',
        'password',
        'nivel_acesso',
        'thumb',
        'cpf',
        'telefone_pessoal_1',
        'telefone_pessoal_1_pending',
        'telefone_pessoal_2',
        'telefone_pessoal_1_verification_code_hash',
        'telefone_pessoal_1_verification_code_expires_at',
        'telefone_pessoal_1_verification_code_sent_at',
        'telefone_pessoal_1_verified_at',
        'apelido',
        'nome_empresa',
        'logo_padrao_path',
        'logo_dark_path',
        'mentorado',
        'meta_pixel_id',
        'meta_pixel_api',
        'meta_pixel_eventcode',
        'google_ads',
        'whatsapp_atendimento',
        'whatsapp_atendimento_tempo',
        'w3_whatsapp_float_enabled',
        'w3_whatsapp_float_delay_seconds',
        'w3_whatsapp_float_whatsapp_atendimento_id',
        'dominio',
        'dominio_externo',
        'home_page_layout',
        'home_page_destination',
        'home_page_whatsapp_flow',
        'meta_conta_anuncios_id',
        'meta_pagina_id',
        'meta_instagram_id',
        'meta_app_id',
        'faturamento_total',
        'comissao_total',
        'numero_total_vendas',
        'formulario_whatsapp',
        'formulario_pre_checkout',
        'many_api',
        'many_cliente_telefone_id'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code_hash',
        'telefone_pessoal_1_verification_code_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_code_expires_at' => 'datetime',
        'email_verification_code_sent_at' => 'datetime',
        'password' => 'hashed',
        'telefone_pessoal_1_verification_code_expires_at' => 'datetime',
        'telefone_pessoal_1_verification_code_sent_at' => 'datetime',
        'telefone_pessoal_1_verified_at' => 'datetime',
        'w3_whatsapp_float_enabled' => 'boolean',
        'w3_whatsapp_float_delay_seconds' => 'integer',
        'w3_whatsapp_float_whatsapp_atendimento_id' => 'integer',
        'home_page_destination' => 'string',
        'home_page_whatsapp_flow' => 'string',
        'nome_empresa' => 'string',
        'logo_padrao_path' => 'string',
        'logo_dark_path' => 'string',
    ];

    public function sendEmailVerificationNotification(): void
    {
        $code = $this->issueEmailVerificationCode();

        $this->notify(new EmailVerificationCodeNotification($code));
    }

    public function issueEmailVerificationCode(int $minutes = 15): string
    {
        $code = $this->generateVerificationCode();

        $this->forceFill([
            'email_verification_code_hash' => Hash::make($code),
            'email_verification_code_expires_at' => now()->addMinutes($minutes),
            'email_verification_code_sent_at' => now(),
        ])->save();

        return $code;
    }

    public function clearEmailVerificationCode(): void
    {
        $this->forceFill([
            'email_verification_code_hash' => null,
            'email_verification_code_expires_at' => null,
            'email_verification_code_sent_at' => null,
        ])->save();
    }

    public function matchesEmailVerificationCode(string $code): bool
    {
        $hash = (string) ($this->email_verification_code_hash ?? '');
        $expiresAt = $this->email_verification_code_expires_at;

        if ($hash === '' || !$expiresAt || now()->greaterThan($expiresAt)) {
            return false;
        }

        return Hash::check($code, $hash);
    }

    public function issuePhoneVerificationCode(string $phone, int $minutes = 15): string
    {
        $code = $this->generateVerificationCode();

        $this->forceFill([
            'telefone_pessoal_1_pending' => $phone,
            'telefone_pessoal_1_verification_code_hash' => Hash::make($code),
            'telefone_pessoal_1_verification_code_expires_at' => now()->addMinutes($minutes),
            'telefone_pessoal_1_verification_code_sent_at' => now(),
        ])->save();

        return $code;
    }

    public function clearPhoneVerificationCode(): void
    {
        $this->forceFill([
            'telefone_pessoal_1_pending' => null,
            'telefone_pessoal_1_verification_code_hash' => null,
            'telefone_pessoal_1_verification_code_expires_at' => null,
            'telefone_pessoal_1_verification_code_sent_at' => null,
        ])->save();
    }

    public function matchesPhoneVerificationCode(string $code): bool
    {
        $hash = (string) ($this->telefone_pessoal_1_verification_code_hash ?? '');
        $expiresAt = $this->telefone_pessoal_1_verification_code_expires_at;

        if ($hash === '' || !$expiresAt || now()->greaterThan($expiresAt)) {
            return false;
        }

        return Hash::check($code, $hash);
    }

    public function confirmPhoneVerification(): void
    {
        $pendingPhone = trim((string) ($this->telefone_pessoal_1_pending ?? ''));

        $this->forceFill([
            'telefone_pessoal_1' => $pendingPhone !== '' ? $pendingPhone : $this->telefone_pessoal_1,
            'telefone_pessoal_1_pending' => null,
            'telefone_pessoal_1_verification_code_hash' => null,
            'telefone_pessoal_1_verification_code_expires_at' => null,
            'telefone_pessoal_1_verification_code_sent_at' => null,
            'telefone_pessoal_1_verified_at' => now(),
        ])->save();
    }

    public function hasVerifiedPhone(): bool
    {
        return !empty($this->telefone_pessoal_1) && !empty($this->telefone_pessoal_1_verified_at);
    }

    private function generateVerificationCode(int $length = 6): string
    {
        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    public function codigo_ref()
    {
        return $this->hasMany(Codigo_ref::class, 'user_id', 'id');
    }

    public function codigoRefPorCurso($curso_id)
    {
        $codigo_ref = $this->codigo_ref()
                    ->where('curso_id', $curso_id)
                    ->where('user_id', $this->id)
                    ->first();

        // Verifica se o cÃ³digo contÃ©m apenas nÃºmeros ou "/"
        if ($codigo_ref && (is_numeric($codigo_ref->codigo_ref) || str_contains($codigo_ref->codigo_ref, '/'))) {
            return false;
        }

        return str_replace(" ", "", $codigo_ref->codigo_ref);
    }


    public function leads()
    {
        return $this->hasMany(Leads::class);
    }

    public function whatsappAtendimentos(): HasMany
    {
        return $this->hasMany(WhatsappAtendimento::class, 'user_id', 'id');
    }
    
    public function purchaseEvents() //PEGAR OS LEADS DO AFILIADO
    {
        return $this->hasManyThrough(
            PurchaseEvent::class,
            Codigo_ref::class,
            'user_id', // Foreign key on Codigo_ref table...
            'affiliate_code', // Foreign key on PurchaseEvent table...
            'id', // Local key on User table...
            'codigo_ref' // Local key on Codigo_ref table...
        );
    }

    public function approvedCompletedPurchaseEventsSummary() //COMPRAS APROVADAS
    {
        $purchaseEvents = $this->purchaseEvents()
            ->whereIn('purchase_status', ['APPROVED', 'COMPLETED'])
            ->get(['purchase_original_offer_price_value']);

        $totalCount = $purchaseEvents->count();
        $totalSum = $purchaseEvents->sum('purchase_original_offer_price_value');

        return [
            'total_count' => $totalCount,
            'total_sum' => $totalSum,
        ];
    }

    public static function getMonthlyRegistrationsLastSixMonths()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(4)->startOfMonth();
        $endDate = $now->endOfMonth();

        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER)"
            : "YEAR(created_at)";
        $monthExpression = $driver === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : "MONTH(created_at)";

        return static::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("$yearExpression as year, $monthExpression as month, COUNT(*) as total, 
                        COUNT(CASE WHEN dominio IS NOT NULL OR dominio_externo IS NOT NULL THEN 1 END) as total_with_dominio")
            ->groupByRaw("$yearExpression, $monthExpression")
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => $row->year,
                    'month' => $row->month,
                    'total' => $row->total,
                    'total_with_dominio' => $row->total_with_dominio,
                ];
            })
            ->toArray();
    }

}
