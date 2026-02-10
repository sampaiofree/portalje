<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Codigo_ref;
use App\Models\Leads;
use App\Models\PurchaseEvent;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Constantes para os papéis do usuário
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

        // Verifica se o código contém apenas números ou "/"
        if ($codigo_ref && (is_numeric($codigo_ref->codigo_ref) || str_contains($codigo_ref->codigo_ref, '/'))) {
            return false;
        }

        return str_replace(" ", "", $codigo_ref->codigo_ref);
    }


    public function leads()
    {
        return $this->hasMany(Leads::class);
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

        return static::whereBetween('created_at', [$startDate, $endDate])
            // Seleciona o ano, o mês, a quantidade total e a quantidade com 'dominio' e 'dominio_externo' não nulos
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total, 
                        COUNT(CASE WHEN dominio IS NOT NULL OR dominio_externo IS NOT NULL THEN 1 END) as total_with_dominio')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => $row->year,
                    'month' => $row->month,
                    'total' => $row->total,  // Total de cadastros
                    'total_with_dominio' => $row->total_with_dominio,  // Total de cadastros com dominio e dominio_externo não nulos
                ];
            })
            ->toArray();
    }

}
