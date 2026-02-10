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
    //HOME PAGE
    public function man()
    {

        $curso = Curso::find(3);

        $conteudo = new Home_e_cursosController;
        
        $conteudo_principal = $conteudo->lista_conteudo($curso->conteudo_principal);
        
        $resultado = "";
        foreach ($conteudo_principal as $topico) {
            // Garanta que o título esteja em UTF-8
            $titulo = mb_convert_encoding($topico['title'], 'UTF-8', 'auto');
            $resultado .= "* " . $titulo . "\n";
        }
        
        // Define o cabeçalho HTTP para UTF-8
        header('Content-Type: application/json; charset=utf-8');
        
        // Retorna o resultado em JSON, garantindo que esteja em UTF-8
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        //dd($resultado); exit;
        
    }

    
    public function post_worpress(){
        // Dados do seu site WordPress
        $wp_site = 'https://3f7.org/wp-json/wp/v2/posts';
        $usuario = 'sampaio.free';
        $senha_app = 'YSYm WNNG FwdM 1JhV edQt 1pfN'; // Application Password

        // Conteúdo do post
        $dados = [
            'title'   => 'Você não vai acreditar no que essa proteína faz com seu corpo',
            'content' => '<h1>Você não vai acreditar no que essa proteína faz com seu corpo</h1>
                A proteína é um dos nutrientes mais importantes para o funcionamento do nosso organismo. Seja na construção muscular ou na regeneração celular, ela exerce um papel vital na manutenção da saúde e no bem-estar. Neste artigo, vamos explorar de forma clara e acessível os benefícios dessa proteína, mostrando como ela pode ser uma aliada indispensável para quem busca qualidade de vida.

                A cada dia, novas pesquisas revelam funções surpreendentes dos diferentes tipos de proteína, sejam elas provenientes de fontes animais ou vegetais. Se você está interessado em emagrecer, ganhar massa muscular ou até mesmo investir na sua saúde para evitar problemas futuros, conhecer os efeitos dessa proteína pode transformar sua rotina.

                <h2>O Papel Fundamental da Proteína em Nosso Corpo</h2>
                A proteína é essencial para a construção e reparação dos tecidos. Sem ela, nosso corpo não conseguiria se manter saudável ou se recuperar de lesões. Além disso, as proteínas atuam como enzimas e hormônios, regulando diversos processos metabólicos e ajudando a transformar os alimentos em energia.

                Muitos estudos apontam que o consumo adequado de proteína pode melhorar a função imunológica e acelerar a recuperação após atividades físicas intensas. Por isso, atletas e entusiastas do fitness investem em fontes de proteína de alta qualidade, como o whey protein, para potencializar o ganho muscular e promover a regeneração dos músculos.

                <h2>Proteína e Emagrecimento: Uma Combinação Eficaz</h2>
                Uma das grandes vantagens do consumo de proteína é seu efeito termogênico. Isso significa que, durante a digestão, o corpo gasta uma quantidade significativa de energia para metabolizá-la, contribuindo para o emagrecimento. Incorporar fontes proteicas em sua dieta pode ajudar a controlar o apetite, manter a saciedade por mais tempo e reduzir os picos de fome.

                Além disso, quando associada à prática regular de exercícios, a proteína facilita a preservação da massa magra mesmo durante períodos de redução calórica. Essa característica torna a proteína uma escolha inteligente para quem busca um emagrecimento saudável e sustentável, sem perder a qualidade muscular.

                <h2>Ação da Proteína no Metabolismo e na Energia</h2>
                Outra função surpreendente da proteína é sua capacidade de atuar no metabolismo. Diferentemente dos carboidratos e das gorduras, que servem como fontes primárias de energia, a proteína estimula a taxa metabólica basal. Isso auxilia no gasto calórico mesmo em repouso, contribuindo para a manutenção do peso corporal.

                Para quem pensa em viajar barato ou reduzir custos com alimentação fora de casa, apostar em alimentos ricos em proteína pode ser uma estratégia viável. Preparar refeições balanceadas com fontes como ovos, peixes, leguminosas e carnes magras garante um aporte nutricional de qualidade, sem a necessidade de recorrer constantemente a suplementos caros.

                <h2>Os Benefícios para a Massa Muscular e Recuperação</h2>
                Diversos estudos comprovam que a ingestão proteica adequada é crucial para o ganho de massa muscular, especialmente quando combinada com atividades físicas de resistência. Essa proteína ajuda a reparar as microlesões causadas durante os treinos, promovendo uma recuperação mais rápida e eficaz.

                A suplementação com proteínas, como o whey protein, se destaca principalmente nesse contexto. Ela tem alta biodisponibilidade, facilitando a absorção e permitindo que os músculos recebam os nutrientes necessários para sua regeneração. Assim, atletas e pessoas engajadas em programas de treinamento veem resultados significativos tanto no fortalecimento muscular quanto na melhora do desempenho.

                <h2>Proteína e Finanças Pessoais: Investindo na Saúde</h2>
                Embora o investimento financeiro em suplementos possa parecer alto à primeira vista, a prioridade deve ser sempre a sua saúde. Quando pensamos em finanças pessoais, investir em uma alimentação rica em nutrientes é fundamental para evitar custos futuros com tratamentos médicos e complicações de saúde.

                Optar por fontes de proteína de qualidade, seja por meio de alimentos naturais ou suplementos, é uma forma de garantir que seu corpo funcione de maneira otimizada. Essa escolha preventiva pode evitar gastos elevados com problemas de saúde, promovendo um equilíbrio financeiro a longo prazo.

                <h2>Dicas para Escolher a Melhores Fontes de Proteína</h2>
                Varie suas fontes: Inclua tanto proteínas animais quanto vegetais em sua dieta para obter um espectro completo de aminoácidos.
                Leia os rótulos: Ao optar por suplementos, confira a concentração de proteína e evite aqueles com muitas adições artificiais.
                Combine com outros nutrientes: A proteína age de forma mais eficaz quando associada a uma dieta equilibrada, com carboidratos complexos e gorduras saudáveis.
                Consulte um especialista: Um nutricionista pode ajudar a definir a quantidade ideal de proteína para o seu perfil e suas metas, seja para emagrecer ou ganhar massa muscular.
                <h2>Cuidados e Considerações ao Aumentar o Consumo de Proteína</h2>
                Apesar dos inúmeros benefícios, o consumo exagerado de proteína pode trazer alguns riscos. Excesso pode sobrecarregar os rins e levar a problemas de digestão. Por isso, é fundamental encontrar um equilíbrio, adequando a quantidade de proteína às necessidades individuais.

                Para os que têm condições de saúde pré-existentes, uma avaliação médica é recomendada antes de realizar mudanças significativas na dieta. Investir em saúde é sempre uma decisão inteligente, mas é importante priorizar a segurança e o bem-estar.

                <h2>Conclusão: Transforme sua Saúde com a Proteína Certa</h2>
                O que essa proteína faz com seu corpo vai muito além da construção muscular. Ela desempenha um papel integral em diversos processos vitais, desde a recuperação pós-treino até o controle do metabolismo e a promoção do emagrecimento. Ao entender seus benefícios e saber escolher as melhores fontes, você pode transformar sua rotina alimentar e investir na sua saúde de maneira consciente.

                Se você busca melhorias reais na qualidade de vida e quer evitar problemas futuros, considere aumentar o consumo de proteína de forma equilibrada. Seja para conquistar a tão desejada massa muscular, reduzir medidas ou melhorar a performance nos treinos, a proteína é uma aliada poderosa e surpreendente. Invista em uma rotina alimentar mais rica e sinta os efeitos positivos no seu corpo e bem-estar.

                Com essas informações, fica claro que os efeitos da proteína podem ser verdadeiramente transformadores. Experimente ajustar sua dieta com o acompanhamento de profissionais e descubra como essa simples mudança pode impactar positivamente sua saúde, seu desempenho e até suas finanças pessoais no longo prazo.',
            'status'  => 'publish', // ou 'draft' se quiser como rascunho
        ];

        // Enviar POST para o WordPress
        $resposta = Http::withBasicAuth($usuario, $senha_app)
            ->post($wp_site, $dados);

        // Verifica o resultado
        if ($resposta->successful()) {
            return response()->json(['sucesso' => true, 'post' => $resposta->json()]);
        } else {
            return response()->json(['erro' => true, 'detalhes' => $resposta->body()], 400);
        }
    }

   public function post_imagem_wordpress(){
    $imageUrl = 'https://oaidalleapiprodscus.blob.core.windows.net/private/org-uFE8mS3TsgMhjHCHa8CVHstG/user-rKn4pzhq9hx6BsO7r4FnKrAp/img-bNMoXWz5Q4SWd3kCGNcFis54.png?st=2025-06-14T21%3A53%3A33Z&se=2025-06-14T23%3A53%3A33Z&sp=r&sv=2024-08-04&sr=b&rscd=inline&rsct=image/png&skoid=cc612491-d948-4d2e-9821-2683df3719f5&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2025-06-14T08%3A48%3A43Z&ske=2025-06-15T08%3A48%3A43Z&sks=b&skv=2024-08-04&sig=tJk2%2Bqq7owAyqgj6fwdX5LPRywnjB6rNuvKujLYIrnQ%3D'; // URL da imagem gerada
    $context = stream_context_create([
        "http" => [
            "header" => "User-Agent: LaravelBot/1.0\r\n"
        ]
    ]);

    $originalContents = file_get_contents($imageUrl);
    $sourceImage = imagecreatefromstring($originalContents);


    // Caminho local
    $imageName = 'capa_' . time() . '.webp';
    $localPath = storage_path("app/$imageName");

    // Salvar como WEBP (qualidade 80%)
    imagewebp($sourceImage, $localPath, 80);
    imagedestroy($sourceImage);


    $auth = base64_encode('sampaio.free:YSYmWNNGFwdM1JhVedQt1pfN');

    $uploadResponse = Http::withHeaders([
        'Authorization' => "Basic $auth",
        'Content-Disposition' => 'attachment; filename="' . $imageName . '"',
        //'Content-Type' => $mime,
    ])->attach(
        'file',
        file_get_contents($localPath),
        $imageName
    )->post('https://3f7.org/wp-json/wp/v2/media');

    //dd($uploadResponse->json());

    $mediaId = $uploadResponse['id'];

    $postId = 7;

    Http::withHeaders([
        'Authorization' => "Basic $auth",
        'Content-Type' => 'application/json',
    ])->put("https://3f7.org/wp-json/wp/v2/posts/$postId", [
        'featured_media' => $mediaId,
    ]);
}


    public function gerar_inagem(){

    }

    public function catalogoMeta($desconto=null)
    {
        $produtos = $curso = Curso::orderBy('gratuito', 'desc')
                                ->orderBy('ordem')
                                ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= "<channel>\n";
        $xml .= "<title>Cursos Profissionalizantes Portal Jovem Empreendedor</title>\n";
        $xml .= "<link>https://ead.portalje.org/</link>\n";
        $xml .= "<description>CURSOS PROFISSIONALIZANTES em mais de 40 áreas. Escolha seu curso e comece ainda hoje!</description>\n";

        foreach ($produtos as $p) {
            if($p['publicado'] AND $p['link_checkout_completo']){
            $xml .= "<item>\n";
            $xml .= "<g:id>{$p['id']}{$desconto}</g:id>\n";
            $xml .= "<title>{$p['titulo']}</title>\n";
            $xml .= "<description>{$p['headline']}</description>\n";
            $xml .= "<link>https://ead.portalje.org/{$p['url']}/{$desconto}</link>\n";
            $xml .= "<g:image_link>https://ead.portalje.org/storage/{$p['capa_quadrada']}</g:image_link>\n";
            $xml .= "<g:availability>in stock</g:availability>\n";
            $xml .= "<g:condition>new</g:condition>\n";
            $xml .= "<g:price>39.40 BRL</g:price>\n";
            $xml .= "</item>\n";
            }
        }

        $xml .= "</channel>\n";
        $xml .= "</rss>";

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

public function leads() 
{
    $leads = Lead::get();

    // Cria uma lista com os telefones dos leads sem o DDI
    $telefonesSemDdi = $leads->map(function ($lead) {
        return substr($lead->whatsapp, 2); // remove o 55
    })->filter()->unique()->values()->all(); // remove duplicados

    // Busca só os eventos com telefones compatíveis
    $events = PurchaseEvent::whereIn('buyer_checkout_phone', $telefonesSemDdi)->get();

    // Indexa por telefone para consulta rápida
    $eventsPorTelefone = $events->groupBy('buyer_checkout_phone');

    header("Content-Type: text/html; charset=UTF-8");
    echo "<h1>Lista de Leads e Compras</h1>";

    foreach ($leads as $lead) {
        $telefone = substr($lead->whatsapp, 2);
        $compras = $eventsPorTelefone->get($telefone, collect());

        
        echo "<hr>";
        
        echo "<h3>Lead:</h3>";
        echo "Data: {$lead->created_at}<br>";
        echo "Nome: {$lead->nome}<br>";
        echo "WhatsApp: {$lead->whatsapp}<br>";
        echo "Evento Portal: {$lead->evento_portal}<br>";
        echo "curso: {$lead->curso}<br>";

        echo "<h4>Compras:</h4>";
        if ($compras->isEmpty()) {
            echo "Nenhuma compra encontrada.<br>";
        } else {
            foreach ($compras as $p) {
                echo "<div style='margin-left:20px;'>";
                echo "Data: {$p->created_at}<br>";
                echo "purchase_status: {$p->purchase_status}<br>";
                echo "Nome Produto: {$p->product_name}<br>";
                echo "Nome Comprador: {$p->buyer_name}<br>";
                echo "Telefone: {$p->buyer_checkout_phone}<br>";
                echo "Documento: {$p->buyer_document}<br>";
                echo "</div><br>";
            }
        }
    }
}




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