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
use Carbon\Carbon;

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
        'password',
        'nivel_acesso',
        'thumb',
        'cpf',
        'telefone_pessoal_1',
        'telefone_pessoal_2',
        'apelido',
        'mentorado',
        'meta_pixel_id',
        'meta_pixel_api',
        'meta_pixel_eventcode',
        'google_ads',
        'whatsapp_atendimento',
        'whatsapp_atendimento_tempo',
        'dominio',
        'dominio_externo',
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
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
