<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Home_e_cursosController;

use App\Services\OpenAIService;

use App\Models\User;
use App\Models\Lead;
use App\Models\PurchaseEvent;
use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\Dados_portal;
use App\Models\ShortenedUrl; 
use Carbon\Carbon;

use App\Services\ManyChatService;

class ManController extends Controller
{
    




    //DADOS DO USUARIO - AFILIADO OU PRODUTOR - PARA HOME PAGE
    private function dadosusuario_home($dominio){
        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if($dominio!='portalje.org' AND $dominio!='dns.portalje.org' AND $dominio!='jemp.me' AND $dominio!='jovemempreendedor.org' AND $dominio!='dns.jovemempreendedor.org'){            

            $verificar = User::where('dominio', $dominio)->orWhere('dominio_externo', $dominio)->first();
            if($verificar){

                //DADOS DO AFILIADO
                $dados['dados'] = [
                    'afiliado' => true,
                    'whatsapp_atendimento' => $verificar->whatsapp_atendimento,
                    'whatsapp_atendimento_tempo' => $verificar->whatsapp_atendimento_tempo,
                    'meta_pixel_id' => $verificar->meta_pixel_id,
                    'formulario_pre_checkout' => $verificar->formulario_pre_checkout,
                    'formulario_whatsapp' => $verificar->formulario_whatsapp,
                    'user_id' => $verificar->id,
                ];

                $dados['cursos'] = Codigo_ref::where('codigo_ref.user_id', $verificar->id)
                ->join('curso', 'curso.id', '=', 'codigo_ref.curso_id')
                ->select(
                    'codigo_ref.*', 
                    'curso.*',
                )
                ->get();
            

                return $dados;
            }

        }

        $dados_portal = Dados_portal::first();
        $dados['dados'] = [
            'afiliado' => false,
            'whatsapp_atendimento' => $dados_portal->telefone_suporte_alunos,
            'whatsapp_atendimento_tempo' => $dados_portal->whatsapp_atendimento_tempo,
            'meta_pixel_id' => null,
            'formulario_pre_checkout' => $dados_portal->formulario_pre_checkout,
            'formulario_whatsapp' => $dados_portal->formulario_whatsapp,
            'user_id' => null,
            'affiliate_code' => null
        ];
        $dados['cursos'] = Curso::select()->get();
        return $dados;

    }



     //PÁGINA CURSO INDIVIDUAL
     public function curso_individual(Request $request, $curso = null, $desconto = null) {
            
        if (!$curso) {return redirect()->away('https://portalje.org');}        
        // Consulta o banco de dados com o valor de $curso
        $curso = Curso::where('url', $curso)->first();    
        // Verifica se a consulta encontrou o curso
        if (!$curso) {return redirect()->away('https://portalje.org');}

        //FORMATACAO DOS PRECOS
        $dados = explode("x", $curso->preco_parcelado_completo);
        $curso->parcelamento = $dados[0];
        $curso->preco = (float)str_replace('R$', '', str_replace(',', '.', $dados[1]));
        $curso->preco_cheio_completo = (int)str_replace('R$', '', $curso->preco_cheio_completo);
        $curso->preco_cheio = $curso->preco_cheio_completo*3;

        //VERIFICAR DESCONTOS
        if($desconto AND $desconto !='w'){

            $cupons = [
                'o10',
                'o20',
                'o30',
                'o40',
                'o50',
                'o60',
                'o70'
            ];

            foreach($cupons as $cupom){
                if($desconto == $cupom){
                    $cupom = (int)str_replace('o', '', $cupom);
                    $desconto = "&offDiscount=".$cupom."OFF";

                    $indice = (float)str_replace('0', '', $cupom);
                    $indice = $indice*0.1;

                    //$curso->preco_cheio = $indice*0.1;

                    $curso->preco = $curso->preco - ($indice * $curso->preco); 
                    $curso->preco_cheio = $curso->preco_cheio_completo;
                    $curso->preco_cheio_completo = (float)$curso->preco_cheio - ($indice * (float)$curso->preco_cheio);
                }
            }
        }
        
        //FORMATAÇÃO DOS PREÇOS 2
        $curso->preco = number_format($curso->preco, 2, ',', '');
        $curso->preco_cheio_completo = "R$".number_format($curso->preco_cheio_completo, 2, ',', '');
        $curso->preco_parcelado_completo = $curso->parcelamento."xR$".$curso->preco;

        //CONTEUDO
        $curso->conteudo_bonus = $this->lista_conteudo($curso->conteudo_bonus);        
        $curso->areas_de_atuacao = explode("/",$curso->areas_de_atuacao);

        //DADOS DO AFILIADO OU PRODUTOR
        $dados = $this->dadosusuario($request->getHost(), $curso);
        if (!$dados) {return redirect()->away('https://portalje.org');}   
        $curso->whatsapp_atendimento = $dados['whatsapp_atendimento'];
        $curso->whatsapp_atendimento_tempo = $dados['whatsapp_atendimento_tempo'];
        $curso->link_checkout_completo = $dados['link_checkout_completo'].$desconto;
        $curso->meta_pixel_id = $dados['meta_pixel_id'];
        $curso->formulario = $dados['formulario_pre_checkout'];
        $curso->user_id = $dados['user_id'];
        $curso->affiliate_code = $dados['affiliate_code'];
        $curso->origem = 'checkout_completo';

        //PAGINA DIRETO PARA O WHATSAPP
        if($desconto AND $desconto =='w'){
            $curso->formulario = $dados['formulario_whatsapp'];
            if($dados['formulario_whatsapp']){$zap_complemento = "meu nome é {nome},";}else{$zap_complemento = '';}
            $curso->link_checkout_completo = "https://wa.me/$curso->whatsapp_atendimento?text=Olá, $zap_complemento quero fazer minha inscrição no curso de $curso->titulo";
            $curso->origem = 'whatsapp';
        }
            

        return view('novapagina2', compact('curso'));
    }

    //DADOS DO USUARIO - AFILIADO OU PRODUTOR
    private function dadosusuario($dominio, $curso = null){
        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if($dominio!='portalje.org' AND $dominio!='dns.portalje.org' AND $dominio!='jemp.me' AND $dominio!='jovemempreendedor.org' AND $dominio!='dns.jovemempreendedor.org'){            

            $verificar = User::where('dominio', $dominio)->orWhere('dominio_externo', $dominio)->first();
            if($verificar){

                //DADOS DO AFILIADO
                $dados = [
                    'whatsapp_atendimento' => $verificar->whatsapp_atendimento,
                    'whatsapp_atendimento_tempo' => $verificar->whatsapp_atendimento_tempo,
                    'meta_pixel_id' => $verificar->meta_pixel_id,
                    'formulario_pre_checkout' => $verificar->formulario_pre_checkout,
                    'formulario_whatsapp' => $verificar->formulario_whatsapp,
                    'user_id' => $verificar->id,
                    
                ];

                if($curso){
                    $dados_codigo_ref = Codigo_ref::where('curso_id', $curso->id)->where('user_id', $verificar->id)->first();
                    if(!$dados_codigo_ref){return false;}
                    $codigo_ref = $dados_codigo_ref['codigo_ref'];
                    $dados['link_checkout_completo'] = "https://go.hotmart.com/$codigo_ref?ap=$curso->codigo_afiliado_plano_completo";
                    $dados['affiliate_code'] = $dados_codigo_ref['codigo_ref'];
                }

                return $dados;
            }

        }

        $dados_portal = Dados_portal::first();
        $dados = [
            'whatsapp_atendimento' => $dados_portal->telefone_suporte_alunos,
            'whatsapp_atendimento_tempo' => $dados_portal->whatsapp_atendimento_tempo,
            'meta_pixel_id' => null,
            'formulario_pre_checkout' => $dados_portal->formulario_pre_checkout,
            'formulario_whatsapp' => $dados_portal->formulario_whatsapp,
            'user_id' => null,
            'affiliate_code' => null
        ];

        if($curso){
            $dados['link_checkout_completo'] = "$curso->link_checkout_completo&hideBillet=1";
        }

        return $dados;

    }

    public function lista_conteudo($html){

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if(!$html){return null;}

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $listItems = $dom->getElementsByTagName('li');

        $courses = [];
        $currentCourse = null;

        foreach ($listItems as $item) {
            $text = $item->textContent;
            $class = $item->getAttribute('class');

            if (strpos($class, 'ql-indent-1') === false) {
                // Novo curso
                if ($currentCourse) {
                    $courses[] = $currentCourse;
                }
                $currentCourse = ['title' => $text, 'topics' => []];
            } else {
                // Adiciona tópico ao curso atual
                $currentCourse['topics'][] = $text;
            }
        }

        // Adiciona o último curso, se existir
        if ($currentCourse) {
            $courses[] = $currentCourse;
        }

        return $courses;
    }
    

    public function cadastrarCurso()
    {
        // Faz a requisição para a API externa
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        
        if ($response->successful()) {
  
            $cursos = $response->json();

            foreach($cursos as $dados){
                $curso = Curso::find($dados['course_id']);

                if(!$curso){

                    $curso = new Curso();
                    $curso->id = $dados['course_id'];
                    $curso->codigo_id_hotmart = $dados['course_vendor_id'];
                    $curso->titulo = $dados['course_title'];
                    $curso->headline = $dados['course_headline'];
                    $curso->descricao_curta = $dados['course_desc'];
                    $curso->conteudo_principal = $dados['conteudo'];
                    $curso->conteudo_bonus = $dados['bonus'];
                    $curso->capa_quadrada = $dados['course_cover'];
                    $curso->capa_vertical = $dados['img_capa_vertical'];
                    $curso->capa_horizontal = $dados['img_capa_horizontal'];
                    $curso->nota_avaliacao = $dados['course_reviews'];
                    $curso->numero_alunos = $dados['course_students'];
                    $curso->salario_maximo = $dados['course_salary'];
                    $curso->video_dentro_do_curso = $dados['video_curso_por_dentro'];
                    $curso->video_apresentacao = $dados['video_apresentacao_venda'];
                    $curso->link_checkout_basico = $dados['course_vendor_checkout'];
                    $curso->link_checkout_completo = $dados['course_vendor_checkout2'];
                    $curso->codigo_afiliado_plano_basico = $dados['course_aff_sellid_2'];
                    $curso->codigo_afiliado_plano_completo = $dados['course_aff_sellid_1'];
                    $curso->preco_parcelado_basico = $dados['preco_parcelado_basico'];
                    $curso->preco_parcelado_completo = $dados['preco_parcelado_completo'];
                    $curso->preco_cheio_basico = $dados['preco_cheio_basico'];
                    $curso->preco_cheio_completo = $dados['preco_cheio_completo'];
                    $curso->horas_basico = $dados['horas_basico'];
                    $curso->horas_completo = $dados['horas_completo'];
                    $curso->link_materiais = $dados['course_material'];
                    $curso->link_afiliacao = $dados['course_affiliation_link'];
                    $curso->link_area_membros = $dados['course_classes_link'];
                    $curso->professor_nome = $dados['user_name']." ".$dados['user_lastname'];
                    $curso->professor_biografia = $dados['user_twitter'];
                    $curso->professor_foto = $dados['user_thumb'];
                    $curso->save();
                }
            }
            
            echo "Cursos atualizados";



        } else {
            // Caso a requisição não tenha sido bem-sucedida
           echo "Erro ao buscar dados da API";
        }
    }

    public function abandono_carrinho(){
        // Faz a requisição para a API externa
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        
        $dados = $response->json();

        foreach($dados as $dado){
            $abandonedCart  = new AbandonedCart();
            $abandonedCart->affiliate = null;
            $abandonedCart->product_id = $dado['order_product_id'] ?? null;
            $abandonedCart->product_name = null;
            $abandonedCart->buyer_name = $dado['order_client_name'] ?? null;
            $abandonedCart->buyer_email = $dado['order_email'] ?? null;
            $abandonedCart->buyer_phone = $dado['phone_checkout_local_code'].$dado['phone_checkout_number'] ?? null;
            $abandonedCart->offer_code = null;
            $abandonedCart->checkout_country_name = null;
            $abandonedCart->checkout_country_iso = null;
            $abandonedCart->save();
        }

        echo "Feito, seu bandido!";
        
    }

    public function purchase(){
        // Faz a requisição para a API externa
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        ini_set('max_execution_time', 100000);
        $dados = $response->json();

        foreach($dados as $dado){

            $purchaseEvent = PurchaseEvent::updateOrCreate(
                ['transaction' => $dado['order_transaction']],
                [                   
                    "product_id" => $dado['order_product_id'] ?? null,
                    "created_at" => Carbon::parse($dado['order_purchase_date'])->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
                    "product_name" => $dado['course_title'] ?? null,
                    "buyer_email" => $dado['order_email'] ?? null,
                    "buyer_name" => $dado['order_client_name'] ?? null,
                    "buyer_checkout_phone" => $dado['phone_checkout_local_code'].$dado['phone_checkout_number'] ?? null,
                    "affiliate_name" => $dado['order_aff_name'] ?? null,
                    "affiliate_code" => $dado['order_aff'] ?? null,
                    "transaction" => $dado['order_transaction'] ?? null,
                    "purchase_payment_billet_barcode" => $dado['order_billet_barcode'] ?? null,
                    "purchase_status" => $dado['order_status'] ?? null,
                    "purchase_order_date" => strtotime($dado['order_purchase_date']) * 1000 ?? null,
                    "purchase_payment_type" => $dado['order_payment_type'] ?? null,
                    "buyer_document" => $dado['order_client_doc'] ?? null,
                    'created_at' => $dado['order_purchase_date'] ?? null,
                    
                ]
            );
            
        }

        echo "Feito, seu bandido!"; 
        
    }

    public function codigoref(){
        // Faz a requisição para a API externa
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        ini_set('max_execution_time', 100000);
        $dados = $response->json();

        foreach($dados as $dado){

            $purchaseEvent = Codigo_ref::updateOrCreate(
                ['codigo_ref' => $dado['aff_hotmart_ref']],
                [
                    "user_id" => $dado['user_id'] ?? null,
                    "curso_id" => $dado['course_id'] ?? null,
                    
                ]
            );
            
        }

        echo "Feito, seu bandido!";
        
    }

    public function index(){
        // Retrieving the collection of 'Curso' models where 'publicado' is not null
        $cursos = Curso::whereNotNull('publicado')->get();
    
        // Check if the collection is not empty
        if ($cursos->isNotEmpty()) {
            // Loop through each 'curso' in the collection
            foreach ($cursos as $curso) {
                // Output the 'titulo' property
                echo $curso->titulo . "<br>";
            }
        } else {
            echo "No courses found.";
        }
    }
    

    public function nomear_curso(){
        // Configura o tempo máximo de execução para a execução longa
        ini_set('max_execution_time', 100000);
    
        $dados = true;
    
        while($dados){
            // Busca o primeiro registro com product_name nulo
            $dados = PurchaseEvent::whereNull('product_name')->first();
        
            if ($dados) {
                // Define um valor padrão para product_name
                $dados->product_name = "Auxiliar Administrativo";
        
                // Verifica se há um product_id associado
                if($dados->product_id){
                    // Busca o curso correspondente ao product_id
                    $curso = Curso::where('codigo_id_hotmart', $dados->product_id)->first();
                    
                    // Se o curso foi encontrado, atualiza o product_name
                    if($curso) {
                        $dados->product_name = $curso->titulo;
                    }
                }
                
                // Salva as alterações no registro
                $dados->save();
            } else {
                // Termina o loop se não houver mais registros para processar
                break;
            }
        }
    }  
    
    public function manutencao(){
        // Configura o tempo máximo de execução para a execução longa
        ini_set('max_execution_time', 100000);
    
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        ini_set('max_execution_time', 100000);
        $dados = $response->json();

        foreach($dados as $dado){

            $user = User::find($dado['user_id']);
            if($user){
                $purchaseEvent = ShortenedUrl::updateOrCreate(
                    [
                        'dominio' => 'jemp.me', 
                        'slug' => $dado['short_hash']
                    ],
                    [                   
                        "user_id" => $dado['user_id'],
                        "url_longa" => $dado['short_location'],
                        
                    ]
                );
            }

        }
    } 

    public function user(){
        // Faz a requisição para a API externa
        $response = Http::get('https://afiliados.jovemempreendedor.org/admin/parceiro/teste4.php');
        
        $dados = $response->json();

        ini_set('max_execution_time', 10000);
        $tel=['(', ")", "{", "}", " ", "-"];
        foreach($dados as $dado){

            $purchaseEvent = User::updateOrCreate(
                ['email' => $dado['user_email']],
                [
                    'id' => $dado['user_id'],
                    "name" => $dado['user_name']." ".$dado['user_lastname'] ?? null,
                    "email_verified_at" => date('Y-m-d H:i:s'),
                    "nivel_acesso" => 'user',
                    "cpf" => str_replace(".", "", $dado['user_document']),
                    "telefone_pessoal_1" => str_replace($tel, "", $dado['user_telephone'] ?? null),
                    "telefone_pessoal_2" => str_replace($tel, "", $dado['telefone_pessoal_2'] ?? null),
                    "mentorado" => $dado['user_mentorado'] ?? null,
                    "meta_pixel_id" => $dado['user_aff_pixelfb'] ?? null,
                    "meta_pixel_api" => $dado['pixelFaceAPI'] ?? null,
                    "meta_pixel_eventcode" => $dado['pixeltesteventcode'] ?? null,
                    "whatsapp_atendimento" => $dado['user_aff_whats'] ?? null,
                    "whatsapp_atendimento_tempo" => $dado['user_aff_domain_whatsapp_tempo'] ?? null,
                    "dominio_externo" => $dado['user_aff_domain'] ?? null,
                    "meta_conta_anuncios_id" => $dado['user_ad_account_id'] ?? null,
                    "meta_pagina_id" => $dado['user_page_id'] ?? null,
                    "meta_instagram_id" => $dado['user_instagram_actor_id'] ?? null,
                    "meta_app_id" => $dado['appId'] ?? null,
                    
                ]
            );
            
        }

        echo "Feito, seu bandido!";
        
    }

    
}