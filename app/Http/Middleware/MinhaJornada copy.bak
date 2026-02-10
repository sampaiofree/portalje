<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\Codigo_ref;
use App\Models\PurchaseEvent;

class MinhaJornada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Obtém o usuário autenticado
            $user = Auth::user();
            if($user->id==13){ $user->id=43011;}   
            $ref = Codigo_ref::where('user_id', $user->id)->first();
            
            
            $codigo_ref = new Codigo_ref();
            $codigos_ref = $codigo_ref->where('user_id', $user->id)->pluck('codigo_ref'); 
            $ativos = $codigo_ref->where('user_id', $user->id)->where('mostrar_curso', '<>', '')->where('mostrar_curso', '<>', '0')->whereNotNull('mostrar_curso')->get(); 
            if($ativos->isEmpty()){$cursos_ativos = false;}else{$cursos_ativos = true;};
            $leads = PurchaseEvent::whereIn('affiliate_code', $codigos_ref)->first();

            $quantidade_vendas = PurchaseEvent::whereIn('affiliate_code', $codigos_ref)
                                            ->where('purchase_status', 'APPROVED')
                                            ->orWhere('purchase_status', 'COMPLETE')
                                            ->count();

                                           // Calcular a soma dos valores purchase_full_price_value para vendas aprovadas ou completas
                                           $purchaseEvents = PurchaseEvent::whereIn('affiliate_code', $codigos_ref)
                                           ->where(function($query) {
                                               $query->where('purchase_status', 'APPROVED')
                                                     ->orWhere('purchase_status', 'COMPLETE');
                                           })->get(['purchase_original_offer_price_value']);
                                    
                                       // Debugging: output the values
                                       foreach ($purchaseEvents as $event) {
                                           //echo $event->purchase_original_offer_price_value;
                                       }
                                    
                                       // Calculating the sum
                                       $total_sum = $purchaseEvents->sum('purchase_original_offer_price_value');


            if($quantidade_vendas>=1){$uma_venda = true;} else{$uma_venda = false;} //CINCO VENDAS
            if($quantidade_vendas>=5){$cinco_vendas = true;} else{$cinco_vendas = false;} //CINCO VENDAS
            if($quantidade_vendas>=10){$dez_vendas = true;} else{$dez_vendas = false;} //CINCO VENDAS
            if($quantidade_vendas>=50){$cinquenta_vendas = true;} else{$cinquenta_vendas = false;} //CINCO VENDAS
            if($quantidade_vendas>=100){$cem_vendas = true;} else{$cem_vendas = false;} //CINCO VENDAS
            if($quantidade_vendas>=500){$quinhentas_vendas = true;} else{$quinhentas_vendas = false;} //CINCO VENDAS
            if($quantidade_vendas>=1000){$mil_vendas = true;} else{$mil_vendas = false;} //CINCO VENDAS

            if($user->formulario_whatsapp OR $user->formulario_pre_checkout){$formulario = true;}else{$formulario = false;}

            // Monta a jornada usando os dados do usuário
            $minha_jornada = [
                [
                    "concluido" => $user->dominio,
                    "titulo" => "Cadastre o seu domínio",
                    "link" => "DcwJ6yeQMdo",
                    "texto" => "",
                    "aulas" => false,
                ],
                [
                    "concluido" => $user->whatsapp_atendimento,
                    "titulo" => "Cadastre seu WhatsApp de Atendimento",
                    "link" => "lkRWo6_Lc78",
                    "texto" => "",
                    "aulas" => false,
                ],
                [
                    "concluido" => $ref,
                    "titulo" => "Cadastre seus produtos",
                    "link" => "G3_UjvwizSc",
                    "texto" => "<h5>Links e vídeos mencionados no vídeo</h5><a class='d-block' href='https://www.youtube.com/playlist?list=PL8UPaaNJEdSBxmq-xDp3pFcCEYVEf89Ua' target='_blank'><i class='me-1 ri-youtube-line '></i>O que é um afiliado e Como Funciona a Hotmart?</a><a class='d-block' href='https://sso.hotmart.com/signup' target='_blank'><i class='me-1 ri-external-link-line '></i>Link para fazer o cadastro na Hotmart</a>",
                    "aulas" => [
                        [
                            "titulo" => "Como cadastrar seus produtos",
                            "link" => "G3_UjvwizSc",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Como ativar e desativar os cursos?",
                            "link" => "ToROzX6dq9k",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Como corrigir erros no site",
                            "link" => "7xIt3PLPZvw",
                            "texto" => "",
                        ],
                    ]
                ],
                [
                    "concluido" => $formulario,
                    "titulo" => "Comece a divulgar seus links",
                    "link" => "",
                    "texto" => "",
                    "aulas" => [
                        [
                            "titulo" => "Qual link devo usar?",
                            "link" => "F_RYIH7NpOQ",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Como personalizar os seus links?",
                            "link" => "pm48QOzVfZA",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Ofereça aulas gratuitas",
                            "link" => "Y6r6-XtsxMY",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Suporte para os Parceiros",
                            "link" => "a3pAutrATok",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Ative pelo menos 1 formulário",
                            "link" => "g7L-zGT1jYM",
                            "texto" => "",
                        ],
                    ],
                ],
                [
                    "concluido" => $leads,
                    "titulo" => "Consiga seu Primeiro Lead",
                    "link" => "",
                    "texto" => "",
                    "aulas" => [
                        [
                            "titulo" => "Anunciar em grupos do WhatsAPP",
                            "link" => "vI-eMiiWEyk",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Abordagem individual pelo WhatsAPP",
                            "link" => "lw6nQokHnRo",
                            "texto" => "",
                        ],
                    ],
                ],
                [
                    "concluido" => $uma_venda,
                    "titulo" => "Consiga sua primeira venda",
                    "link" => "",
                    "texto" => "",
                    "aulas" => [
                        [
                            "titulo" => "Consiga Leads de Graça",
                            "link" => "h45Yh10Yico",
                            "texto" => "",
                        ],
                        [
                            "titulo" => "Tráfego Pago para Captação de Leads",
                            "link" => "mfjCq1sT73Y",
                            "texto" => "",
                        ],
                    ],
                ],
                [
                    "concluido" => $cinco_vendas,
                    "titulo" => "Consiga 5 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => [
                        [
                            "titulo" => "A melhor estratégia de vendas para quem está começando",
                            "link" => "97EUtY_otic",
                            "texto" => "Link do Curso do Método Carvalho <br> <a href='https://youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU&feature=shared'>https://youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU</a>",
                        ],
                        [
                            "titulo" => "Como melhorar o seu atendimento?",
                            "link" => "JnIzFq4oa7E",
                            "texto" => "",
                        ],
                    ],
                ],
                [
                    "concluido" => $dez_vendas,
                    "titulo" => "Consiga 10 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => [
                        [
                            "titulo" => "VOCÊ GANHOU UM PRESENTE",
                            "link" => "AkNDPGjXpQY",
                            "texto" => "<a href='https://chat.whatsapp.com/JcnpKdQnMArDfPP99jKAlU'>Clique <strong>AQUI</strong> para entrar no grupo</a>",
                        ],
                    ],
                ],
                [
                    "concluido" => $cinquenta_vendas,
                    "titulo" => "Consiga 50 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => false,
                ],
                [
                    "concluido" => $cem_vendas,
                    "titulo" => "Consiga 100 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => false,
                ],
                [
                    "concluido" => $quinhentas_vendas,
                    "titulo" => "Consiga 500 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => false,
                ],
                [
                    "concluido" => $mil_vendas,
                    "titulo" => "Consiga 1000 vendas",
                    "link" => "",
                    "texto" => "",
                    "aulas" => false,
                ],
            ];
            

          
           // Compartilha os dados com todas as views
           view()->share('minha_jornada', $minha_jornada);
           view()->share('quantidade_vendas', $quantidade_vendas);
           view()->share('total_sum', $total_sum);
        }
        return $next($request);
    }
}
