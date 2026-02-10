<?php
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\Home_e_cursosController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ManController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Meta_apiController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\Alunos;
use App\Http\Controllers\ShortenedUrlController;
use App\Http\Controllers\ViajandinhoController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\FormacaoEmpresarialController;
use App\Exports\TabelaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\FacebookConversionsController;
use App\Http\Controllers\ProfileController;
use Laravel\Folio\Folio; 

//require __DIR__.'/auth.php';

Auth::routes(['verify' => true]);

Route::domain('jovemempreendedor.org')->group(function () {
    Route::redirect('/home', 'https://jovemempreendedor.org/?src=remarketing&sck=remarketing&d=o80');
});


//TESTES
Route::view('/test', 'cursos.iframe');
Route::view('/edital', 'landign_pages.edital');    
Route::view('/edital2', 'landign_pages.edital2');  
Route::view('/quiz', 'quiz.quiz_2');  
Route::view('/quiz2', 'quiz.quiz_cursos_gratuitos2');
Route::get('/manleads', [ManController::class, 'leads']);  
Route::view('/modelo1', 'paginas_carvalho.modelo1');
Route::view('/emprego-7-dias', 'landign_pages.emprego_7_dias');
Route::view('/workshop', 'landign_pages.vendedor_online');

//BIO
Route::view('/bio', 'bio');

//GRUPO_SEXTA_DA_OPORTUNIDADE
Route::view('/grupo_vip', 'grupo_sexta_oportunidade');  

//API FACEBOOK 26-05-2025
Route::post('/facebook/enviar-evento', [FacebookConversionsController::class, 'enviarEvento'])->name('facebook.enviarEvento');



//LANDIG PAGES
Route::prefix('lp')->group(function () {
    Route::get('formacao_empresarial/{preco?}', function ($preco = null) { return view('formacao_empresarial.lp1', ['preco' => $preco]);})->name('formacao_empresarial');
    Route::post('formacao_empresarial', [FormacaoEmpresarialController::class, 'form'])->name('formacao_empresarial_post');    
    Route::view('/vender-pela-internet', 'landign_pages.vendedor_online')->name('vendedor_online');

    //ROTAS PARA A ESTRATEGIA DE VAGAS DE EMPREGO
    Route::get('/aprenda-uma-profissao/{cidade?}/{whatsapp?}/{data?}', [LandingPageController::class, 'profissao']); //PÁGINA DO QUIZ
    Route::post('/enviar_formulario', [LandingPageController::class, 'enviar_formulario'])->name('enviar_formulario'); //ENVIAR FORMULARIO
    Route::post('/enviar_formulario_empresa', [LandingPageController::class, 'enviar_formulario_empresa'])->name('enviar_formulario_empresa'); //ENVIAR FORMULARIO
    


});

//MEUS HOTLINKS E ENCURTADOS
Route::get('/contabilidade', function () { return redirect('https://go.hotmart.com/P60800364B?&d=o50'); });
Route::get('/contabilidade-contato', function () { return redirect('https://wa.me/5511982671533?text=Quero%20tirar%20minhas%20d%C3%BAvidas%20sobre%20o%20curso%20AUXILIAR%20DE%20CONTABILIDADE'); });
Route::get('/contabilidade-checkout', function () { return redirect('https://go.hotmart.com/P60800364B?ap=a82b&offDiscount=50OFF'); });

/*Route::get('/home', function () {
    return view('auth.login'); // Redireciona para a URL externa
});*/

//COMBOS
Route::get('combo/{url}', [ComboController::class, 'pagina'])->name('combo.pagina');


//CADASTRO DE LEADS POR FORMULÁRIOS
Route::post('/cadastro_lead_whatsapp', [LeadsController::class, 'novo_lead'])->name('lead_whatsapp');  
Route::post('/curso_gratuito_lead', [LeadsController::class, 'curso_gratuito_lead'])->name('curso_gratuito_lead');  



/*SUPORTE AOS ALUNOS */
Route::prefix('alunos')->group(function () {
    Route::get('/suporte', [Alunos::class, 'suporte_alunos'])->name('suporte_alunos');
});

Route::get('/suporte', [Alunos::class, 'suporte_alunos']);
Route::view('/afiliados/termos', 'termos_e_politica.termos_uso')->name('afiliados_termos');
Route::view('/afiliados/politica', 'termos_e_politica.politica')->name('afiliados_politica'); 
 
/*ROTAS DOS USUARIOS |EXIGE EMAIL EMAIL AUTENTICADO */
Route::middleware(['auth', 'verified', 'minha_jornada'])->group(function () {
    Route::prefix('user')->group(function () {
        /**META API */
        Route::get('/auto_ads/auto_ads', [Meta_apiController::class, 'auto_ads'])->name('auto_ads');
        Route::post('/auto_ads/auto_ads/meta_accessToken', [Meta_apiController::class, 'accessToken'])->name('meta_accessToken');
        Route::get('/auto_ads/buscar_cidades', [Meta_apiController::class, 'buscarCidades'])->name('cidades.buscar');
        Route::post('/auto_ads/processar/cidades', [Meta_apiController::class, 'processarCidades'])->name('cidades.processar'); 
        /**META API */

        /* NOVOS LEADS */
        Route::get('/novos_leads', [LandingPageController::class, 'novos_leads'])->name('novos_leads');
        Route::post('/leads/{id}/arquivar', [LandingPageController::class, 'arquivar'])->name('leads.arquivar');
        Route::post('/leads/{lead}/desarquivar', [LandingPageController::class, 'desarquivar'])->name('leads.desarquivar');

        /* NOVOS LEADS */

        /**EVOLUTION WHATSAPP **/
        Route::get('/instance/index', [InstanceController::class, 'index'])->name('instance.index'); //PÁGINA DE CONEXÃO | VERIFICA STATUS E VERIFICA SE EXITE INSTANCIA, CASO NÃO CRIA AUTOMATICAMENTE
        Route::post('/instance/extrair_grupo', [InstanceController::class, 'extrair_leads_grupos'])->name('extrair_leads_grupos'); //EXTRAIR LEADS DE GRUPOS
        Route::post('/instance/create', [InstanceController::class, 'criar_instancia'])->name('criar_instancia'); //CRIAR UMA NOVA INSTANCIA
        Route::get('/instance/qrcode', [InstanceController::class, 'get_QRCode'])->name('instance.qrcode'); //RETORNA O QRCODE
        Route::get('/instance/reiniciar', [InstanceController::class, 'logoutInstance'])->name('instance.reiniciar'); //RETORNA O QRCODE
        Route::post('/zap_automatico/enviar_mensagem', [InstanceController::class, 'enviar_mensagem'])->name('zap_enviar_mensagem'); //FORMULÁRIO DE ENVIAR MENSAGEM
        /**EVOLUTION WHATSAPP **/
    
        //Route::view('/configurar_site', 'adm.configurar_site')->name('afiliado_configurar_site'); //MENU > MINHA ESTRUTURA > CONFIGURAÇÕES DO SITE 

        Route::view('/configurar_site2', 'dashboard.profile.settings')->name('afiliado_configurar_site'); //MENU > MINHA ESTRUTURA > CONFIGURAÇÕES DO SITE 
        
        Route::get('/afiliados_cadastrar_curso', [CursoController::class, 'afiliados_cadastrar_curso'])->name('cadastrar_cursos'); //MENU > MINHA ESTRUTURA > CURSOS
        
        Route::get('/leads', [LeadsController::class, 'painel_leads_afiliado'])->name('painel_leads_afiliado'); //NOVO PAINEL DE LEADS
        
        Route::get('/leads/hotmart_leads', [LeadsController::class, 'hotmart_leads'])->name('hotmart_leads'); //MENU > MEUS LEADS >> HOTMART  
        //Route::get('/leads/leads_purshase', [LeadsController::class, 'leads_purshase'])->name('leads_purshase'); //MENU > MEUS LEADS >> HOTMART 
        Route::get('/leads/portal_leads', [LeadsController::class, 'portal_leads'])->name('portal_leads'); //MENU > MEUS LEADS >> PORTAL
        
        Route::controller(UserController::class)->group(function () { //PAGINA INICIAL DA DASHBOARD
            Route::get('/parceiros', 'index')->name('parceiros');
            Route::get('/home', 'index')->name('home');
            Route::get('/dashboard', 'index')->name('dashboard');
            //Route::get('/dashboard_parceiros', 'index')->name('home'); 
        });

        Route::get('/ranking', [UserController::class, 'ranking'])->name('ranking');   

        Route::post('/leads/alterar_atendimento', [LeadsController::class, 'alterar_atendimento'])->name('alterar_atendimento'); //LEADS ALTERAR ATENDIMENTO
        Route::post('/dominio', [UserController::class, 'update_dominio'])->name('alterar_dominio'); //MENU > MINHA ESTRUTURA > MEU SITE || SUBMETENDO O FORMULÁRIO
        Route::post('/whatsapp_atendimento', [UserController::class, 'update_whatsapp_atendimento'])->name('alterar_whatsapp_atendimento'); //MENU > MINHA ESTRUTURA > WHATSAPP DE ATENDIMENTO || SUBMETENDO O FORMULÁRIO      
        Route::post('/afiliados_cadastrar_curso', [CursoController::class, 'afiliados_cadastrar_curso_ref'])->name('cadastrar_codigo_ref'); //MENU > MINHA ESTRUTURA > CURSOS || ENVIO DO FORMULÁRIO
        Route::post('/configurar_site', [UserController::class, 'afiliado_configurar_site'])->name('afiliado_configurar_site_post'); //MENU > MINHA ESTRUTURA > CONFIGURAÇÕES DO SITE || SUBMETENDO O FORMULÁRIO
        Route::post('/testar_pixel', [Meta_apiController::class, 'testar_pixel'])->name('afiliado_testar_pixel'); //MENU > MINHA ESTRUTURA > CONFIGURAÇÕES DO SITE || TESTAR PIXEL DO META FACEBOOK

        
        Route::post('/encurtar_link', [ShortenedUrlController::class, 'encurtar'])->name('encurtar_link');// Criar o link encurtado
        Route::view('/encurtar_link', 'dashboard.encurtadorlinks.create');// MOSTRAR A PÁGINA PARA CRIAR O LINK 
        Route::get('/encurtar_link_lista', [ShortenedUrlController::class, 'lista'])->name('encurtar_link_lista');// LISTTA link encurtado
        Route::delete('/encurtar_link/excluir/{id}', [ShortenedUrlController::class, 'excluir'])->name('encurtar_link_excluir');// EXCLUIR
        Route::get('/encurtar_link/editar/{id}', [ShortenedUrlController::class, 'editar_mostrar'])->name('encurtar_link_editar_mostrar');// Acessar a página de editar
        Route::post('/encurtar_link/editar/{id}', [ShortenedUrlController::class, 'editar'])->name('encurtar_link_editar_alterar');// POST: Editar Alterar

        Route::get('/exportar-tabela', function () {
            return Excel::download(new TabelaExport, 'tabela.xlsx');
        }); 
        
    });
});



/*ROTAS DO ADMINISTRADOR*/
Route::prefix('administrador')->group(function () {

    //COMBOS
    Route::get('combo/novo', [ComboController::class, 'novo'])->name('combo.novo');
    Route::post('combo/editar', [ComboController::class, 'editar'])->name('combo.editar');
    Route::get('combo/lista', [ComboController::class, 'index'])->name('combo.index');
    Route::get('combo/{id}/editar', [ComboController::class, 'editarForm'])->name('combo.editar_form');

    Route::get('administrador/combo/{combo}/cursos', [ComboController::class, 'formCursos'])->name('combo.cursos.form');
    Route::post('administrador/combo/{combo}/cursos', [ComboController::class, 'salvarCursos'])->name('combo.cursos.salvar');
    //Excluir o curso da tabela combo_cursos
    Route::delete('administrador/combo/{combo}/curso/{curso}', [ComboController::class, 'excluirCurso'])->name('combo.curso.excluir');



    Route::get('/cursos/cursos_lista', function () { //CURSOS
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }
        // Chame seu controlador ou lógica aqui
        return app(App\Http\Controllers\AdminController::class)->adm_cursos_lista();  
    })->name('adm_cursos_lista');

    Route::get('/dashboard', function () { //CURSOS
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }
        // Chame seu controlador ou lógica aqui
        return app(App\Http\Controllers\AdminController::class)->dashboard();
    })->name('dashboard_adm');

    //AULAS GRATUITAS
    Route::get('/aulas_gratuitas/cadastrar', function () { //CURSOS
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }
        // Chame seu controlador ou lógica aqui
        return app(App\Http\Controllers\CursoController::class)->aulas_gratuitas_cadastrar();
    })->name('aulas_gratuitas_cadastrar');

    Route::get('/aulas_gratuitas/lista', function () { //CURSOS
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }
        // Chame seu controlador ou lógica aqui
        return app(App\Http\Controllers\CursoController::class)->aulas_gratuitas_index();
    })->name('aulas_gratuitas_index');

    Route::delete('/aulas-demonstrativas/{id}', [CursoController::class, 'aulas_gratuitas_destroy'])->name('aulas_gratuitas_destroy');

    Route::post('/aulas_gratuitas/cadastrar/post', [CursoController::class, 'aulas_gratuitas_cadastrar_post'])->name('aulas_gratuitas_cadastrar_post');
    //FIM AULAS GRATUITAS


    Route::post('/cursos/update-order', [CursoController::class, 'updateOrder'])->name('adm_cursos_update_order');


    Route::get('/cursos/editar_curso/{id}', function ($id) {
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }
        // Chame seu controlador ou lógica aqui
        return app(App\Http\Controllers\AdminController::class)->adm_editar_curso($id);
    
    })->name('adm_editar_curso');

    Route::put('cursos/editar_curso_post/{id}', [AdminController::class, 'adm_editar_curso_post'])->name('adm_editar_curso_post');
    Route::post('/adm_cursos_lista_editar/{id?}', [AdminController::class, 'adm_cursos_lista_editar'])->name('adm_cursos_lista_editar');
    Route::get('curso_novo', [AdminController::class, 'create'])->name('cursos.novo');

    Route::get('leads/hotmart', [AdminController::class, 'leads_hotmart'])->name('leads.hotmart');
});

/*PIXEL DO FACEBOOK META ADS */
Route::get('meta_api/{evento?}', [Meta_apiController::class, 'evento'])->name('meta_api.evento.get');
Route::post('meta_api/{evento?}', [Meta_apiController::class, 'evento'])->name('meta_api.evento.post');

Route::post('meta_api_direto', [Meta_apiController::class, 'evento_direto'])->name('meta_api_direto');

//ROTAS DO JEMP.ME
Route::domain('jemp.me')->group(function () {
    Route::get('/{slug}', [ShortenedUrlController::class, 'redirecionar']);//  LINK ENCURTADO DO JEMP.ME
});

//ENCURTADOR DE LINK
Route::get('/e/{slug}', [ShortenedUrlController::class, 'redirecionar']);// LINK ENCURTADO DO DOMINIO PRINCIPAL

//AFILIADOS.JOVEMEMPREENDEDOR.ORG
Route::domain('afiliados.jovemempreendedor.org')->group(function () {
    Route::any('{any}', function () {
        return redirect('https://portalje.org/login'); // Redireciona para a rota nomeada 'login'
    })->where('any', '.*'); // O '.*' captura qualquer URI
});

//REDIRECIONAR DOMINIO/PARCEIROS
Route::get('/parceiros', function () {
    return view('parceiros'); // Redireciona para a URL externa
});

//REDIRECIONAMENTO DE LINKS COM COOKIE PARA AFILIADOS
Route::get('/redirect', [RedirectController::class, 'redirectWithUrl'])->name('redirectWithUrl'); 

/**EMAIL NOTIFICAÇÕES DE CADASTRO E REDEFINIÇÃO DE SENHA**/
Route::prefix('email')->group(function () {
    Route::get('/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // Marca o e-mail como verificado
        return redirect()->route('home')->with('message', 'E-mail verificado com sucesso!');
    })->middleware(['auth', 'signed'])->name('verification.verify');
    

    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});

//LOGIN DOS AFILIADOS
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


//LINK DE MANUTENÇÃO
Route::get('manutencao', [ManController::class, 'index'])->name('manutencao');

//VIAJANDINHO
Route::prefix('viajandinho')->group(function () {
    Route::get('/nova_exedicao', [ViajandinhoController::class, 'nova_expedicao'])->name('nova_expedicao');
    Route::get('/listar_exedicoes', [ViajandinhoController::class, 'listar_exedicoes'])->name('listar_exedicoes');
    Route::get('/listar_interessados', [ViajandinhoController::class, 'listar_interessados'])->name('listar_interessados');
    Route::get('/editar_exedicao/{id}', [ViajandinhoController::class, 'editar_exedicao'])->name('editar_exedicao');
    Route::put('/editar_exedicao_alterar', [ViajandinhoController::class, 'editar_exedicao_alterar'])->name('editar_exedicao_alterar');
    Route::post('/editar_exedicao_ativo', [ViajandinhoController::class, 'editar_exedicao_ativo'])->name('editar_exedicao_ativo');
});    

//PÁGINA W3
Route::get('/w3/{cidade?}', [Home_e_cursosController::class, 'carvalho_whatsapp'])->name('w3');

//ANTIGA HOME PAGE
//Route::get('/', [Home_e_cursosController::class, 'index'])->name('w3');

//PÁGINA D3
//Route::get('/d3/{cidade?}', [Home_e_cursosController::class, 'index']);

//HOME PAGE NOVA
Route::get('/cursos/{watsapp_curso?}/{cidade_desconto?}', [Home_e_cursosController::class, 'home'])->name('nova_home_cursos');
Route::get('/{watsapp_curso?}/{cidade_desconto?}', [Home_e_cursosController::class, 'home'])->name('nova_home');

Route::fallback(function () {
    return redirect()->route('nova_home'); // ou redirect('/')
});


//rotas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//CURSOS INDIVIDUAIS
//Route::get('/cursos/{curso?}/{cidade?}', [Home_e_cursosController::class, 'curso_individual'])->name('cursos_curso'); //CURSOS INDIVIDUAIS
//Route::get('/{curso?}/{cidade?}', [Home_e_cursosController::class, 'curso_individual'])->name('home_curso'); //CURSOS INDIVIDUAIS