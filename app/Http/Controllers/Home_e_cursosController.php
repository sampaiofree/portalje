<?php
namespace App\Http\Controllers;

use App\Models\Codigo_ref;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Meta_apiController;

use App\Models\Curso;
use App\Models\Cupom;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use App\Models\RootDomainCourseConfig;
use App\Services\CourseCompleteOfferService;
use App\Services\RootDomainCoursePublicStateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

use DOMDocument;
use DOMXPath;

class Home_e_cursosController extends Controller
{

    public $dados_portal;
    private RootDomainCoursePublicStateService $rootDomainCoursePublicStateService;
    private CourseCompleteOfferService $courseCompleteOfferService;

    public function __construct(
        RootDomainCoursePublicStateService $rootDomainCoursePublicStateService,
        CourseCompleteOfferService $courseCompleteOfferService
    ) {
        $this->rootDomainCoursePublicStateService = $rootDomainCoursePublicStateService;
        $this->courseCompleteOfferService = $courseCompleteOfferService;
        $this->dados_portal = $this->carregarDadosPortal();
    }

    private function carregarDadosPortal(): array
    {
        return $this->rootDomainCoursePublicStateService->portalData();
    }

    private function rootPortalHosts(): array
    {
        return $this->rootDomainCoursePublicStateService->rootPortalHosts();
    }

    private function isRootPortalHost(?string $dominio): bool
    {
        return $this->rootDomainCoursePublicStateService->isRootPortalHost($dominio);
    }

    private function rootDomainConfigsByCourseId()
    {
        return $this->rootDomainCoursePublicStateService->configsByCourseId();
    }

    private function rootDomainConfigForCourse(?Curso $curso): ?RootDomainCourseConfig
    {
        return $this->rootDomainCoursePublicStateService->configForCourse($curso);
    }

    private function applyRootDomainPublicConfigOnCourse(Curso $curso, ?RootDomainCourseConfig $config): void
    {
        $this->rootDomainCoursePublicStateService->applyConfigOnCourse($curso, $config, $this->dados_portal);
    }

    private function buildRootDomainCourseData(Curso $curso, ?RootDomainCourseConfig $config): array
    {
        return $this->rootDomainCoursePublicStateService->buildRootDomainCourseData(
            $curso,
            $config,
            [
                'logo_padrao_url' => $this->resolverLogoPadraoUrl(),
                'logo_dark_url' => $this->resolverLogoDarkUrl(),
            ],
            $this->dados_portal
        );
    }

    public function redirecionar_whatsapp(Request $request)
    {
        $user = $this->resolverUsuarioPublicoParaWhatsapp($request);
        $selecao = $this->selecionarWhatsappParaRedirect($request, $user, true);
        $whatsapp = $selecao['whatsapp'] ?? null;

        if (!$whatsapp) {
            return redirect()->away('https://portalje.org');
        }

        $empresa = $this->resolverNomeEmpresa($user);
        $mensagem = "Olá quero saber sobre os cursos do {$empresa}";

        return redirect()->away($this->montarWhatsappExternoUrl($whatsapp, $mensagem));
    }

    public function redirecionar_whatsapp_curso(Request $request, string $curso)
    {
        if (!Schema::hasTable('curso')) {
            return redirect()->away('https://portalje.org');
        }

        $cursoModel = Curso::where('url', $curso)->first();
        if (!$cursoModel) {
            return redirect()->away('https://portalje.org');
        }

        $user = $this->resolverUsuarioPublicoParaWhatsapp($request, $cursoModel);
        $selecao = $this->selecionarWhatsappParaRedirect($request, $user, false);
        $whatsapp = $selecao['whatsapp'] ?? null;

        if (!$whatsapp) {
            return redirect()->away('https://portalje.org');
        }

        $intent = (string) $request->query('intent');
        $mensagem = $intent === 'inscricao'
            ? "Olá, quero fazer minha inscrição no curso de {$cursoModel->titulo}"
            : "Olá, quero garantir minha vaga no curso de {$cursoModel->titulo}";

        return redirect()->away($this->montarWhatsappExternoUrl($whatsapp, $mensagem));
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
        $curso->formulario = (bool)($dados['formulario_pre_checkout'] ?? true);
        $curso->user_id = $dados['user_id'];
        $curso->affiliate_code = $dados['affiliate_code'];
        $curso->company_name = $dados['company_name'] ?? 'Programa Jovem Empreendedor';
        $curso->logo_padrao_url = $dados['logo_padrao_url'] ?? asset('img/home_page/logojecolor.webp');
        $curso->logo_dark_url = $dados['logo_dark_url'] ?? asset('img/home_page/logowhite.png');
        $curso->modo_precos = 'padrao';
        $curso->cupom_principal_id = null;
        $curso->cupom_secundario_id = null;
        $curso->cupom_principal_codigo = null;
        $curso->cupom_secundario_codigo = null;

        $cursoBasePricing = clone $curso;
        $this->aplicarConfiguracaoDePrecosPorCupom($curso, $dados, !empty($desconto_banner));
        $curso->origem = 'checkout_completo';

        $curso->cidade = $this->sanitizeCityName(request()->get('c'));
        $curso->v = request()->get('v') ?? "27 Bolsas de Estudo com Desconto!";
        $desconto = request()->get('wd') ? 'w' : $desconto ;
        //PAGINA DIRETO PARA O WHATSAPP
        
        if($desconto AND $desconto =='w'){
            $curso->formulario = (bool)($dados['formulario_pre_checkout'] ?? true);
            if($curso->formulario){
                $whatsappFixoCurso = $this->normalizarWhatsappFixo(request()->get('t'));
                if (!$whatsappFixoCurso && !empty($dados['user_id'])) {
                    $whatsappUser = User::find((int) $dados['user_id']);
                    $whatsappSelecionado = $this->selecionarWhatsappAtendimentoParaPaginaPublica($whatsappUser);
                    $curso->whatsapp_atendimento = $whatsappSelecionado['whatsapp'] ?? $curso->whatsapp_atendimento;
                    $curso->whatsapp_atendimento_id = $whatsappSelecionado['id'] ?? null;
                }
                $curso->link_checkout_completo = "https://wa.me/$curso->whatsapp_atendimento?text=Olá, meu nome é {nome}, quero fazer minha inscrição no curso de $curso->titulo";
            }else{
                $curso->link_checkout_completo = $this->montarWhatsappRedirectUrl($request, $curso, ['intent' => 'inscricao']);
            }
            $curso->origem = 'whatsapp';
        }

        $publicPricingConfig = $this->buildPublicCoursePricingConfig(
            $cursoBasePricing,
            $curso,
            $dados,
            !empty($desconto_banner)
        );
        $curso->pricing_initial_state = $publicPricingConfig['initial_state'];
        $curso->pricing_offer_variants = $publicPricingConfig['offer_variants'];
        $curso->current_complete_offer_key = $publicPricingConfig['current_complete_offer_key'];
        $curso->current_basic_offer_key = $publicPricingConfig['current_basic_offer_key'];
        $curso->countdown = $publicPricingConfig['countdown'];

        $lpView = config('lp.course_view', 'novapagina');
        if (!view()->exists($lpView)) {
            $lpView = 'novapagina';
        }

        if(request()->get('g')=='1'){ 
            //PAGINA DAS AULAS GRATUITAS
            return view('cursos.aulas_gratuitas', compact('curso', 'desconto_banner'));
        }elseif(request()->get('test')=='2'){
            //PAGINA TESTE
            return view('cursos.3ofertas', compact('curso', 'desconto_banner')); 
        }elseif(request()->get('ga')=='1'){ 
            //PAGINA DAS AULAS GRATUITAS PAGINA TESTE
            return view('cursos.curso_gratuito_a', compact('curso', 'desconto_banner'));
        }elseif(request()->get('test')=='1'){
            //PAGINA TESTE
            return view('novapagina2', compact('curso', 'desconto_banner')); 
        }else{
            //PAGINA OFICIAL DOS AFILIADOS
            return view($lpView, compact('curso', 'desconto_banner')); 
        }   
        
        
    }

    private function normalizarHost(string $dominio): string
    {
        $dominio = strtolower(trim($dominio));
        if ($dominio === '') {
            return '';
        }

        if (!str_contains($dominio, '://')) {
            $dominio = 'https://' . $dominio;
        }

        $host = (string) (parse_url($dominio, PHP_URL_HOST) ?? '');
        $host = preg_replace('/^www\./', '', strtolower(trim($host)));

        return $host ?? '';
    }

    //DADOS DO USUARIO - AFILIADO OU PRODUTOR
    private function dadosusuario($dominio, $curso = null, $ref = null){
        $dominio = $this->normalizarHost((string) $dominio);

        if ($curso && !$ref && $this->isRootPortalHost($dominio)) {
            return $this->buildRootDomainCourseData(
                $curso,
                $this->rootDomainConfigForCourse($curso)
            );
        }

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
                $whatsappFloatSelecionado = $this->selecionarWhatsappAtendimentoParaBotaoFlutuante($verificar, $whatsappSelecionado);
                $whatsappFloatAtendimento = $whatsappFloatSelecionado['whatsapp'] ?? $whatsappAtendimento;

                //DADOS DO AFILIADO
                $dados = [
                    'whatsapp_atendimento' => $whatsappAtendimento,
                    'whatsapp_atendimento_id' => $whatsappSelecionado['id'] ?? null,
                    'whatsapp_float_atendimento' => $whatsappFloatAtendimento,
                    'whatsapp_float_atendimento_id' => $whatsappFloatSelecionado['id'] ?? ($whatsappSelecionado['id'] ?? null),
                    'whatsapp_atendimento_tempo' => $verificar->whatsapp_atendimento_tempo,
                    'meta_pixel_id' => $verificar->meta_pixel_id,
                    'company_name' => $this->resolverNomeEmpresa($verificar),
                    'logo_padrao_url' => $this->resolverLogoPadraoUrl($verificar),
                    'logo_dark_url' => $this->resolverLogoDarkUrl($verificar),
                    'formulario_pre_checkout' => $verificar->formulario_pre_checkout,
                    'formulario_whatsapp' => $verificar->formulario_whatsapp,
                    'user_id' => $verificar->id,
                    'modo_precos' => 'padrao',
                    'cupom_principal_id' => null,
                    'cupom_secundario_id' => null,
                    'usar_contador' => false,
                    'contador_minutos' => null,
                    'contador_acao' => null,
                    'contador_destino_oferta' => null,
                    
                ];

                if($curso){
                    $dados_codigo_ref = Codigo_ref::where('curso_id', $curso->id)->where('user_id', $verificar->id)->first();
                    if(!$dados_codigo_ref){return false;}
                    $codigo_ref = $dados_codigo_ref['codigo_ref'];
                    $modoPrecos = $dados_codigo_ref['modo_precos'] ?? 'padrao';
                    if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
                        $modoPrecos = 'padrao';
                    }

                    $dados['link_checkout_completo'] = "https://go.hotmart.com/$codigo_ref?ap=$curso->codigo_afiliado_plano_completo";
                    $dados['affiliate_code'] = $dados_codigo_ref['codigo_ref'];
                    $dados['modo_precos'] = $modoPrecos;
                    $dados['cupom_principal_id'] = !empty($dados_codigo_ref['cupom_principal_id']) ? (int) $dados_codigo_ref['cupom_principal_id'] : null;
                    $dados['cupom_secundario_id'] = !empty($dados_codigo_ref['cupom_secundario_id']) ? (int) $dados_codigo_ref['cupom_secundario_id'] : null;
                    $dados['formulario_pre_checkout'] = isset($dados_codigo_ref['formulario_pre_checkout'])
                        ? (bool) $dados_codigo_ref['formulario_pre_checkout']
                        : (bool) $dados['formulario_pre_checkout'];
                    $dados['usar_contador'] = isset($dados_codigo_ref['usar_contador'])
                        ? (bool) $dados_codigo_ref['usar_contador']
                        : false;
                    $dados['contador_minutos'] = !empty($dados_codigo_ref['contador_minutos'])
                        ? (int) $dados_codigo_ref['contador_minutos']
                        : null;
                    $dados['contador_acao'] = $dados_codigo_ref['contador_acao'] ?? null;
                    $dados['contador_destino_oferta'] = $dados_codigo_ref['contador_destino_oferta'] ?? null;
                }

                return $dados;
            }

        }

        if($ref && Schema::hasTable('codigo_ref') && Schema::hasTable('users')){
          $ref = Codigo_ref::where('codigo_ref', $ref)
              ->join('users', 'users.id', '=', 'codigo_ref.user_id')
              ->select('codigo_ref.*', 'users.*', 'codigo_ref.formulario_pre_checkout as codigo_ref_formulario_pre_checkout')
              ->first();
        }

        $refUserId = is_object($ref) ? ($ref->id ?? null) : (is_array($ref) ? ($ref['id'] ?? null) : null);
        $refWhatsapp = is_object($ref) ? ($ref->whatsapp_atendimento ?? null) : (is_array($ref) ? ($ref['whatsapp_atendimento'] ?? null) : null);
        $refWhatsappTempo = is_object($ref) ? ($ref->whatsapp_atendimento_tempo ?? null) : (is_array($ref) ? ($ref['whatsapp_atendimento_tempo'] ?? null) : null);
        $refMetaPixel = is_object($ref) ? ($ref->meta_pixel_id ?? null) : (is_array($ref) ? ($ref['meta_pixel_id'] ?? null) : null);
        $refNomeEmpresa = is_object($ref) ? ($ref->nome_empresa ?? null) : (is_array($ref) ? ($ref['nome_empresa'] ?? null) : null);
        $refLogoPadraoPath = is_object($ref) ? ($ref->logo_padrao_path ?? null) : (is_array($ref) ? ($ref['logo_padrao_path'] ?? null) : null);
        $refLogoDarkPath = is_object($ref) ? ($ref->logo_dark_path ?? null) : (is_array($ref) ? ($ref['logo_dark_path'] ?? null) : null);
        $refFormularioPreCheckout = is_object($ref)
            ? ($ref->codigo_ref_formulario_pre_checkout ?? $ref->formulario_pre_checkout ?? null)
            : (is_array($ref) ? ($ref['codigo_ref_formulario_pre_checkout'] ?? $ref['formulario_pre_checkout'] ?? null) : null);
        $refFormularioWhatsapp = is_object($ref) ? ($ref->formulario_whatsapp ?? null) : (is_array($ref) ? ($ref['formulario_whatsapp'] ?? null) : null);
        $refCodigo = is_object($ref) ? ($ref->codigo_ref ?? null) : (is_array($ref) ? ($ref['codigo_ref'] ?? null) : null);
        $refModoPrecosRaw = is_object($ref) ? ($ref->modo_precos ?? 'padrao') : (is_array($ref) ? ($ref['modo_precos'] ?? 'padrao') : 'padrao');
        $refCupomPrincipalIdRaw = is_object($ref) ? ($ref->cupom_principal_id ?? null) : (is_array($ref) ? ($ref['cupom_principal_id'] ?? null) : null);
        $refCupomSecundarioIdRaw = is_object($ref) ? ($ref->cupom_secundario_id ?? null) : (is_array($ref) ? ($ref['cupom_secundario_id'] ?? null) : null);
        $refUsarContadorRaw = is_object($ref) ? ($ref->usar_contador ?? false) : (is_array($ref) ? ($ref['usar_contador'] ?? false) : false);
        $refContadorMinutosRaw = is_object($ref) ? ($ref->contador_minutos ?? null) : (is_array($ref) ? ($ref['contador_minutos'] ?? null) : null);
        $refContadorAcaoRaw = is_object($ref) ? ($ref->contador_acao ?? null) : (is_array($ref) ? ($ref['contador_acao'] ?? null) : null);
        $refContadorDestinoOfertaRaw = is_object($ref) ? ($ref->contador_destino_oferta ?? null) : (is_array($ref) ? ($ref['contador_destino_oferta'] ?? null) : null);

        $whatsappSelecionadoRef = ['id' => null, 'whatsapp' => null];
        if ($refUserId && Schema::hasTable('users')) {
            $refUser = User::find($refUserId);
            if ($refUser) {
                $whatsappSelecionadoRef = $this->selecionarWhatsappAtendimento($refUser);
                $refNomeEmpresa = $refUser->nome_empresa ?? $refNomeEmpresa;
                $refLogoPadraoPath = $refUser->logo_padrao_path ?? $refLogoPadraoPath;
                $refLogoDarkPath = $refUser->logo_dark_path ?? $refLogoDarkPath;
            }
        }

        $dados_portal = $this->dados_portal;
        $dados = [
            'whatsapp_atendimento' => $whatsappSelecionadoRef['whatsapp'] ?? ($refWhatsapp ?? $dados_portal['telefone_suporte_alunos']),
            'whatsapp_atendimento_id' => $whatsappSelecionadoRef['id'] ?? null,
            'whatsapp_atendimento_tempo' => $refWhatsappTempo ?? $dados_portal['whatsapp_atendimento_tempo'],
            'meta_pixel_id' => $refMetaPixel,
            'company_name' => $this->resolverNomeEmpresaFromRaw($refNomeEmpresa),
            'logo_padrao_url' => $this->resolverLogoPadraoUrlFromPath($refLogoPadraoPath),
            'logo_dark_url' => $this->resolverLogoDarkUrlFromPath($refLogoDarkPath),
            'formulario_pre_checkout' => $refFormularioPreCheckout ?? $dados_portal['formulario_pre_checkout'],
            'formulario_whatsapp' => $refFormularioWhatsapp ?? $dados_portal['formulario_whatsapp'],
            'user_id' => $refUserId,
            'affiliate_code' => $refCodigo,
            'modo_precos' => in_array($refModoPrecosRaw, ['padrao', 'um_preco', 'dois_precos'], true) ? $refModoPrecosRaw : 'padrao',
            'cupom_principal_id' => !empty($refCupomPrincipalIdRaw) ? (int) $refCupomPrincipalIdRaw : null,
            'cupom_secundario_id' => !empty($refCupomSecundarioIdRaw) ? (int) $refCupomSecundarioIdRaw : null,
            'usar_contador' => (bool) $refUsarContadorRaw,
            'contador_minutos' => !empty($refContadorMinutosRaw) ? (int) $refContadorMinutosRaw : null,
            'contador_acao' => $refContadorAcaoRaw ?: null,
            'contador_destino_oferta' => $refContadorDestinoOfertaRaw ?: null,
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
    
        $host = strtolower($request->getHost());
        $pathInfo = rtrim($request->getPathInfo(), '/');
        if ($pathInfo === '') {
            $pathInfo = '/';
        }
        $isRootPortalDomain = $this->isRootPortalHost($host);
        $isHomeOrCursosPath = in_array($pathInfo, ['/', '/cursos'], true);
        $hasPageOverride = $request->has('edital')
            || $request->has('test')
            || $request->has('lista')
            || $request->has('gratuito');

        // Root portal domains should always use the W3 home unless an explicit legacy override is requested.
        if ($isRootPortalDomain && $isHomeOrCursosPath && !$hasPageOverride) {
            $cidadeLayout = $this->sanitizeCityName($cidade_desconto ?? $request->get('c'));
            $pagina = $this->dados_da_pagina(null, $cidadeLayout);
            $modoCardsW3 = $this->resolverModoCardsW3($request, null, 'curso');
            $homePageWhatsappFlow = $this->resolverHomePageWhatsappFlow($request, 'formulario');
            $pagina['cards_destino'] = $modoCardsW3;
            $usarFormularioWhatsappNaHomeW3 = !($modoCardsW3 === 'whatsapp' && $homePageWhatsappFlow === 'direto');
            $pagina['whatsapp_requires_form'] = $usarFormularioWhatsappNaHomeW3;
            $cursos = $this->listar_cursos($request, null, 'w3', $modoCardsW3, $usarFormularioWhatsappNaHomeW3);

            return view('home_e_cursos.w3', compact('cursos', 'pagina'));
        }

        

        /**PEGAR DADOS DO USUÁRIO**/
        $afiliadoID = request()->get('af') ?? null;
        $dados = $this->dadosusuario_home($request->getHost(), $afiliadoID);
        if (!$dados) {return redirect()->away('https://portalje.org');}

        $isRootRequest = $request->getPathInfo() === '/';
        $layoutOverride = in_array((string) $request->query('layout'), ['padrao', 'w3'], true)
            ? (string) $request->query('layout')
            : null;
        $homePageLayout = $dados['dados']['home_page_layout'] ?? 'padrao';
        $siteContactProvider = in_array((string) ($dados['dados']['site_contact_provider'] ?? 'whatsapp'), ['whatsapp', 'typebot'], true)
            ? (string) $dados['dados']['site_contact_provider']
            : 'whatsapp';
        $homePageDestination = $this->resolverHomePageDestinationForProvider(
            $dados['dados']['home_page_destination'] ?? 'curso',
            $siteContactProvider
        );
        $storedHomePageWhatsappFlow = in_array((string) ($dados['dados']['home_page_whatsapp_flow'] ?? 'formulario'), ['formulario', 'direto'], true)
            ? (string) $dados['dados']['home_page_whatsapp_flow']
            : 'formulario';
        $homePageWhatsappFlow = $this->resolverHomePageWhatsappFlow($request, $storedHomePageWhatsappFlow);
        if ($layoutOverride !== null) {
            $homePageLayout = $layoutOverride;
        }
        if ($isRootRequest && !empty($dados['dados']['afiliado']) && $homePageLayout === 'w3') {
            $afiliadoUser = !empty($dados['dados']['user_id'])
                ? User::find((int) $dados['dados']['user_id'])
                : null;

            if ($afiliadoUser) {
                $cidadeLayout = $this->sanitizeCityName($cidade_desconto ?? request()->get('c'));
                $pagina = $this->dados_da_pagina($afiliadoUser, $cidadeLayout);
                $modoCardsW3 = $this->resolverModoCardsW3($request, null, $homePageDestination);
                $pagina['cards_destino'] = $modoCardsW3;
                $usarFormularioWhatsappNaHomeW3 = !($modoCardsW3 === 'whatsapp' && $homePageWhatsappFlow === 'direto');
                $pagina['whatsapp_requires_form'] = $usarFormularioWhatsappNaHomeW3;
                if ($modoCardsW3 === 'whatsapp' && $usarFormularioWhatsappNaHomeW3) {
                    $whatsappSelecionado = $this->selecionarWhatsappAtendimentoParaPaginaPublica($afiliadoUser);
                    $pagina['whatsapp'] = $whatsappSelecionado['whatsapp'] ?? $pagina['whatsapp'];
                    $pagina['whatsapp_atendimento_id'] = $whatsappSelecionado['id'] ?? null;
                }
                $cursos = $this->listar_cursos($request, $afiliadoUser, 'w3', $modoCardsW3, $usarFormularioWhatsappNaHomeW3);

                $temCursoVisivel = collect($cursos)->contains(function ($curso) {
                    return !empty($curso->publicado) && !empty($curso->mostrar_na_pagina);
                });

                if (!$temCursoVisivel) {
                    return redirect()->away('https://jovemempreendedor.org/');
                }

                return view('home_e_cursos.w3', compact('cursos', 'pagina'));
            }
        }

        //PEGAR TODOS OS PARAMETROS PARA COLOCAR NA URL    
        $info = $dados['dados'];
        $info['parametros'] = isset($query) ? $query.$src : $src;
        $info['home_destination'] = $homePageDestination;
        $info['site_contact_provider'] = $siteContactProvider;
        $info['typebot'] = $siteContactProvider === 'typebot' && $homePageDestination === 'typebot';

        //CARVALHO WHATSAPP
        if (request()->has('w')) {
            $info['whatsapp'] = request()->get('w');
        } elseif ($isRootRequest && $homePageDestination === 'whatsapp') {
            $info['whatsapp'] = '1';
        } else {
            $info['whatsapp'] = null;
        }
        $whatsappFixoHome = $this->normalizarWhatsappFixo(request()->get('t'));
        if($whatsappFixoHome){
            $info['whatsapp_atendimento'] = $whatsappFixoHome;
            $info['whatsapp_atendimento_id'] = null;
        }
        $info['whatsapp'] = $watsapp_curso ?? $info['whatsapp'];
        
        
        //DESCONTO
        $d = request()->get('d') ?? null;
        if($this->normalizarHost((string) $request->getHost()) === 'jovemempreendedor.org'){
            $d = "o80";
            //$info['parametros'] =$info['parametros']."&d=o80";
        } //Desconto padrão no Portal JE Produtor        
        $desconto_banner = str_replace('o', '', $d);
       
        
        //NOME DA CIDADE
        $info['cidade'] = $this->sanitizeCityName(request()->get('c'));
        $info['cidade'] = $this->sanitizeCityName($cidade_desconto) ?? $info['cidade'];
        $info['v'] = request()->get('v') ?? "27 Bolsas de Estudo com Desconto!";
        

        /**DEFINIR SE TERÁ OU NÃO FORMULÁRIO WHATSAPP**/
        if (!$info['whatsapp']) {
            $info['formulario'] = false;
        } elseif ($isRootRequest && $homePageWhatsappFlow === 'direto') {
            $info['formulario'] = false;
        } else {
            $info['formulario'] = true;
        }
        if (!empty($info['whatsapp']) && !empty($info['formulario']) && !$whatsappFixoHome && !empty($info['user_id'])) {
            $whatsappUser = User::find((int) $info['user_id']);
            $whatsappSelecionado = $this->selecionarWhatsappAtendimentoParaPaginaPublica($whatsappUser);
            $info['whatsapp_atendimento'] = $whatsappSelecionado['whatsapp'] ?? $info['whatsapp_atendimento'];
            $info['whatsapp_atendimento_id'] = $whatsappSelecionado['id'] ?? null;
        }
        $cursos = $dados['cursos']; 
        
        if(request()->get('edital')=='1'){
            return view('landign_pages.edital', compact('cursos', 'info', 'desconto_banner'));
        }elseif(request()->get('test')=='1'){
            return view('home', compact('cursos', 'info', 'desconto_banner'));
        }elseif($isRootPortalDomain && request()->get('lista')=='1'){ 
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
        $dominio = $this->normalizarHost((string) $dominio);

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
                $siteContactProvider = $this->resolverSiteContactProvider($verificar);
                $homePageDestination = $this->resolverHomePageDestinationForProvider(
                    $verificar->home_page_destination ?? 'curso',
                    $siteContactProvider
                );
                $whatsappSelecionado = $this->selecionarWhatsappAtendimento($verificar);
                $whatsappAtendimento = $whatsappSelecionado['whatsapp'] ?? $verificar->whatsapp_atendimento;
                $whatsappFloatSelecionado = $this->selecionarWhatsappAtendimentoParaBotaoFlutuante($verificar, $whatsappSelecionado);
                $whatsappFloatAtendimento = $whatsappFloatSelecionado['whatsapp'] ?? $whatsappAtendimento;
                $whatsappDelaySeconds = isset($verificar->w3_whatsapp_float_delay_seconds)
                    ? max(0, (int) $verificar->w3_whatsapp_float_delay_seconds)
                    : 0;
                $whatsappMostrar = isset($verificar->w3_whatsapp_float_enabled)
                    ? (bool) $verificar->w3_whatsapp_float_enabled
                    : true;

                //DADOS DO AFILIADO
                $dados['dados'] = [
                    'afiliado' => true,
                    'whatsapp_atendimento' => $whatsappAtendimento,
                    'whatsapp_atendimento_id' => $whatsappSelecionado['id'] ?? null,
                    'whatsapp_float_atendimento' => $whatsappFloatAtendimento,
                    'whatsapp_float_atendimento_id' => $whatsappFloatSelecionado['id'] ?? ($whatsappSelecionado['id'] ?? null),
                    'whatsapp_atendimento_tempo' => $whatsappDelaySeconds,
                    'whatsapp_mostrar' => $whatsappMostrar,
                    'meta_pixel_id' => $verificar->meta_pixel_id,
                    'company_name' => $this->resolverNomeEmpresa($verificar),
                    'logo_padrao_url' => $this->resolverLogoPadraoUrl($verificar),
                    'logo_dark_url' => $this->resolverLogoDarkUrl($verificar),
                    'home_page_layout' => in_array((string) $verificar->home_page_layout, ['padrao', 'w3'], true)
                        ? (string) $verificar->home_page_layout
                        : 'padrao',
                    'site_contact_provider' => $siteContactProvider,
                    'home_page_destination' => $homePageDestination,
                    'home_page_whatsapp_flow' => in_array((string) $verificar->home_page_whatsapp_flow, ['formulario', 'direto'], true)
                        ? (string) $verificar->home_page_whatsapp_flow
                        : 'formulario',
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
                    ->orderBy('curso.ordem')
                    ->orderBy('curso.id')
                    ->get();

                    $dados['cursos']->each(function ($curso) {
                        $this->aplicarDadosTypebotNoCurso($curso);
                    });
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
            'whatsapp_atendimento_tempo' => 0,
            'whatsapp_mostrar' => true,
            'meta_pixel_id' => null,
            'company_name' => 'Programa Jovem Empreendedor',
            'logo_padrao_url' => $this->resolverLogoPadraoUrl(),
            'logo_dark_url' => $this->resolverLogoDarkUrl(),
            'home_page_layout' => 'padrao',
            'site_contact_provider' => 'whatsapp',
            'home_page_destination' => 'curso',
            'home_page_whatsapp_flow' => 'formulario',
            'formulario_pre_checkout' => $dados_portal['formulario_pre_checkout'],
            'formulario_whatsapp' => $dados_portal['formulario_whatsapp'],
            'user_id' => null,
            'affiliate_code' => null
        ];
        if (Schema::hasTable('curso')) {
            $dados['cursos'] = Curso::orderBy('ordem')
                                ->orderBy('id')
                                ->get();
            $dados['cursos']->each(function ($curso) {
                $this->aplicarDadosTypebotNoCurso($curso);
            });
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
        $cidade = $this->sanitizeCityName($cidade) ?? $this->sanitizeCityName($request->query('c'));

        //Pegar dados do usuario pelo dominio
        $dados_afiliado = $this->listar_user_pelo_dominio($request->getHost());

        if($dados_afiliado=='redirect'){return redirect()->away('https://portalje.org');} 

        //DADOS DA PÁGINA
        $pagina = $this->dados_da_pagina($dados_afiliado, $cidade);
        $siteContactProvider = $this->resolverSiteContactProvider($dados_afiliado instanceof User ? $dados_afiliado : null);
        $storedHomePageDestination = $dados_afiliado instanceof User
            ? $this->resolverHomePageDestinationForProvider($dados_afiliado->home_page_destination ?? 'curso', $siteContactProvider)
            : 'curso';
        $modoCardsW3 = $this->resolverModoCardsW3($request, $cidade, $storedHomePageDestination);
        $storedWhatsappFlow = $dados_afiliado instanceof User
            ? (string) ($dados_afiliado->home_page_whatsapp_flow ?? 'formulario')
            : 'formulario';
        $homePageWhatsappFlow = $this->resolverHomePageWhatsappFlow(
            $request,
            in_array($storedWhatsappFlow, ['formulario', 'direto'], true)
                ? $storedWhatsappFlow
                : 'formulario'
        );
        $pagina['cards_destino'] = $modoCardsW3;
        $usarFormularioWhatsappNaHomeW3 = !($modoCardsW3 === 'whatsapp' && $homePageWhatsappFlow === 'direto');
        $pagina['whatsapp_requires_form'] = $usarFormularioWhatsappNaHomeW3;
        if ($dados_afiliado instanceof User && $modoCardsW3 === 'whatsapp' && $usarFormularioWhatsappNaHomeW3) {
            $whatsappSelecionado = $this->selecionarWhatsappAtendimentoParaPaginaPublica($dados_afiliado);
            $pagina['whatsapp'] = $whatsappSelecionado['whatsapp'] ?? $pagina['whatsapp'];
            $pagina['whatsapp_atendimento_id'] = $whatsappSelecionado['id'] ?? null;
        }

        //LISTAR OS CURSOS 

        $cursos = $this->listar_cursos($request, $dados_afiliado, 'w3', $modoCardsW3, $usarFormularioWhatsappNaHomeW3);

        $temCursoVisivel = collect($cursos)->contains(function ($curso) {
            return !empty($curso->publicado) && !empty($curso->mostrar_na_pagina);
        });

        if (!$temCursoVisivel) {
            return redirect()->away('https://jovemempreendedor.org/');
        }

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
        $cidade = $this->sanitizeCityName($cidade);

        $nome_cidade = null;
        $curso_id = null;
        $user_id = null;
        $botao_whatsapp_flutuante = null;
        $site_contact_provider = 'whatsapp';
        $company_name = 'Programa Jovem Empreendedor';
        $logo_padrao_url = $this->resolverLogoPadraoUrl();
        $logo_dark_url = $this->resolverLogoDarkUrl();
        $botao_whatsapp_flutuante_nome_curso = "os cursos do {$company_name}";
        $form_lead_titulo = "Para receber mais informações, preencha o formulário abaixo.";
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
            $site_contact_provider = $this->resolverSiteContactProvider($user);
            $whatsappSelecionado = $this->selecionarWhatsappAtendimento($user);
            $whatsapp_atendimento = $whatsappSelecionado['whatsapp'] ?? $user->whatsapp_atendimento ?? $this->dados_portal['telefone_suporte_alunos'];
            $whatsapp_atendimento_id = $whatsappSelecionado['id'] ?? null;
            $whatsappFloatSelecionado = $this->selecionarWhatsappAtendimentoParaBotaoFlutuante($user, $whatsappSelecionado);
            $whatsapp_float_atendimento = $whatsappFloatSelecionado['whatsapp'] ?? $whatsapp_atendimento;
            $whatsapp_float_atendimento_id = $whatsappFloatSelecionado['id'] ?? $whatsapp_atendimento_id;
            $company_name = $this->resolverNomeEmpresa($user);
            $logo_padrao_url = $this->resolverLogoPadraoUrl($user);
            $logo_dark_url = $this->resolverLogoDarkUrl($user);
            $whatsapp_atendimento_tempo = isset($user->w3_whatsapp_float_delay_seconds)
                ? (int) $user->w3_whatsapp_float_delay_seconds
                : (int) ($user->whatsapp_atendimento_tempo ?? 0);
            $whatsapp_mostrar = isset($user->w3_whatsapp_float_enabled)
                ? (bool) $user->w3_whatsapp_float_enabled
                : true;
            $formulario_whatsapp = $user->formulario_whatsapp;
            $formulario_pre_checkout = $user->formulario_pre_checkout;
            $pidel_id = $user->meta_pixel_id;
        }else{ //DADOS DO PRODUTOR
            $dados =  $this->dados_portal;
            $whatsapp_atendimento =  $dados['telefone_suporte_alunos'];
            $whatsapp_atendimento_id = null;
            $whatsapp_float_atendimento = $whatsapp_atendimento;
            $whatsapp_float_atendimento_id = null;
            $whatsapp_atendimento_tempo = (int) ($dados['whatsapp_atendimento_tempo'] ?? 0);
            $whatsapp_mostrar = true;
            $formulario_whatsapp = $dados['formulario_whatsapp'];
            $formulario_pre_checkout = $dados['formulario_pre_checkout'];
            $pidel_id = null;
        }

        $botao_whatsapp_flutuante_nome_curso = "os cursos do {$company_name}";

        if($nome_cidade){
            $cidadeDestacada = e($nome_cidade);
            $headline = "27 Bolsas de Estudo liberadas para <span style='color: rgb(13, 110, 253) !important;'>$cidadeDestacada</span>";
            $headline_sub = "Escolha seu curso para falar com o nosso consultor pelo WhatsApp";
            $headline_botao = 'Escolher o meu curso agora!';
        }elseif($curso){
            $headline = $curso->titulo;
            $headline_sub = $curso->headline;
            $headline_botao = 'Quero saber mais';
            $botao_whatsapp_flutuante_nome_curso = "sobre o curso de $curso->titulo";
            $curso_id = $curso->id;
            $form_lead_titulo = "Para receber mais informações, preencha o formulário abaixo.";
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
            "whatsapp_atendimento_tempo"=> $whatsapp_atendimento_tempo,
            "whatsapp"=>$whatsapp_atendimento,
            "whatsapp_atendimento_id" => $whatsapp_atendimento_id,
            "whatsapp_float_atendimento" => $whatsapp_float_atendimento,
            "whatsapp_float_atendimento_id" => $whatsapp_float_atendimento_id,
            "whatsapp_mostrar"=> $whatsapp_mostrar,
            "site_contact_provider"=> $site_contact_provider,
            "formulario_whatsapp"=> $formulario_whatsapp,
            "formulario_pre_checkout"=> $formulario_pre_checkout,
            "botao_whatsapp_flutuante"=> $botao_whatsapp_flutuante,
            "form_lead_titulo"=> $form_lead_titulo,
            "form_lead_botao"=> $form_lead_botao,
            "pidel_id"=> $pidel_id,
            "company_name"=> $company_name,
            "logo_padrao_url"=> $logo_padrao_url,
            "logo_dark_url"=> $logo_dark_url,
        ];
    }

    private function resolverNomeEmpresa(?User $user = null): string
    {
        if (!$user) {
            return 'Programa Jovem Empreendedor';
        }

        return $this->resolverNomeEmpresaFromRaw($user->nome_empresa ?? null);
    }

    private function resolverNomeEmpresaFromRaw($valor): string
    {
        $nomeEmpresa = trim((string) ($valor ?? ''));
        return $nomeEmpresa !== '' ? $nomeEmpresa : 'Programa Jovem Empreendedor';
    }

    private function resolverLogoPadraoUrl(?User $user = null): string
    {
        $path = $user?->logo_padrao_path ?? null;
        return $this->resolverLogoPadraoUrlFromPath($path);
    }

    private function resolverLogoDarkUrl(?User $user = null): string
    {
        $path = $user?->logo_dark_path ?? null;
        return $this->resolverLogoDarkUrlFromPath($path);
    }

    private function resolverLogoPadraoUrlFromPath($path): string
    {
        return $this->resolverLogoUsuarioUrl($path, 'img/home_page/logojecolor.webp');
    }

    private function resolverLogoDarkUrlFromPath($path): string
    {
        return $this->resolverLogoUsuarioUrl($path, 'img/home_page/logowhite.png');
    }

    private function resolverLogoUsuarioUrl($path, string $fallbackAsset): string
    {
        $path = trim((string) ($path ?? ''));
        if ($path === '') {
            return asset($fallbackAsset);
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . ltrim($path, '/'));
            }
        } catch (Throwable $e) {
            return asset($fallbackAsset);
        }

        return asset($fallbackAsset);
    }

    public function listar_cursos($request, $user = null, $pagina = 'home', $modoCardsW3 = null, $usarFormularioWhatsapp = true){
        
        $modoCardsW3 = in_array($modoCardsW3, ['curso', 'whatsapp', 'typebot'], true) ? $modoCardsW3 : 'curso';

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
            $codigosRefByCourseId = $codigos_ref
                ? $codigos_ref
                    ->filter(fn ($codigoRef) => is_object($codigoRef) && isset($codigoRef->curso_id))
                    ->keyBy(fn ($codigoRef) => (int) $codigoRef->curso_id)
                : collect();
            $formulario_whatsapp = $user->formulario_whatsapp;
            $formulario_pre_checkout = $user->formulario_pre_checkout;
            $whatsAppAtendimentoId = null;
            if (in_array($pagina, ['w', 'w3', 'w4'], true)) {
                $whatsappFixo = $this->normalizarWhatsappFixo($request->query('t'));
                if ($whatsappFixo) {
                    $whatsApp = $whatsappFixo;
                    $whatsAppAtendimentoId = null;
                } elseif ($modoCardsW3 === 'whatsapp' && $usarFormularioWhatsapp) {
                    $whatsappSelecionado = $this->selecionarWhatsappAtendimentoParaPaginaPublica($user);
                    $whatsApp = $whatsappSelecionado['whatsapp'] ?? $user->whatsapp_atendimento ?? $this->dados_portal['telefone_suporte_alunos'];
                    $whatsAppAtendimentoId = $whatsappSelecionado['id'] ?? null;
                } else {
                    $whatsappSelecionado = $this->selecionarWhatsappAtendimento($user);
                    $whatsApp = $whatsappSelecionado['whatsapp'] ?? $user->whatsapp_atendimento ?? $this->dados_portal['telefone_suporte_alunos'];
                    $whatsAppAtendimentoId = $whatsappSelecionado['id'] ?? null;
                }
            } else {
                $whatsApp =  $user->whatsapp_atendimento;
            }
            $data_user = $user->id;
        }else{
            $dados_portal = $this->dados_portal;
            $codigos_ref = collect();
            $codigosRefByCourseId = collect();
            $formulario_whatsapp = $dados_portal['formulario_whatsapp'];
            $formulario_pre_checkout = $dados_portal['formulario_pre_checkout'];
            $whatsApp =  $dados_portal['telefone_suporte_alunos'];
            $whatsAppAtendimentoId = null;
            $data_user = null;
        }

        $isRootPortalHost = !$user && $this->isRootPortalHost((string) $request->getHost());
        $rootDomainConfigsByCourseId = $isRootPortalHost
            ? $this->rootDomainConfigsByCourseId()
            : collect();
        
        $datacursos = Curso::orderBy('ordem')
            ->orderBy('id')
            ->get();

        $cursos = [];
        $vagas = $this->vagas();
        $n=0;

        foreach ($datacursos as $curso) {
            $cursoEncontrado = false;
            $codigoRefAtual = null;
            $vaga =  $vagas[$n]; $n++;
            //ADICIONAR CÓDIGO REF DO USER EM CADA CURSO
            if ($codigosRefByCourseId->isNotEmpty()) {
                $codigo_ref = $codigosRefByCourseId->get((int) $curso->id);

                if ($codigo_ref) {
                    $codigoRefAtual = $codigo_ref;
                    $curso->codigo_ref = $codigo_ref->codigo_ref;
                    $curso->codigo_ref_id = $codigo_ref->id;
                    $curso->mostrar_curso = $codigo_ref->mostrar_curso;
                    $modoPrecos = $codigo_ref->modo_precos ?? 'padrao';
                    if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
                        $modoPrecos = 'padrao';
                    }
                    $curso->modo_precos = $modoPrecos;
                    $curso->cupom_principal_id = !empty($codigo_ref->cupom_principal_id) ? (int) $codigo_ref->cupom_principal_id : null;
                    $curso->cupom_secundario_id = !empty($codigo_ref->cupom_secundario_id) ? (int) $codigo_ref->cupom_secundario_id : null;
                    $curso->formulario_pre_checkout = isset($codigo_ref->formulario_pre_checkout)
                        ? (bool) $codigo_ref->formulario_pre_checkout
                        : true;
                    $curso->usar_contador = isset($codigo_ref->usar_contador)
                        ? (bool) $codigo_ref->usar_contador
                        : false;
                    $curso->contador_minutos = !empty($codigo_ref->contador_minutos)
                        ? (int) $codigo_ref->contador_minutos
                        : null;
                    $curso->contador_acao = $codigo_ref->contador_acao ?? null;
                    $curso->contador_destino_oferta = $codigo_ref->contador_destino_oferta ?? null;
                    $cursoEncontrado = true;
                }
            }

            if (!$cursoEncontrado) {
                $curso->codigo_ref = false;
                $curso->codigo_ref_id = false;
                $curso->mostrar_curso = false;
                $curso->modo_precos = 'padrao';
                $curso->cupom_principal_id = null;
                $curso->cupom_secundario_id = null;
                $curso->formulario_pre_checkout = true;
                $curso->usar_contador = false;
                $curso->contador_minutos = null;
                $curso->contador_acao = null;
                $curso->contador_destino_oferta = null;
            }

            if ($isRootPortalHost) {
                $this->applyRootDomainPublicConfigOnCourse(
                    $curso,
                    $rootDomainConfigsByCourseId->get($curso->id)
                );
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

            $this->aplicarDadosTypebotNoCurso($curso, $codigoRefAtual);
            $curso->typebot_whatsapp_url = $this->montarTypebotWhatsappUrl($whatsApp ?? null);

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
                    <a class='lead'  target=\"_blanck\" data-href=\"https://wa.me/$whatsApp?text=Olá, quero garantir minha vaga no curso de $curso->titulo\">
                        <img src=\"$src\" alt=\"$curso->titulo\" class=\"img-fluid rounded-4 border border-1\">
                    </a>";
                }*/
            }elseif($pagina == 'w3' OR $pagina == 'w4'){
                $curso->card_image = asset('storage/' . $curso->capa_vertical);
                $curso->card_headline = $this->limitar_string(str_replace('"', '', (string) $curso->headline));
                $curso->card_vagas = $vaga;

                if ($modoCardsW3 === 'whatsapp') {
                    if ($usarFormularioWhatsapp) {
                        $curso->card_link = "https://wa.me/$whatsApp?text=Olá, meu nome é {nome} e quero saber mais sobre o curso de $curso->titulo";
                    } else {
                        $curso->card_link = $this->montarWhatsappRedirectUrl($request, $curso);
                    }
                    $curso->card_user_id = $data_user;
                    $curso->card_origem = 'whatsapp';
                    $curso->card_whatsapp_atendimento_id = $whatsAppAtendimentoId;
                } elseif ($modoCardsW3 === 'typebot') {
                    $curso->card_link = $this->montarLinkCursoCardW3($request, $curso);
                    $curso->card_user_id = null;
                    $curso->card_origem = 'typebot';
                    $curso->card_whatsapp_atendimento_id = null;
                } else {
                    $curso->card_link = $this->montarLinkCursoCardW3($request, $curso);
                    $curso->card_user_id = null;
                    $curso->card_origem = 'curso';
                    $curso->card_whatsapp_atendimento_id = null;
                }
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

    private function sanitizeCityName($city): ?string
    {
        $city = trim((string) ($city ?? ''));

        if ($city === '') {
            return null;
        }

        $city = strip_tags($city);
        $city = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $city);
        $city = preg_replace('/\s+/u', ' ', $city);
        $city = trim((string) $city);

        if ($city === '') {
            return null;
        }

        return mb_substr($city, 0, 80);
    }

    public function listar_todos_cursos(){

        $cursos = Curso::orderBy('ordem')
            ->orderBy('id')
            ->get();
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
        $dominio = $this->normalizarHost((string) $dominio);

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

    private function resolverModoCardsW3(Request $request, ?string $cidade = null, string $defaultDestination = 'curso'): string
    {
        $queryDestination = (string) $request->query('destination');

        if ($defaultDestination === 'typebot') {
            if (in_array($queryDestination, ['curso', 'typebot'], true)) {
                return $queryDestination;
            }

            if ((string) $request->query('w') === '1') {
                return 'whatsapp';
            }

            return 'typebot';
        }

        if (in_array($queryDestination, ['curso', 'whatsapp'], true)) {
            return $queryDestination;
        }

        if (!empty($cidade)) {
            return 'whatsapp';
        }

        if ((string) $request->query('w') === '1') {
            return 'whatsapp';
        }

        return in_array($defaultDestination, ['curso', 'whatsapp'], true) ? $defaultDestination : 'curso';
    }

    private function resolverSiteContactProvider(?User $user): string
    {
        if (!$user || !Schema::hasColumn('users', 'site_contact_provider')) {
            return 'whatsapp';
        }

        $provider = (string) ($user->site_contact_provider ?? 'whatsapp');

        return in_array($provider, ['whatsapp', 'typebot'], true) ? $provider : 'whatsapp';
    }

    private function resolverHomePageDestinationForProvider($destination, string $siteContactProvider): string
    {
        $destination = (string) ($destination ?? 'curso');
        $allowedDestinations = $siteContactProvider === 'typebot'
            ? ['curso', 'typebot']
            : ['curso', 'whatsapp'];

        return in_array($destination, $allowedDestinations, true) ? $destination : 'curso';
    }

    private function montarTypebotWhatsappUrl($whatsapp): string
    {
        $digits = preg_replace('/\D/', '', (string) ($whatsapp ?? ''));

        return $digits !== '' ? 'https://wa.me/' . $digits : '';
    }

    private function aplicarDadosTypebotNoCurso(object $curso, ?Codigo_ref $ref = null): void
    {
        $cursoModel = $this->normalizarCursoTypebot($curso);
        $ref = $ref ?: $this->normalizarCodigoRefTypebot($curso);
        $completeOffer = $this->courseCompleteOfferService->completeOffer($cursoModel, $ref);

        $curso->typebot_curso_nome = trim((string) ($cursoModel->titulo ?? ''));
        $curso->typebot_curso_preco = (string) ($completeOffer['price_value'] ?? '');
        $curso->typebot_checkout_url = (string) ($completeOffer['checkout_url'] ?? '');
        $curso->typebot_curso_imagem_url = $this->montarTypebotImagemCursoUrl($curso->capa_quadrada ?? null);
        $curso->typebot_curso_areas = $this->extrairAreasTypebot($curso->areas_de_atuacao ?? null);
        $curso->typebot_curso_conteudo = $this->extrairTopicosPrincipaisTypebot($curso->conteudo_principal ?? null);
        $curso->typebot_curso_bonus = $this->extrairTopicosPrincipaisTypebot($curso->conteudo_bonus ?? null);
    }

    private function normalizarCursoTypebot(object $curso): Curso
    {
        if ($curso instanceof Curso) {
            return $curso;
        }

        $cursoModel = new Curso();
        $attributes = method_exists($curso, 'getAttributes')
            ? $curso->getAttributes()
            : get_object_vars($curso);

        $cursoModel->setRawAttributes($attributes, true);

        return $cursoModel;
    }

    private function normalizarCodigoRefTypebot(object $curso): ?Codigo_ref
    {
        $codigoRef = trim((string) ($curso->codigo_ref ?? ''));

        if ($codigoRef === '') {
            return null;
        }

        $attributes = [
            'codigo_ref' => $codigoRef,
        ];

        foreach (['curso_id', 'modo_precos', 'cupom_principal_id', 'cupom_secundario_id'] as $field) {
            if (isset($curso->{$field})) {
                $attributes[$field] = $curso->{$field};
            }
        }

        $ref = new Codigo_ref();
        $ref->setRawAttributes($attributes, true);

        return $ref;
    }

    private function montarTypebotImagemCursoUrl($path): string
    {
        $path = trim((string) ($path ?? ''));

        if ($path === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    private function extrairAreasTypebot($areas): array
    {
        $areas = trim((string) ($areas ?? ''));

        if ($areas === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($area) => trim((string) $area),
            preg_split('/[\/,]/', $areas) ?: []
        )));
    }

    private function extrairTopicosPrincipaisTypebot($html): array
    {
        $conteudos = $this->lista_conteudo((string) ($html ?? '')) ?? [];
        $titulos = [];

        foreach ($conteudos as $conteudo) {
            $titulo = trim((string) ($conteudo['title'] ?? ''));

            if ($titulo !== '') {
                $titulos[] = $titulo;
            }

            if (count($titulos) >= 12) {
                break;
            }
        }

        return array_values(array_unique($titulos));
    }

    private function resolverHomePageWhatsappFlow(Request $request, string $defaultFlow = 'formulario'): string
    {
        $queryFlow = (string) $request->query('whatsapp_flow');
        if (in_array($queryFlow, ['formulario', 'direto'], true)) {
            return $queryFlow;
        }

        return in_array($defaultFlow, ['formulario', 'direto'], true) ? $defaultFlow : 'formulario';
    }

    private function montarLinkCursoCardW3(Request $request, Curso $curso): string
    {
        $query = $request->query();
        unset($query['w'], $query['t'], $query['destination']);

        $url = 'https://' . $request->getHost() . '/' . ltrim((string) $curso->url, '/');
        $queryString = http_build_query($query);

        if ($queryString !== '') {
            return $url . '?' . $queryString;
        }

        return $url;
    }

    private function montarWhatsappRedirectUrl(Request $request, ?Curso $curso = null, array $extras = []): string
    {
        $path = '/whatsapp';

        if ($curso) {
            $path .= '/curso/' . rawurlencode((string) $curso->url);
        }

        $query = array_merge($request->query(), $extras);
        foreach ($query as $key => $value) {
            if ($value === null || $value === '') {
                unset($query[$key]);
            }
        }

        $queryString = http_build_query($query);
        $url = $request->getSchemeAndHttpHost() . $path;

        return $queryString !== '' ? $url . '?' . $queryString : $url;
    }

    private function montarWhatsappExternoUrl(string $whatsapp, string $mensagem): string
    {
        $whatsapp = preg_replace('/\D/', '', $whatsapp);

        return 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode($mensagem);
    }

    private function resolverUsuarioPublicoParaWhatsapp(Request $request, ?Curso $curso = null): ?User
    {
        $afiliadoId = (int) $request->query('af', 0);
        if ($afiliadoId > 0) {
            $user = User::find($afiliadoId);
            if ($user) {
                return $user;
            }
        }

        $ref = trim((string) $request->query('ref', ''));
        if ($ref !== '' && Schema::hasTable('codigo_ref')) {
            $query = Codigo_ref::where('codigo_ref', $ref);
            if ($curso) {
                $query->where('curso_id', $curso->id);
            }

            $codigoRef = $query->first();
            if ($codigoRef) {
                $user = User::find($codigoRef->user_id);
                if ($user) {
                    return $user;
                }
            }
        }

        $dominio = $this->normalizarHost((string) $request->getHost());
        if (!$this->isRootPortalHost($dominio) && $dominio !== 'dns.portalje.org' && $dominio !== 'dns.jovemempreendedor.org') {
            return User::where('dominio', $dominio)
                ->orWhere('dominio_externo', $dominio)
                ->first();
        }

        return null;
    }

    private function normalizarWhatsappFixo($value): ?string
    {
        $whatsapp = preg_replace('/\D/', '', (string) ($value ?? ''));

        return strlen($whatsapp) > 10 && strlen($whatsapp) <= 15 ? $whatsapp : null;
    }

    private function selecionarWhatsappParaRedirect(Request $request, ?User $user, bool $usarCanalFixoFlutuante): array
    {
        $whatsappFixo = $this->normalizarWhatsappFixo($request->query('t'));
        if ($whatsappFixo) {
            return ['id' => null, 'whatsapp' => $whatsappFixo, 'source' => 'query'];
        }

        if ($user) {
            if ($usarCanalFixoFlutuante) {
                $canalFixo = $this->selecionarWhatsappFlutuanteFixo($user);
                if ($canalFixo) {
                    return $canalFixo + ['source' => 'float_fixed'];
                }
            }

            $selecionado = $this->selecionarWhatsappAtendimentoParaRedirectRodizio($user);
            if ($selecionado) {
                return $selecionado + ['source' => 'rotation'];
            }

            $fallbackWhatsapp = preg_replace('/\D/', '', (string) $user->whatsapp_atendimento);
            if ($fallbackWhatsapp !== '') {
                return ['id' => null, 'whatsapp' => $fallbackWhatsapp, 'source' => 'legacy'];
            }
        }

        $portalWhatsapp = preg_replace('/\D/', '', (string) ($this->dados_portal['telefone_suporte_alunos'] ?? ''));

        return ['id' => null, 'whatsapp' => $portalWhatsapp ?: null, 'source' => 'portal'];
    }

    private function selecionarWhatsappFlutuanteFixo(User $user): ?array
    {
        if (!Schema::hasTable('whatsapp_atendimento') || !Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            return null;
        }

        $selectedId = (int) ($user->w3_whatsapp_float_whatsapp_atendimento_id ?? 0);
        if ($selectedId <= 0) {
            return null;
        }

        $registro = $user->whatsappAtendimentos()
            ->where('id', $selectedId)
            ->where('is_active', true)
            ->first();

        if (!$registro) {
            return null;
        }

        return [
            'id' => $registro->id,
            'whatsapp' => (string) $registro->whatsapp,
        ];
    }

    private function selecionarWhatsappAtendimentoParaRedirectRodizio(User $user): ?array
    {
        if (
            !Schema::hasTable('whatsapp_atendimento')
            || !Schema::hasColumn('whatsapp_atendimento', 'last_routed_at')
            || !Schema::hasColumn('whatsapp_atendimento', 'routed_count')
        ) {
            $fallback = $this->selecionarWhatsappAtendimento($user);
            return !empty($fallback['whatsapp']) ? $fallback : null;
        }

        $registroSessao = $this->selecionarWhatsappAtendimentoDaSessao($user);
        if ($registroSessao) {
            return $registroSessao;
        }

        $selecionado = $this->selecionarWhatsappAtendimentoRodizio($user);
        $this->guardarWhatsappAtendimentoNaSessao($user, $selecionado);

        return $selecionado;
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

    private function selecionarWhatsappAtendimentoParaPaginaPublica(?User $user): array
    {
        if (!$user) {
            return ['id' => null, 'whatsapp' => null];
        }

        $fallbackWhatsapp = preg_replace('/\D/', '', (string) $user->whatsapp_atendimento);

        if (
            !Schema::hasTable('whatsapp_atendimento')
            || !Schema::hasColumn('whatsapp_atendimento', 'last_routed_at')
            || !Schema::hasColumn('whatsapp_atendimento', 'routed_count')
        ) {
            return $this->selecionarWhatsappAtendimento($user);
        }

        $registroSessao = $this->selecionarWhatsappAtendimentoDaSessao($user);
        if ($registroSessao) {
            return $registroSessao;
        }

        $registro = $this->selecionarWhatsappAtendimentoRodizio($user, true);

        if ($registro) {
            $this->guardarWhatsappAtendimentoNaSessao($user, $registro);
            return $registro;
        }

        return ['id' => null, 'whatsapp' => $fallbackWhatsapp ?: null];
    }

    private function whatsappAtendimentoSessionKey(User $user): string
    {
        return 'portalje.whatsapp_atendimento.public_user.' . $user->id;
    }

    private function selecionarWhatsappAtendimentoDaSessao(User $user): ?array
    {
        if (!Schema::hasTable('whatsapp_atendimento')) {
            return null;
        }

        $sessionKey = $this->whatsappAtendimentoSessionKey($user);
        $registroId = (int) session($sessionKey, 0);

        if ($registroId <= 0) {
            return null;
        }

        $registro = $user->whatsappAtendimentos()
            ->where('id', $registroId)
            ->where('is_active', true)
            ->first();

        if (!$registro) {
            session()->forget($sessionKey);
            return null;
        }

        return [
            'id' => $registro->id,
            'whatsapp' => (string) $registro->whatsapp,
        ];
    }

    private function guardarWhatsappAtendimentoNaSessao(User $user, ?array $selecao): void
    {
        $registroId = (int) ($selecao['id'] ?? 0);

        if ($registroId <= 0) {
            return;
        }

        session()->put($this->whatsappAtendimentoSessionKey($user), $registroId);
    }

    private function selecionarWhatsappAtendimentoRodizio(User $user, bool $usarCache = false): ?array
    {
        $request = request();
        $cacheKey = 'portalje.whatsapp_atendimento_selection.' . $user->id;

        if ($usarCache && $request->attributes->has($cacheKey)) {
            return $request->attributes->get($cacheKey);
        }

        $selecao = DB::transaction(function () use ($user) {
            $registro = $user->whatsappAtendimentos()
                ->where('is_active', true)
                ->orderByRaw('CASE WHEN last_routed_at IS NULL THEN 0 ELSE 1 END')
                ->orderBy('last_routed_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$registro) {
                return null;
            }

            WhatsappAtendimento::whereKey($registro->id)->update([
                'last_routed_at' => now(),
                'routed_count' => DB::raw('COALESCE(routed_count, 0) + 1'),
            ]);

            return [
                'id' => $registro->id,
                'whatsapp' => (string) $registro->whatsapp,
            ];
        });

        if ($usarCache) {
            $request->attributes->set($cacheKey, $selecao);
        }

        return $selecao;
    }

    private function selecionarWhatsappAtendimentoParaBotaoFlutuante(?User $user, ?array $fallbackSelection = null): array
    {
        if (!$user) {
            return ['id' => null, 'whatsapp' => null];
        }

        if (!Schema::hasTable('whatsapp_atendimento') || !Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            return $fallbackSelection ?? $this->selecionarWhatsappAtendimento($user);
        }

        $selectedId = (int) ($user->w3_whatsapp_float_whatsapp_atendimento_id ?? 0);

        if ($selectedId > 0) {
            $registro = $user->whatsappAtendimentos()
                ->where('id', $selectedId)
                ->where('is_active', true)
                ->first();

            if ($registro) {
                return [
                    'id' => $registro->id,
                    'whatsapp' => (string) $registro->whatsapp,
                ];
            }
        }

        return $fallbackSelection ?? $this->selecionarWhatsappAtendimento($user);
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
            data-link=\"https://wa.me/$whatsApp?text=Olá, quero garantir minha vaga no curso de $curso->titulo\" 
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
            href=\"https://wa.me/$whatsApp?text=Olá, quero garantir minha vaga no curso de $curso->titulo\">
            <i class=\"ri-whatsapp-fill me-2\" style='font-size: xx-large;'></i>Saiba mais pelo WhatsApp!</a>
            </div>";
        }

        return $botao;

    }

    private function buildPublicCoursePricingConfig(
        Curso $cursoBasePricing,
        Curso $cursoAtual,
        array $dados,
        bool $descontoBannerAtivo
    ): array {
        return $this->rootDomainCoursePublicStateService->buildPublicCoursePricingConfig(
            $cursoBasePricing,
            $cursoAtual,
            $dados,
            $descontoBannerAtivo
        );
    }

    private function buildPublicCourseInitialState(Curso $cursoAtual, bool $mostrarPlanoSecundario): array
    {
        $completeOffer = $this->buildOfferPayloadFromCourse($cursoAtual, 'completo');
        $basicOffer = $mostrarPlanoSecundario
            ? $this->buildOfferPayloadFromCourse($cursoAtual, 'basico')
            : null;

        return [
            'layout' => $mostrarPlanoSecundario ? 'two' : 'one',
            'hero' => $mostrarPlanoSecundario && $basicOffer
                ? [
                    'label' => $basicOffer['hero_label'],
                    'value' => $basicOffer['price_value'],
                    'cash_value' => $basicOffer['cash_value'],
                    'show_cash_line' => false,
                    'plan' => 'basico',
                ]
                : [
                    'label' => $completeOffer['hero_label'],
                    'value' => $completeOffer['price_value'],
                    'cash_value' => $completeOffer['cash_value'],
                    'show_cash_line' => $completeOffer['show_cash_line'],
                    'plan' => 'completo',
                ],
            'cards' => [
                'basico' => $basicOffer,
                'completo' => $completeOffer,
            ],
        ];
    }

    private function buildPublicCourseOfferVariants(Curso $cursoBasePricing, string $modoPrecos): array
    {
        $variants = [
            'completo_padrao' => $this->buildOfferPayloadFromCourse($cursoBasePricing, 'completo'),
        ];

        if ($modoPrecos === 'padrao') {
            $variants['basico_padrao'] = $this->buildOfferPayloadFromCourse($cursoBasePricing, 'basico');
            return $variants;
        }

        if (!Schema::hasTable('cupons')) {
            return $variants;
        }

        $cupons = Cupom::query()->orderBy('id')->get();
        foreach ($cupons as $cupom) {
            $variants['completo_cupom:' . $cupom->id] = $this->buildOfferPayloadFromCourse(
                $this->applyOfferCouponOnClonedCourse($cursoBasePricing, 'completo', $cupom),
                'completo'
            );
        }

        if ($modoPrecos === 'dois_precos') {
            foreach ($cupons as $cupom) {
                $variants['basico_cupom:' . $cupom->id] = $this->buildOfferPayloadFromCourse(
                    $this->applyOfferCouponOnClonedCourse($cursoBasePricing, 'basico', $cupom),
                    'basico'
                );
            }
        }

        return $variants;
    }

    private function applyOfferCouponOnClonedCourse(Curso $cursoBasePricing, string $plano, Cupom $cupom): Curso
    {
        $cursoVariant = clone $cursoBasePricing;

        $precoCheioCompleto = $this->extrairValorMonetario($cursoBasePricing->preco_cheio_completo);
        $precoParcelaCompleto = $this->extrairValorMonetario($cursoBasePricing->preco_parcelado_completo);

        if ($plano === 'completo') {
            $cursoVariant->link_checkout_completo = $this->aplicarCupomNoCheckoutUrl(
                $cursoVariant->link_checkout_completo,
                $cupom->codigo
            );

            if ($precoCheioCompleto !== null) {
                $cursoVariant->preco_cheio_completo = $this->formatarValorMonetario(
                    $this->aplicarDescontoPercentual($precoCheioCompleto, (float) $cupom->desconto)
                );
            }

            if ($precoParcelaCompleto !== null) {
                $cursoVariant->preco_parcelado_completo = $this->formatarPrecoParcelado(
                    $this->aplicarDescontoPercentual($precoParcelaCompleto, (float) $cupom->desconto),
                    $cursoVariant->parcelamento
                );
            }

            return $cursoVariant;
        }

        $cursoVariant->link_checkout_basico = $this->aplicarCupomNoCheckoutUrl(
            $cursoVariant->link_checkout_basico,
            $cupom->codigo
        );

        if ($precoCheioCompleto !== null) {
            $cursoVariant->preco_cheio_basico = $this->formatarValorMonetario(
                $this->aplicarDescontoPercentual($precoCheioCompleto, (float) $cupom->desconto)
            );
        }

        if ($precoParcelaCompleto !== null) {
            $cursoVariant->preco_parcelado_basico = $this->formatarPrecoParcelado(
                $this->aplicarDescontoPercentual($precoParcelaCompleto, (float) $cupom->desconto),
                $cursoVariant->parcelamento
            );
        }

        return $cursoVariant;
    }

    private function buildOfferPayloadFromCourse(Curso $curso, string $plano): array
    {
        $isBasico = $plano === 'basico';
        $precoCheio = $isBasico
            ? ($curso->preco_cheio_basico ?? null)
            : ($curso->preco_cheio_completo ?? null);
        $precoParcelado = $isBasico
            ? ($curso->preco_parcelado_basico ?? null)
            : ($curso->preco_parcelado_completo ?? null);
        $precoCheioValor = $this->extrairValorMonetario($precoCheio);
        $ocultarParcelado = $precoCheioValor !== null && $precoCheioValor < 100;

        return [
            'plan' => $isBasico ? 'basico' : 'completo',
            'price_value' => $ocultarParcelado
                ? ($precoCheio ?? 'Consulte')
                : ($precoParcelado ?? 'Consulte'),
            'cash_value' => $precoCheio ?? 'consulte',
            'show_cash_line' => !$ocultarParcelado,
            'checkout_url' => $isBasico
                ? ($curso->link_checkout_basico ?? '#')
                : ($curso->link_checkout_completo ?? '#'),
            'requires_lead' => !empty($curso->formulario),
            'cta_label' => $isBasico ? 'Quero o plano básico' : 'Quero o plano completo',
            'hero_label' => $isBasico ? 'Investimento único de' : 'Investimento do plano completo',
        ];
    }

    private function normalizePublicCountdownConfig(
        array $dados,
        string $modoPrecos,
        array $offerVariants,
        bool $paginaPermiteCountdown,
        bool $planoBasicoVisivel
    ): array {
        $minutes = !empty($dados['contador_minutos']) ? (int) $dados['contador_minutos'] : null;
        $action = $dados['contador_acao'] ?? null;
        $destinationOffer = $dados['contador_destino_oferta'] ?? null;
        $enabled = !empty($dados['usar_contador']) && $paginaPermiteCountdown;

        if (!$enabled) {
            return [
                'enabled' => false,
                'minutes' => null,
                'action' => null,
                'destination_offer' => null,
                'storage_key' => null,
                'end_label' => 'Encerrado',
            ];
        }

        if (!in_array($minutes, [1, 5, 10, 20, 30, 50], true)) {
            $enabled = false;
        }

        if (!in_array($action, ['nada', 'encerrar_basico', 'alterar_preco', 'whatsapp'], true)) {
            $enabled = false;
        }

        if ($action === 'encerrar_basico' && (!$planoBasicoVisivel || !in_array($modoPrecos, ['padrao', 'dois_precos'], true))) {
            $enabled = false;
        }

        if ($action === 'alterar_preco' && !array_key_exists((string) $destinationOffer, $offerVariants)) {
            $enabled = false;
        }

        if (
            $action === 'alterar_preco' &&
            is_string($destinationOffer) &&
            str_starts_with($destinationOffer, 'basico_') &&
            !$planoBasicoVisivel
        ) {
            $enabled = false;
        }

        if (!$enabled) {
            return [
                'enabled' => false,
                'minutes' => null,
                'action' => null,
                'destination_offer' => null,
                'storage_key' => null,
                'end_label' => 'Encerrado',
            ];
        }

        return [
            'enabled' => true,
            'minutes' => $minutes,
            'action' => $action,
            'destination_offer' => $action === 'alterar_preco' ? (string) $destinationOffer : null,
            'storage_key' => null,
            'end_label' => 'Encerrado',
        ];
    }

    private function aplicarConfiguracaoDePrecosPorCupom(Curso $curso, array $dados, bool $descontoBannerAtivo): void
    {
        $this->rootDomainCoursePublicStateService->applyPricingConfigurationByCoupon($curso, $dados, $descontoBannerAtivo);
    }

    private function forcarConfiguracaoPadraoDePrecos(Curso $curso): void
    {
        $curso->modo_precos = 'padrao';
        $curso->cupom_principal_id = null;
        $curso->cupom_secundario_id = null;
        $curso->cupom_principal_codigo = null;
        $curso->cupom_secundario_codigo = null;
    }

    private function extrairValorMonetario(?string $valor): ?float
    {
        if (!$valor) {
            return null;
        }

        // Remove prefixos de parcelamento (ex.: "12x" ou "12x de ") antes do parse monetario.
        $valorNormalizado = preg_replace('/^\s*\d+\s*x(?:\s*de)?\s*/i', '', trim($valor)) ?? trim($valor);
        $somenteNumeros = preg_replace('/[^\d,.]/', '', $valorNormalizado);
        if (!$somenteNumeros) {
            return null;
        }

        if (str_contains($somenteNumeros, ',') && str_contains($somenteNumeros, '.')) {
            $ultimaVirgula = strrpos($somenteNumeros, ',');
            $ultimoPonto = strrpos($somenteNumeros, '.');

            if ($ultimaVirgula !== false && $ultimoPonto !== false && $ultimaVirgula > $ultimoPonto) {
                $somenteNumeros = str_replace('.', '', $somenteNumeros);
                $somenteNumeros = str_replace(',', '.', $somenteNumeros);
            } else {
                $somenteNumeros = str_replace(',', '', $somenteNumeros);
            }
        } elseif (str_contains($somenteNumeros, ',')) {
            $somenteNumeros = str_replace('.', '', $somenteNumeros);
            $somenteNumeros = str_replace(',', '.', $somenteNumeros);
        } elseif (substr_count($somenteNumeros, '.') > 1) {
            $partes = explode('.', $somenteNumeros);
            $decimal = array_pop($partes);
            $somenteNumeros = implode('', $partes) . '.' . $decimal;
        }

        if (!is_numeric($somenteNumeros)) {
            return null;
        }

        return (float) $somenteNumeros;
    }

    private function formatarValorMonetario(?float $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        return 'R$' . number_format($valor, 2, ',', '.');
    }

    private function formatarPrecoParcelado(?float $valorParcela, $parcelamento): ?string
    {
        if ($valorParcela === null) {
            return null;
        }

        $preco = $this->formatar_preco_parcelado($valorParcela, $parcelamento);
        return $preco['parcelamento'] . "xR$" . $preco['preco'];
    }

    private function aplicarDescontoPercentual(?float $valor, float $desconto): ?float
    {
        if ($valor === null) {
            return null;
        }

        return $valor * (1 - ($desconto / 100));
    }

    private function aplicarCupomNoCheckoutUrl(?string $url, ?string $codigoCupom): ?string
    {
        if (!$url || !$codigoCupom) {
            return $url;
        }

        if (preg_match('/([?&])offDiscount=[^&]*/', $url)) {
            return preg_replace('/([?&])offDiscount=[^&]*/', '$1offDiscount=' . $codigoCupom, $url, 1) ?? $url;
        }

        $separador = str_contains($url, '?') ? '&' : '?';
        return $url . $separador . 'offDiscount=' . $codigoCupom;
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

        if($dados['preco']=='6,53' AND (int) $dados['parcelamento']===12){
            $dados['preco'] = "7,04";
            $dados['parcelamento'] = "11";
        }elseif($dados['preco']=='2,91' AND (int) $dados['parcelamento']===12){
            $dados['preco'] = "7,95";
            $dados['parcelamento'] = "4";
        }elseif($dados['preco']=='5,76' AND $dados['parcelamento']==12){
            $dados['preco'] = "7,41";
            $dados['parcelamento'] = "9";
        }elseif($dados['preco']=='3,84'){
            $dados['preco'] = "7,15";
            $dados['parcelamento'] = "6";
        }

        return $dados;

    }

}
