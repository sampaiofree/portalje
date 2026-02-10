<?php
namespace App\Http\Controllers;

use App\Models\Codigo_ref;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Meta_apiController;

use App\Models\Curso;
use App\Models\User;
use App\Models\Dados_portal;
use Illuminate\Support\Facades\Schema;

use DOMDocument;
use DOMXPath;
use Throwable;

class Home_e_cursosController extends Controller
{

    public $dados_portal;

    public function __construct()
    {
        $this->dados_portal = $this->carregarDadosPortal();
    }

    private function carregarDadosPortal(): array
    {
        $defaults = [
            'telefone_suporte_alunos' => '5511982671533',
            'whatsapp_atendimento_tempo' => 'Seg a Sex 08:00-18:00',
            'formulario_pre_checkout' => true,
            'formulario_whatsapp' => true,
        ];

        try {
            if (!Schema::hasTable('portal_informacoes')) {
                return $defaults;
            }

            $dados = Dados_portal::first();

            if (!$dados) {
                return $defaults;
            }

            return array_merge($defaults, $dados->toArray());
        } catch (Throwable $e) {
            return $defaults;
        }
    }

    //PÁGINA CURSO INDIVIDUAL
    public function curso_individual(Request $request, $curso = null, $desconto = null) {
            
        if (!$curso) {return redirect()->away('https://portalje.org');}  
        if (!Schema::hasTable('curso')) {return redirect()->route('nova_home');}
            
        /**PEGAR TODOS OS PARAMETROS PARA COLOCAR NO LINK DO CHECKOUT**/
        $queryParams = request()->query();
        $query = "";
        if($queryParams){
             foreach($queryParams as $queryParam => $conteudo){
                 $query .= "&".$queryParam."=".$conteudo;
             }
        }

        if(!$query){
            $src = "&src=pagina_individual";
        }elseif(!request()->get('src') AND $query){
            $src = "&src=";
            foreach($queryParams as $queryParam => $conteudo){
                $src .= $conteudo."|";
            }
        }else{
            $src = null;
        }
        
        //PÁGINAS DO MÉTODO CARVALHO
        if($curso=='w1' OR $curso=='w2' OR $curso=='w3' OR $curso=='w4' OR $curso=='w5' OR $curso=='w6' OR $curso=='w7' OR $curso=='w8' OR $curso=='w' ){return $this->carvalho_whatsapp($request, $desconto, $curso);}
        
        // Consulta o banco de dados com o valor de $curso
        $curso = Curso::where('url', $curso)->first();    
        // Verifica se a consulta encontrou o curso
        if (!$curso) {return redirect()->away('https://portalje.org');}

        //FORMATACAO DOS PRECOS
        $dados = explode("x", $curso->preco_parcelado_completo);
        $curso->parcelamento = $dados[0];
        $curso->preco = isset($dados[1]) ? (float)str_replace('R$', '', str_replace(',', '.', $dados[1])) : null;
        $curso->preco_cheio_completo = (int)str_replace('R$', '', $curso->preco_cheio_completo);
        $curso->preco_cheio = $curso->preco_cheio_completo*3;

        

        //FORMATAÇÃO DA HEADLINE
        $curso->headline = str_replace('"',"", $curso->headline);

        //VERIFICAR DESCONTOS
        $desconto = request()->get('d') ?? $desconto;
        $desconto_banner = null;
        if($desconto AND $desconto !='w'){

            $cupons = [
                'o10',
                'o20',
                'o30',
                'o40',
                'o50',
                'o80'
            ];

            foreach($cupons as $cupom){
                if($desconto == $cupom){
                    $cupom = (int)str_replace('o', '', $cupom);
                    $desconto = "&offDiscount=".$cupom."OFF";

                    $desconto_banner = $cupom;

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
        /*$curso->preco = number_format($curso->preco, 2, ',', '');

        if($curso->preco=='5,76' AND $curso->parcelamento==12){
            $curso->preco = "7,41";
            $curso->parcelamento = "9";
        }*/

        $preco = $this->formatar_preco_parcelado($curso->preco, $curso->parcelamento);

        $curso->preco_cheio_completo = "R$".number_format($curso->preco_cheio_completo, 2, ',', '');
        $curso->preco_parcelado_completo = $preco['parcelamento']."xR$".$preco['preco'];


        $curso->preco_cheio_basico = (int)str_replace('R$', '', $curso->preco_cheio_completo);
        $curso->preco_cheio_basico = (float)$curso->preco_cheio_basico*0.5;
        $curso->preco_cheio_basico = "R$".number_format($curso->preco_cheio_basico, 2, ',', '.');

        $preco_parcelado_basico = explode('R$', $curso->preco_parcelado_completo);
        $preco_parcelado_basico = ((float)$preco_parcelado_basico[1])*0.5;
        $preco = $this->formatar_preco_parcelado($preco_parcelado_basico, $curso->parcelamento);
        $curso->preco_parcelado_basico = $preco['parcelamento']."xR$".$preco['preco'];

        $curso->preco_cheio_certificado = (int)str_replace('R$', '', $curso->preco_cheio_completo);
        $curso->preco_cheio_certificado = (float)$curso->preco_cheio_certificado*0.2;
        $curso->preco_cheio_certificado = "R$".number_format($curso->preco_cheio_certificado, 2, ',', '.');

        $preco_parcelado_certificado = explode('R$', $curso->preco_parcelado_completo);
        $preco_parcelado_certificado = ((float)$preco_parcelado_certificado[1])*0.2;
        $preco = $this->formatar_preco_parcelado($preco_parcelado_certificado, $curso->parcelamento);
        $curso->preco_parcelado_certificado = $preco['parcelamento']."xR$".$preco['preco'];

        $curso->horas_certificado = (int)($curso->horas_completo * 0.5);

        /*
        $curso->preco_cheio_completo = "R$".number_format($curso->preco_cheio_completo, 2, ',', '');
        $curso->preco_parcelado_completo = $curso->parcelamento."xR$".$curso->preco;
         */

        //CONTEUDO
        $curso->conteudo_bonus = $this->lista_conteudo($curso->conteudo_bonus);      
        $curso->conteudo_principal_acordion = $this->lista_conteudo($curso->conteudo_principal);         
        $curso->areas_de_atuacao = explode("/",$curso->areas_de_atuacao);

        //DADOS DO AFILIADO OU PRODUTOR
        $ref = request()->get('ref') ?? false;
        $dados = $this->dadosusuario($request->getHost(), $curso, $ref);
        if (!$dados) {return redirect()->away('https://portalje.org');}   
        $curso->whatsapp_atendimento = $dados['whatsapp_atendimento'];
        $curso->whatsapp_atendimento_id = $dados['whatsapp_atendimento_id'] ?? null;
        if((strlen(request()->get('t')) > 10)){
            $curso->whatsapp_atendimento=request()->get('t');
            $curso->whatsapp_atendimento_id=null;
        }
        //if($curso->whatsapp_atendimento=='5511982671533'){$curso->whatsapp_atendimento='5511962501386';}
        $curso->whatsapp_atendimento_tempo = $dados['whatsapp_atendimento_tempo'];
        $curso->link_checkout_completo = $dados['link_checkout_completo'].$desconto.$query.$src;
        $curso->link_checkout_basico = $dados['link_checkout_completo'].$desconto.$query.$src."&offDiscount=50OFF";
        $curso->link_checkout_certificado = $dados['link_checkout_completo'].$desconto.$query.$src."&offDiscount=80OFF";
        $curso->meta_pixel_id = $dados['meta_pixel_id'];
        $curso->formulario = true; //$dados['formulario_pre_checkout'];
        $curso->user_id = $dados['user_id'];
        $curso->affiliate_code = $dados['affiliate_code'];
        $curso->origem = 'checkout_completo';

        $curso->cidade = request()->get('c') ?? null;
        $curso->v = request()->get('v') ?? "27 Bolsas de Estudo com Desconto!";
        $desconto = request()->get('wd') ? 'w' : $desconto ;
        //PAGINA DIRETO PARA O WHATSAPP
        
        if($desconto AND $desconto =='w'){
            $curso->formulario = true; //$dados['formulario_whatsapp'];
            if($dados['formulario_whatsapp']){$zap_complemento = "meu nome é {nome},";}else{$zap_complemento = '';}
            $curso->link_checkout_completo = "https://wa.me/$curso->whatsapp_atendimento?text=Olá, $zap_complemento quero fazer minha inscrição no curso de $curso->titulo";
            $curso->origem = 'whatsapp';
        }

        if(request()->get('g')=='1'){ 
            //PAGINA DAS AULAS GRATUITAS
            return view('cursos.aulas_gratuitas', compact('curso', 'desconto_banner'));
        }elseif(request()->get('test')=='2'){
            //PAGINA TESTE
            return view('cursos.3ofertas', compact('curso', 'desconto_banner')); 
        }elseif(request()->getHost() === 'jovemempreendedor.org'){
            //PÁGINA DO DOMINIO JOVEMEMPREENDEDOR.ORG
            return view('novapagina', compact('curso', 'desconto_banner'));
        }elseif(request()->get('ga')=='1'){ 
            //PAGINA DAS AULAS GRATUITAS PAGINA TESTE
            return view('cursos.curso_gratuito_a', compact('curso', 'desconto_banner'));
        }elseif(request()->get('test')=='1'){
            //PAGINA TESTE
            return view('novapagina2', compact('curso', 'desconto_banner')); 
        }else{
            //PAGINA OFICIAL DOS AFILIADOS
            return view('novapagina', compact('curso', 'desconto_banner')); 
        }   
        
        
    }

    //DADOS DO USUARIO - AFILIADO OU PRODUTOR
    private function dadosusuario($dominio, $curso = null, $ref = null){
        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if(
            Schema::hasTable('users') AND
            $dominio!='portalje.org' AND
            $dominio!='dns.portalje.org' AND
            $dominio!='jemp.me' AND
            $dominio!='jovemempreendedor.org' AND
            $dominio!='dns.jovemempreendedor.org'
        ){

            $verificar = User::where('dominio', $dominio)->orWhere('dominio_externo', $dominio)->first();
            if($verificar){
                $whatsappSelecionado = $this->selecionarWhatsappAtendimento($verificar);
                $whatsappAtendimento = $whatsappSelecionado['whatsapp'] ?? $verificar->whatsapp_atendimento;

                //DADOS DO AFILIADO
                $dados = [
                    'whatsapp_atendimento' => $whatsappAtendimento,
                    'whatsapp_atendimento_id' => $whatsappSelecionado['id'] ?? null,
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

        if($ref && Schema::hasTable('codigo_ref') && Schema::hasTable('users')){
          $ref = Codigo_ref::where('codigo_ref', $ref)->join('users', 'users.id', '=', 'codigo_ref.user_id')->select('codigo_ref.*','users.*', )->first();
        }

        $refUserId = is_object($ref) ? ($ref->id ?? null) : (is_array($ref) ? ($ref['id'] ?? null) : null);
        $refWhatsapp = is_object($ref) ? ($ref->whatsapp_atendimento ?? null) : (is_array($ref) ? ($ref['whatsapp_atendimento'] ?? null) : null);
        $refWhatsappTempo = is_object($ref) ? ($ref->whatsapp_atendimento_tempo ?? null) : (is_array($ref) ? ($ref['whatsapp_atendimento_tempo'] ?? null) : null);
        $refMetaPixel = is_object($ref) ? ($ref->meta_pixel_id ?? null) : (is_array($ref) ? ($ref['meta_pixel_id'] ?? null) : null);
        $refFormularioPreCheckout = is_object($ref) ? ($ref->formulario_pre_checkout ?? null) : (is_array($ref) ? ($ref['formulario_pre_checkout'] ?? null) : null);
        $refFormularioWhatsapp = is_object($ref) ? ($ref->formulario_whatsapp ?? null) : (is_array($ref) ? ($ref['formulario_whatsapp'] ?? null) : null);
        $refCodigo = is_object($ref) ? ($ref->codigo_ref ?? null) : (is_array($ref) ? ($ref['codigo_ref'] ?? null) : null);

        $whatsappSelecionadoRef = ['id' => null, 'whatsapp' => null];
        if ($refUserId && Schema::hasTable('users')) {
            $refUser = User::find($refUserId);
            if ($refUser) {
                $whatsappSelecionadoRef = $this->selecionarWhatsappAtendimento($refUser);
            }
        }

        $dados_portal = $this->dados_portal;
        $dados = [
            'whatsapp_atendimento' => $whatsappSelecionadoRef['whatsapp'] ?? ($refWhatsapp ?? $dados_portal['telefone_suporte_alunos']),
            'whatsapp_atendimento_id' => $whatsappSelecionadoRef['id'] ?? null,
            'whatsapp_atendimento_tempo' => $refWhatsappTempo ?? $dados_portal['whatsapp_atendimento_tempo'],
            'meta_pixel_id' => $refMetaPixel,
            'formulario_pre_checkout' => $refFormularioPreCheckout ?? $dados_portal['formulario_pre_checkout'],
            'formulario_whatsapp' => $refFormularioWhatsapp ?? $dados_portal['formulario_whatsapp'],
            'user_id' => $refUserId,
            'affiliate_code' => $refCodigo
        ];

        if($curso){
            $dados['link_checkout_completo'] = "$curso->link_checkout_completo&hideBillet=1";
        }

        return $dados;

    }

    //HOME PAGE
    public function home(Request $request, $watsapp_curso = null, $cidade_desconto = null){
        
        /**SE FOR CURSO INDIVIDUAL**/
        if($watsapp_curso AND $watsapp_curso!="w" AND $watsapp_curso!="w1" AND $watsapp_curso!="w2"){
            return $this->curso_individual($request, $watsapp_curso, $cidade_desconto);
        }
        
        /**PEGAR TODOS OS PARAMETROS PARA COLOCAR NO LINK DO CHECKOUT**/
        $queryParams = request()->query();
        /*if($queryParams){
            $query = "?";
            foreach($queryParams as $queryParam => $conteudo){
                $query .= $queryParam."=".$conteudo."&";
            }
        } 

        if(!isset($query)){
            $src = "?src=home_page";
        }elseif(!request()->has('src') AND isset($query)){
            $src = "src=";
            foreach($queryParams as $queryParam => $conteudo){
                $src .= $conteudo."|";
            }
        }else{
            $src = null;
        }*/

        $queryParams = request()->query();

        if (empty($queryParams)) {
            // Se não há parâmetros, define src como padrão
            $src = "?src=home_page";
        } else {
            $src = false;
            $src_conteudo = "";
            $query = "?";
        
            foreach ($queryParams as $queryParam => $conteudo) {
                // Monta a query string no formato key=value&
                $query .= "$queryParam=$conteudo&";
        
                // Monta o conteúdo de src concatenando valores separados por "|"
                $src_conteudo .= $queryParam."_".$conteudo."|";
        
                // Verifica se o parâmetro src existe
                if ($queryParam === 'src') {
                    $src = true;
                }
            }
        
            // Se src não foi encontrado, adiciona src com os valores concatenados
            if (!$src) {
                $src = "src=$src_conteudo&sck=$src_conteudo";
            }
        }
        

        

        /**PEGAR DADOS DO USUÁRIO**/
        $afiliadoID = request()->get('af') ?? null;
        $dados = $this->dadosusuario_home($request->getHost(), $afiliadoID);
        if (!$dados) {return redirect()->away('https://portalje.org');}

        //PEGAR TODOS OS PARAMETROS PARA COLOCAR NA URL    
        $info = $dados['dados'];
        $info['parametros'] = isset($query) ? $query.$src : $src;

        //CARVALHO WHATSAPP
        $info['whatsapp'] = request()->get('w') ?? null;
        if((strlen(request()->get('t')) > 10)){$info['whatsapp_atendimento']=request()->get('t');}
        $info['whatsapp'] = $watsapp_curso ?? $info['whatsapp'];
        
        
        //DESCONTO
        $d = request()->get('d') ?? null;
        if(request()->getHost() === 'jovemempreendedor.org'){
            $d = "o80";
            //$info['parametros'] =$info['parametros']."&d=o80";
        } //Desconto padrão no Portal JE Produtor        
        $desconto_banner = str_replace('o', '', $d);
       
        
        //NOME DA CIDADE
        $info['cidade'] = request()->get('c') ?? null;
        $info['cidade'] = $cidade_desconto ?? $info['cidade'];
        $info['v'] = request()->get('v') ?? "27 Bolsas de Estudo com Desconto!";
        

        /**DEFINIR SE TERÁ OU NÃO FORMULÁRIO WHATSAPP**/
        if($info['whatsapp']){
            $info['formulario'] = true; 
        }else{
            $info['formulario'] = false;
        }
        $cursos = $dados['cursos']; 
        
        if(request()->get('edital')=='1'){
            return view('landign_pages.edital', compact('cursos', 'info', 'desconto_banner'));
        }elseif(request()->get('test')=='1'){
            return view('home', compact('cursos', 'info', 'desconto_banner'));
        }elseif(request()->getHost() === 'jovemempreendedor.org'){ 
            return view('home', compact('cursos', 'info', 'desconto_banner'));
        }elseif(request()->get('lista')=='1'){
            return view('cursos.lista_publica', compact('cursos', 'info', 'desconto_banner'));
        }elseif(request()->get('gratuito')=='1'){
            return view('home_gratuito', compact('cursos', 'info', 'desconto_banner'));
        }else{
            return view('home1', compact('cursos', 'info', 'desconto_banner'));  
        }
        
    }

    //DADOS DO USUARIO - AFILIADO OU PRODUTOR - PARA HOME PAGE
    private function dadosusuario_home($dominio, $afiliadoID = null){
        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if(
            Schema::hasTable('users') &&
            (
                ($dominio!='portalje.org' AND $dominio!='dns.portalje.org' AND $dominio!='jemp.me' AND $dominio!='jovemempreendedor.org' AND $dominio!='dns.jovemempreendedor.org')
                OR $afiliadoID
            )
        ){            

            if($afiliadoID){
                $verificar = User::find($afiliadoID);
            }else{
                $verificar = User::where('dominio', $dominio)->orWhere('dominio_externo', $dominio)->first();
            }
            
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

                if (Schema::hasTable('codigo_ref') && Schema::hasTable('curso')) {
                    $dados['cursos'] = Codigo_ref::where('codigo_ref.user_id', $verificar->id)
                    ->join('curso', 'curso.id', '=', 'codigo_ref.curso_id')
                    ->select(
                        'codigo_ref.*', 
                        'curso.*',
                    )
                    ->orderBy('ordem')
                    ->get();
                } else {
                    $dados['cursos'] = collect();
                }
            

                return $dados;
            }

        }

        $dados_portal = $this->dados_portal;
        $dados['dados'] = [
            'afiliado' => false,
            'whatsapp_atendimento' => $dados_portal['telefone_suporte_alunos'],
            'whatsapp_atendimento_tempo' => $dados_portal['whatsapp_atendimento_tempo'],
            'meta_pixel_id' => null,
            'formulario_pre_checkout' => $dados_portal['formulario_pre_checkout'],
            'formulario_whatsapp' => $dados_portal['formulario_whatsapp'],
            'user_id' => null,
            'affiliate_code' => null
        ];
        if (Schema::hasTable('curso')) {
            $dados['cursos'] = Curso::orderBy('gratuito', 'desc')
                                ->orderBy('ordem')
                                ->get();
        } else {
            $dados['cursos'] = collect();
        }

        return $dados;

    }

    public function index(Request $request, $curso = null, $cidade = null){
        
        //PEGAR DADOS DO AFILIADO PELO DOMINIO
        $dados_afiliado = $this->listar_user_pelo_dominio($request->getHost());
        if($dados_afiliado=='redirect')
        { //SE DADOS DO AFILIADO VIER VAZIO
            return redirect()->away(route('home_curso'));
        }elseif($dados_afiliado)
        {
            if($dados_afiliado->meta_pixel_id AND $dados_afiliado->meta_pixel_api AND $dados_afiliado->meta_pixel_eventcode){ //CASO TENHA OS DADOS DA API, ENVIAR EVENTO DE VISUALIZAÇÃO DE PÁGINA
                $evendo  = new Meta_apiController();
                $evendo->evento($request, null, 'PageView');
            }
        }

        //PÁGINAS W e D
        if($curso=='w1' OR $curso=='w2' OR $curso=='w3' OR $curso=='w4' OR $curso=='w5' OR $curso=='w6' OR $curso=='w7' OR $curso=='w8' OR $curso=='w' ){return $this->carvalho_whatsapp($request, $cidade, $curso);}
        if($curso=='d1' OR $curso=='d2' OR $curso=='d3' OR $curso=='d4' OR $curso=='d5' OR $curso=='d6' OR $curso=='d7' OR $curso=='d8' OR $curso=='d' ){$curso=false;}        
        

        //HOME PAGE
        if(!$curso){ 
            //DADOS DA PÁGINA
            $pagina = $this->dados_da_pagina($dados_afiliado, $cidade);

            //LISTAR OS CURSOS 
            $cursos = $this->listar_cursos($request, $dados_afiliado, $cidade);
    
            return view('home_e_cursos.home', compact('cursos', 'pagina'));
            
        }else{//CURSO INDIVIDUAL
            return $this->listar_curso_individual($request, $curso, $dados_afiliado, $cidade);
        }
        
    }

    public function dados_curso($curso_id)
    {
        $curso = Curso::where('url', $curso_id)->first(); // Use first() para obter o primeiro resultado
        return $curso;
    }   

    public function carvalho_whatsapp(Request $request, $cidade = null, $w = 'w'){

        //Pegar dados do usuario pelo dominio
        $dados_afiliado = $this->listar_user_pelo_dominio($request->getHost());

        if($dados_afiliado=='redirect'){return redirect()->away('https://portalje.org');} 

        //DADOS DA PÁGINA
        $pagina = $this->dados_da_pagina($dados_afiliado, $cidade);

        //LISTAR OS CURSOS 
        
        $cursos = $this->listar_cursos($request, $dados_afiliado, 'w3');
            return view('home_e_cursos.w3', compact('cursos', 'pagina')); 
        
        /*if($w=='w3'){
            $cursos = $this->listar_cursos($request, $dados_afiliado, 'w3');
            return view('home_e_cursos.w3', compact('cursos', 'pagina'));
        }elseif($w=='w4'){
            $cursos = $this->listar_cursos($request, $dados_afiliado, 'w4');
            return view('home_e_cursos.w4', compact('cursos', 'pagina'));
        }else{
            $cursos = $this->listar_cursos($request, $dados_afiliado, 'w');
            return view('home_e_cursos.home', compact('cursos', 'pagina'));
        }*/
        


    }

    public function vagas()
    {
        // Verifica se a sessão 'vagas' não está definida
        if (!session()->has('vagas')) {
            // Cria o array de 'vagas' na sessão
            $vagas = [];

            $contador = 0;

            // Popula o array 'vagas' com números aleatórios entre 2 e 12
            while ($contador <= 100) {
                $vagas[] = rand(2, 12);
                $contador++;
            }

            // Armazena o array 'vagas' na sessão
            session(['vagas' => $vagas]);
        }

        // Acessando o array 'vagas' da sessão
        $vagas = session('vagas');

        // Retornar algo, por exemplo, uma view com os dados de vagas
        return $vagas;
    }


    public function dados_da_pagina($user = null, $cidade = null, $curso = null){
        
        $nome_cidade = null;
        $curso_id = null;
        $user_id = null;
        $botao_whatsapp_flutuante = null;
        $botao_whatsapp_flutuante_nome_curso = "os cursos do Programa Jovem Empreendedor";
        $form_lead_titulo = "Para receber mais informações, preencha o formulário abaixo:";
        $form_lead_botao = "Saiba mais do WhatsApp";
        

        

        if(
            $cidade!='w' AND
            $cidade!='o10' AND
            $cidade!='o20' AND
            $cidade!='o30' AND
            $cidade!='o40' AND
            $cidade!='o50' AND
            $cidade!='o60' AND
            $cidade!='o70' AND
            $cidade!='o80'
            
            ){
            if($cidade){$nome_cidade = $cidade;}
        }

        if($user){ //DADOS DO AFILIADO
            $user_id = $user->id;
            $whatsappSelecionado = $this->selecionarWhatsappAtendimento($user);
            $whatsapp_atendimento = $whatsappSelecionado['whatsapp'] ?? $user->whatsapp_atendimento ?? $this->dados_portal['telefone_suporte_alunos'];
            $whatsapp_atendimento_id = $whatsappSelecionado['id'] ?? null;
            $whatsapp_atendimento_tempo = $user->whatsapp_atendimento_tempo;
            $formulario_whatsapp = $user->formulario_whatsapp;
            $formulario_pre_checkout = $user->formulario_pre_checkout;
            $pidel_id = $user->meta_pixel_id;
        }else{ //DADOS DO PRODUTOR
            $dados =  $this->dados_portal;
            $whatsapp_atendimento =  $dados['telefone_suporte_alunos'];
            $whatsapp_atendimento_id = null;
            $whatsapp_atendimento_tempo = $dados['whatsapp_atendimento_tempo'];
            $formulario_whatsapp = $dados['formulario_whatsapp'];
            $formulario_pre_checkout = $dados['formulario_pre_checkout'];
            $pidel_id = null;
        }

        $whatsapp_mostrar  = true; //MOSTRAR BOTÃO FLUTUANTE DO WHATSAPP

        if($nome_cidade){
            $headline = "27 Bolsas de Estudo liberadas para <span style='color: rgb(13, 110, 253) !important;'>$nome_cidade</span>";
            $headline_sub = "Escolha seu curso para falar com o nosso consultor pelo WhatsApp";
            $whatsapp_mostrar = false; //MOSTRAR BOTÃO FLUTUANTE DO WHATSAPP
            $headline_botao = 'Escolher o meu curso agora!';
        }elseif($curso){
            $headline = $curso->titulo;
            $headline_sub = $curso->headline;
            $headline_botao = 'Quero saber mais';
            $botao_whatsapp_flutuante_nome_curso = "sobre o curso de $curso->titulo";
            $curso_id = $curso->id;
            $form_lead_titulo = "Antes de prosseguir, preecha os dados abaixo!";
            $form_lead_botao = "Continuar";
        }else{
            $headline = "Bolsas de Estudo de até 85%";
            $headline_sub = "Clique no botão abaixo para escolher o seu curso";
            $headline_botao = 'Escolher o meu curso agora!';
        }

        $img_botao_whatsapp = asset('img/home_page/whatsapp.gif');
        
        if($cidade!='w'){
            $botao_whatsapp_flutuante = "<a id=\"whatsapp_botao\"  href=\"https://api.whatsapp.com/send/?phone=$whatsapp_atendimento&text=Olá quero saber sobre $botao_whatsapp_flutuante_nome_curso\" target=\"_blank\" class=\"jump bg-success rounded-circle d-flex justify-content-center align-items-center position-fixed bottom-0 end-0 m-3\" style=\"width: 70px; height: 70px; z-index: 9; visibility:hidden;\"><img alt='Portal Jovem Empreendedor' loading='lazy' src=\"$img_botao_whatsapp\" width='70' height='70'></a>";
        } 
       
        

        return $pagina = [
            "headline"=>$headline,
            "headline_sub"=>$headline_sub,
            "headline_botao"=> $headline_botao,
            "whatsapp_mostrar"=> true,
            "whatsapp_atendimento_tempo"=> $whatsapp_atendimento_tempo,
            "whatsapp"=>$whatsapp_atendimento,
            "whatsapp_atendimento_id" => $whatsapp_atendimento_id,
            "whatsapp_mostrar"=> $whatsapp_mostrar,
            "formulario_whatsapp"=> $formulario_whatsapp,
            "formulario_pre_checkout"=> $formulario_pre_checkout,
            "botao_whatsapp_flutuante"=> $botao_whatsapp_flutuante,
            "form_lead_titulo"=> $form_lead_titulo,
            "form_lead_botao"=> $form_lead_botao,
            "pidel_id"=> $pidel_id,
        ];
    }

    public function listar_cursos($request, $user = null, $pagina = 'home'){
        
        $src = "";
        $sck = "";
        if($request->query('src')!== null){$src = "&src=".$request->query('src');}
        if($request->query('sck')!== null){$src = "&sck=".$request->query('sck');}
        $parametros = $src.$sck;

        if (!Schema::hasTable('curso')) {
            return [];
        }
        

        
        
        if($user){
            $codigos_ref = Schema::hasTable('codigo_ref') ? $user->codigo_ref : collect();
            $formulario_whatsapp = $user->formulario_whatsapp;
            $formulario_pre_checkout = $user->formulario_pre_checkout;
            $whatsAppAtendimentoId = null;
            if (in_array($pagina, ['w', 'w3', 'w4'], true)) {
                $whatsappSelecionado = $this->selecionarWhatsappAtendimento($user);
                $whatsApp = $whatsappSelecionado['whatsapp'] ?? $user->whatsapp_atendimento ?? $this->dados_portal['telefone_suporte_alunos'];
                $whatsAppAtendimentoId = $whatsappSelecionado['id'] ?? null;
            } else {
                $whatsApp =  $user->whatsapp_atendimento;
            }
            $data_user = $user->id;
        }else{
            $dados_portal = $this->dados_portal;
            $codigos_ref = collect();
            $formulario_whatsapp = $dados_portal['formulario_whatsapp'];
            $formulario_pre_checkout = $dados_portal['formulario_pre_checkout'];
            $whatsApp =  $dados_portal['telefone_suporte_alunos'];
            $whatsAppAtendimentoId = null;
            $data_user = null;
        }
        
        $datacursos = Curso::orderBy('ordem')->get();

        $cursos = [];
        $vagas = $this->vagas();
        $n=0;

        foreach ($datacursos as $curso) {
            $cursoEncontrado = false;
            $vaga =  $vagas[$n]; $n++;
            //ADICIONAR CÓDIGO REF DO USER EM CADA CURSO
            if ($codigos_ref !== null && $codigos_ref->isNotEmpty()) {
                if($codigos_ref){
                    foreach ($codigos_ref as $codigo_ref) {
                        if (is_object($codigo_ref) && $codigo_ref->curso_id == $curso->id) {
                            $curso->codigo_ref = $codigo_ref->codigo_ref;
                            $curso->codigo_ref_id = $codigo_ref->id;
                            $curso->mostrar_curso = $codigo_ref->mostrar_curso;
                            $cursoEncontrado = true;
                            break;
                        }
                    }
                }
                
            }

            if (!$cursoEncontrado) {
                $curso->codigo_ref = false;
                $curso->codigo_ref_id = false;
                $curso->mostrar_curso = false;
            }
            
            //DETERMINAR SE CADA CURSO IRÁ APARECER NA PÁGINA OU NÃO
            if($user){
                if($curso->codigo_ref_id AND $curso->publicado AND $curso->permitir_afiliacao){
                    $curso->mostrar_na_pagina = $curso->mostrar_curso;
                }else{
                    $curso->mostrar_na_pagina = false; 
                }
            }
            

            if($user AND $curso->codigo_ref){ //LINK CHECKOUT DO AFILIADO
                if($curso->link_checkout_basico){$curso->link_checkout_basico = "https://go.hotmart.com/$curso->codigo_ref?ap=$curso->codigo_afiliado_plano_basico$parametros";}
                if($curso->link_checkout_completo){$curso->link_checkout_completo = "https://go.hotmart.com/$curso->codigo_ref?ap=$curso->codigo_afiliado_plano_completo$parametros";}
            }else{
                $curso->link_checkout_basico = $curso->link_checkout_basico.$parametros;
                $curso->link_checkout_completo = $curso->link_checkout_completo.$parametros;
            }

            $src = asset('storage/'.$curso->capa_quadrada);
           
            
            if($pagina == 'w'){ //PÁGINAS QUE VÃO PARA O WHATSAPP

                $curso->tag_a = "
                    <a class='lead_navegador' data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" role=\"button\"
                    data-link=\"https://wa.me/$whatsApp?text=Olá, meu nome é {nome} quero saber mais sobre o curso de $curso->titulo\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$data_user\"
                    data-origem=\"whatsapp\"
                    data-whatsapp-atendimento-id=\"$whatsAppAtendimentoId\"
                    >
                        <img src=\"$src\" alt=\"$curso->titulo\" class=\"img-fluid rounded-4 border border-1\">
                    </a>";
                
                /*if($formulario_whatsapp){
                    
                    $curso->tag_a = "
                    <a class='lead_navegador' data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" role=\"button\"
                    data-link=\"https://wa.me/$whatsApp?text=Olá, meu nome é {nome} quero saber mais sobre o curso de $curso->titulo\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$data_user\"
                    data-origem=\"whatsapp\"
                    >
                        <img src=\"$src\" alt=\"$curso->titulo\" class=\"img-fluid rounded-4 border border-1\">
                    </a>";
                }else{
                    $curso->tag_a = "
                    <a class='lead'  target=\"_blanck\" data-href=\"https://wa.me/$whatsApp?text=Olá, quero saber mais sobre o curso de $curso->titulo\">
                        <img src=\"$src\" alt=\"$curso->titulo\" class=\"img-fluid rounded-4 border border-1\">
                    </a>";
                }*/
            }elseif($pagina == 'w3' OR $pagina == 'w4'){ //PÁGINAS QUE VÃO PARA O WHATSAPP
                $src = asset('storage/'.$curso->capa_vertical);
                $curso->headline = str_replace('"','', $curso->headline);
                $curso->headline = $this->limitar_string($curso->headline);
                
                $imagem_curso = "
                <div class=\" my-2 pb-3 border-bottom\">
                    <div class=\"row\">
                        <div class=\"col-4\">
                            <img class=\"align-self-top w-100 rounded-start\" src=\"$src\" alt=\"$curso->titulo\">
                        </div>

                        <div class=\"col-8 align-self-top mb-0 pb-0\">
                            <h6 class=\"tituloCurso mb-0 pb-0 text-dark\" style=\"line-height: 1.1;\">$curso->titulo</h6>
                            <p class=\"my-0 text-dark\" style=\"font-size: 0.7rem;;line-height: 1.1;\">$curso->headline</p>
                            <div class=\"my-0\">
                                <p class=\"text-info my-0 d-flex align-items-center\" style=\"font-size: 0.7rem;;\"><span class=\"ri-time-fill mr-1\" style=\"font-size: 0.7rem;;\"></span>Até $curso->horas_completo horas</p>
                                <p class=\"text-info my-0 d-flex align-items-center\" style=\"font-size: 0.7rem;;\"><span class=\"ri-team-fill mr-1\" style=\"font-size: 0.7rem;;\"></span>$curso->numero_alunos Alunos</p>
                            </div>
                            <div class=\"my-0\">
                                <div class=\"rating\" style=\"font-size: x0.7rem;;\">
                                    <span class=\"ri-star-s-fill text-warning\" style=\"font-size: x0.7rem;;\"></span>
                                    <span class=\"ri-star-s-fill text-warning\" style=\"font-size: x0.7rem;;\"></span>
                                    <span class=\"ri-star-s-fill text-warning\" style=\"font-size: x0.7rem;;\"></span>
                                    <span class=\"ri-star-s-fill text-warning\" style=\"font-size: x0.7rem;;\"></span>
                                    <span class=\"ri-star-s-fill text-warning\" style=\"font-size: x0.7rem;;\"></span>
                                    <span class=\"rating-count text-warning\" style=\"font-size: 0.7rem;;\"><strong>$curso->nota_avaliacao/5</strong></span>
                                </div>
                            </div>
                            <div class=\"my-0\">
                                <span class=\"badge bg-danger rounded-pill px-2 py-1 text-white vaga\" style=\"font-size: xx-small;\">$vaga vagas restantes</span>
                            </div>
                            <p class=\"btn pt-0 mb-0 pb-0 text-white rounded rounded-3\" style=\"font-size:0.8rem; background-color:#0d6efd;\"><strong>Saiba mais</strong></p>
                        </div>
                    </div>
                </div>
                    
                ";

                $curso->tag_a = "
                    <a class='lead_navegador' data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" role=\"button\"
                    data-link=\"https://wa.me/$whatsApp?text=Olá, meu nome é {nome} e quero saber mais sobre o curso de $curso->titulo\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$data_user\"
                    data-origem=\"whatsapp\"
                    data-whatsapp-atendimento-id=\"$whatsAppAtendimentoId\"
                    style='text-decoration: none;'>
                        $imagem_curso                    
                    </a>";

                /*if($formulario_whatsapp){                    
                    $curso->tag_a = "
                    <a class='lead_navegador' data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" role=\"button\"
                    data-link=\"https://wa.me/$whatsApp?text=Olá, meu nome é {nome} e quero saber mais sobre o curso de $curso->titulo\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$data_user\"
                    data-origem=\"whatsapp\"
                    style='text-decoration: none;'>
                        $imagem_curso                    
                    </a>";
                }else{
                    $curso->tag_a = "
                    <a class='lead'  target=\"_blanck\" data-href=\"https://wa.me/$whatsApp?text=Olá, quero saber mais sobre o curso de $curso->titulo\" style='text-decoration: none;'>
                        $imagem_curso    
                    </a>";
                }*/
            }else{ //PÁGINAS QUE VÃO PARA A PÁGINA DO CURSO
                $curso->tag_a = "
                    <a class='view_content' href=\"$curso->url$parametros\">
                        <img src=\"$src\" alt=\"$curso->titulo\" class=\"img-fluid rounded-4 border border-1\">
                    </a>";
            }

            $cursos[] = $curso;
            
        }
        return $cursos;
    }

    public function listar_todos_cursos(){

        $cursos = Curso::orderBy('gratuito','ordem')->get(); //Curso::all();
        return $cursos;
    }

    public function listar_curso_individual($request, $curso, $dados_afiliado = null, $cidade = null) {

        $curso = Curso::where('url', $curso)->first();
        

        $pagina = $this->dados_da_pagina($dados_afiliado, $cidade, $curso);

        if($curso){

            $src = "";
            $sck = "";
            if($request->query('src')!== null){$src = "&src=".$request->query('src');}
            if($request->query('sck')!== null){$src = "&sck=".$request->query('sck');}
            $parametros = $src.$sck;

            $curso->codigo_ref = null;
            $user_id = null;
            if($dados_afiliado){
                $model_ref = Codigo_ref::where('curso_id', $curso->id)->where('user_id', $dados_afiliado->id)->first();
                $user_id = $dados_afiliado->id;
                
            }
            if(isset($model_ref) AND !empty($model_ref)){$curso->codigo_ref=$model_ref['codigo_ref'];}

            $curso->salario_maximo = 'R$ ' . number_format($curso->salario_maximo, 2, ',', '.');
            $curso->areas_de_atuacao = explode("/",$curso->areas_de_atuacao);
            
            $curso->preco_cheio_sem_desconto_basico = $this->preco_cheio_sem_desconto($curso->preco_cheio_basico) ?? "";
            $curso->preco_cheio_sem_desconto_completo = $this->preco_cheio_sem_desconto($curso->preco_cheio_completo) ?? "";

            //DECONTOS
            if(
                $cidade=='o10' OR
                $cidade=='o20' OR
                $cidade=='o30' OR
                $cidade=='o40' OR
                $cidade=='o50' OR
                $cidade=='o60' OR
                $cidade=='o70' OR
                $cidade=='o80'
                ){

                $indice = (int)str_replace("o", "", $cidade);    
                $desconto = "&offDiscount=".$indice."OFF";

                $porcetangem = (100-$indice)*0.01;

                //print_r($porcetangem);
                //exit;

                if($curso->preco_cheio_basico){
                    $preco = explode("R$", $curso->preco_cheio_basico);
                    $curso->preco_parcelado_basico = "R$".number_format((float)$preco[1]*$porcetangem, 2, ',', '.');
                    $curso->preco_cheio_basico = null;
                }
                

                if( $curso->preco_cheio_completo){
                    $preco = explode("R$", $curso->preco_cheio_completo);
                    $curso->preco_parcelado_completo = "R$".number_format((float)$preco[1]*$porcetangem, 2, ',', '.');
                    $curso->preco_cheio_completo = null;
                }
                
            }else{
                $desconto = null;

            }

            //LINKS DOS CHECKOUTS
            if($curso->codigo_ref){
                if($curso->link_checkout_basico){$curso->link_checkout_basico = "https://go.hotmart.com/$curso->codigo_ref?ap=$curso->codigo_afiliado_plano_basico$desconto$parametros";}
                if($curso->link_checkout_completo){$curso->link_checkout_completo = "https://go.hotmart.com/$curso->codigo_ref?ap=$curso->codigo_afiliado_plano_completo$desconto$parametros";}
            }else{
                if($curso->link_checkout_basico){$curso->link_checkout_basico = "$curso->link_checkout_basico$desconto$parametros";}
                if($curso->link_checkout_completo){$curso->link_checkout_completo = "$curso->link_checkout_completo$desconto$parametros";}
                
            }

            if($curso->conteudo_bonus){ 
                $curso->bonus_lista = $this->titulo_li($curso->conteudo_bonus);
            }else{
                $curso->bonus_lista = false;
            }            
            $curso->conteudo_bonus = $this->lista_conteudo($curso->conteudo_bonus);
            $curso->conteudo_principal = $this->lista_conteudo($curso->conteudo_principal);
    
            if($pagina['formulario_pre_checkout']){
                $curso->botao_checkout_basico = "<a class=\"btn btn-info d-block w-100\" role=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" 
                    data-link=\"$curso->link_checkout_basico\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$user_id\"
                    data-origem=\"checkout_basico\" 
                    >Garantir Minha Vaga</a>";
                $curso->botao_checkout_completo = "<a class=\"btn btn-success d-block w-100\" role=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" 
                    data-link=\"$curso->link_checkout_completo\" 
                    data-curso=\"$curso->id\" 
                    data-user=\"$user_id\"
                    data-origem=\"checkout_completo\"     
                    >Garantir Minha Vaga</a>";
            }else{
                $curso->botao_checkout_basico = "<a class=\"btn view_content btn-info d-block w-100\" role=\"button\" href=\"$curso->link_checkout_basico\" >Garantir Minha Vaga</a>";
                $curso->botao_checkout_completo = "<a class=\"btn view_content btn-success d-block w-100\" role=\"button\" href=\"$curso->link_checkout_completo\" >Garantir Minha Vaga</a>";
            }          
            

           //PÁGINA INDIVIDUAL W QUE LEVA PARA O WHATSAPP
            if($cidade=='w' OR $cidade=='0'){$curso->botao_flutuante_whatsapp = $this->curso_individual_w($pagina, $curso, $dados_afiliado);}else{$curso->botao_flutuante_whatsapp = false;}

            if($cidade=='gratuito'){
                 return redirect()->away(route('redirectWithUrl',['url' => $curso['link_area_membros'], 'iframe' => $curso['link_checkout_completo']]));
            }else{
                return view('home_e_cursos.curso_individual', compact('curso', 'pagina'));
            }
        }else{
            return redirect()->away(route('home_curso'));
        }

        
    }

    public function listar_user_pelo_dominio($dominio){

        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if($dominio!='portalje.org' AND $dominio!='dns.portalje.org' AND $dominio!='jemp.me' AND $dominio!='jovemempreendedor.org' AND $dominio!='dns.jovemempreendedor.org'){            

            $verificar = User::where('dominio', $dominio)
                                ->orWhere('dominio_externo', $dominio)
                                ->first();
            

            if($verificar){
                return $verificar;
            }else{
                return 'redirect';
            }

        }else{
            return false;
        }
        
    }

    private function selecionarWhatsappAtendimento(?User $user): array
    {
        if (!$user) {
            return ['id' => null, 'whatsapp' => null];
        }

        $fallbackWhatsapp = preg_replace('/\D/', '', (string) $user->whatsapp_atendimento);

        if (!Schema::hasTable('whatsapp_atendimento')) {
            return ['id' => null, 'whatsapp' => $fallbackWhatsapp ?: null];
        }

        $registro = $user->whatsappAtendimentos()
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN last_lead_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('last_lead_at')
            ->orderBy('id')
            ->first();

        if ($registro) {
            return [
                'id' => $registro->id,
                'whatsapp' => (string) $registro->whatsapp,
            ];
        }

        return ['id' => null, 'whatsapp' => $fallbackWhatsapp ?: null];
    }

    public function preco_cheio_sem_desconto($preco=null){

        if($preco){
            $preco = str_replace("R$", "", $preco);
            return number_format((float)$preco*4, 2, ',', '.');
        }
        
    }

    public function titulo_li($string){
        
        $html = $string;
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('//li[ul]');

        $conteudo = array();
        foreach($elements as $element){
            $conteudo[] = trim($element->childNodes->item(0)->nodeValue) . PHP_EOL;
        }

        return $conteudo;
        
    }


    public function curso_individual_w($pagina, $curso, $user){
        $whatsApp =  $pagina['whatsapp'];
        $user_id = null;
        if($user){$user_id = $user->id;}
        
        if($pagina['formulario_whatsapp']){
            $botao = 
            "
            <div class=\"fixed-bottom d-flex justify-content-center mb-3\">
            <a data-bs-toggle=\"modal\" data-bs-target=\"#modal_lead\" 
            class=\"jump mx-auto btn btn-lg border border-dark border-3 d-flex align-items-center text-white\"
            style='font-weight: bolder;background-color: #009d4e;z-index: 1000;max-width: 90%;font-weight: bolder;'
            data-link=\"https://wa.me/$whatsApp?text=Olá, quero saber mais sobre o curso de $curso->titulo\" 
            data-curso=\"$curso->id\" 
            data-user=\"$user_id\"
            data-origem=\"whatsapp\">
            <i class=\"ri-whatsapp-fill me-2\" style='font-size: xx-large;'></i>Saiba mais pelo WhatsApp!</a>
            </div>";
        }else{
            $botao = 
            "<div class=\"fixed-bottom d-flex justify-content-center mb-3\">
            <a 
            class=\"jump mx-auto btn btn-lg border border-dark border-3 d-flex align-items-center text-white \"
            style='font-weight: bolder;background-color: #009d4e;z-index: 1000;max-width: 90%;font-weight: bolder;'
            href=\"https://wa.me/$whatsApp?text=Olá, quero saber mais sobre o curso de $curso->titulo\">
            <i class=\"ri-whatsapp-fill me-2\" style='font-size: xx-large;'></i>Saiba mais pelo WhatsApp!</a>
            </div>";
        }

        return $botao;

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

    public function limitar_string($string){
        
        $limite = 80; // Número máximo de caracteres desejado

        // Verifica se a string é maior que o limite
        if (mb_strlen($string, 'UTF-8') > $limite) {
            // Corta a string até o limite
            $string_cortada = mb_substr($string, 0, $limite, 'UTF-8');

            // Garante que a string não corte palavras no meio
            $ultima_espaco = mb_strrpos($string_cortada, ' ', 0, 'UTF-8');
            $string_cortada = mb_substr($string_cortada, 0, $ultima_espaco, 'UTF-8') . '...';
        } else {
            $string_cortada = $string;
        }

        return $string_cortada; // Saída: "Esta é uma string..."

    }

    private function formatar_preco_parcelado ($preco, $parcelamento){

        $dados['preco'] = number_format($preco, 2, ',', '');
        $dados['parcelamento'] = $parcelamento;

        if($dados['preco']=="9,50"){
            $dados['preco'] = "9,60";
        }

        if($dados['preco']=='5,76' AND $dados['parcelamento']==12){
            $dados['preco'] = "7,41";
            $dados['parcelamento'] = "9";
        }elseif($dados['preco']=='3,84'){
            $dados['preco'] = "7,15";
            $dados['parcelamento'] = "6";
        }

        return $dados;

    }

}
