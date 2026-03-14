<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Models\Cidades;
use App\Models\Cupom;
use App\Models\RootDomainCourseConfig;
use App\Services\RootDomainCoursePublicStateService;

class ConsultarController extends Controller
{
    private RootDomainCoursePublicStateService $rootDomainCoursePublicStateService;

    public function __construct(RootDomainCoursePublicStateService $rootDomainCoursePublicStateService)
    {
        $this->rootDomainCoursePublicStateService = $rootDomainCoursePublicStateService;
    }

    public function consultarAluno($nomeCampo, $conteudoCampo)
    {
        if($conteudoCampo=="03125257107"){
            echo "Aluno do curdo de Auxiliar Veterinário";
            die();
        }
        if ($nomeCampo == "cpf") $nomeCampo = "buyer_document";
        if ($nomeCampo == "email") $nomeCampo = "buyer_email";
        if ($nomeCampo == "telefone") $nomeCampo = "buyer_checkout_phone";

        $alunos = PurchaseEvent::where($nomeCampo, 'like', '%' . $conteudoCampo . '%')->get();

        $alunos = PurchaseEvent::where($nomeCampo, $conteudoCampo)->get();

        // 🔹 Retornar os resultados formatados em Markdown
        if ($alunos->isEmpty()) {
            return "### ❌ Nenhum aluno encontrado para **{$nomeCampo} = {$conteudoCampo}**";
        }

        $markdown = "## 🔍 Resultado da consulta de alunos\n\n";
        foreach ($alunos as $aluno) {
            $markdown .= "**Nome:** {$aluno->buyer_name}\n";
            $markdown .= "- **Email:** {$aluno->buyer_email}\n";
            $markdown .= "- **Telefone:** {$aluno->buyer_checkout_phone}\n";
            $markdown .= "- **Documento:** {$aluno->buyer_document}\n";
            $markdown .= "- **Curso:** {$aluno->product_name}\n";
            $markdown .= "- **Status - Transação:** {$aluno->purchase_status}\n";
            $markdown .= "- **Data da transação:** {$aluno->purchase_date}\n\n";
            $markdown .= "---\n\n";
        }

        return $markdown;
    }

    public function consultarCurso($id = null)
    {
        $query = Curso::query();

        if ($id) {
            $query->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('codigo_id_hotmart', $id);
            });
        } else {
            $query->where(function ($q) {
                $q->where('publicado', '1')
                  ->orWhere('publicado', 'on');
            });
        }

        $cursos = $query->get();

        if (!$id) {
            $configsByCourseId = $this->rootDomainCoursePublicStateService->configsByCourseId();
            $cursos = $cursos
                ->filter(fn (Curso $curso) => $this->rootDomainCoursePublicStateService->isVisibleForCourse(
                    $curso,
                    $configsByCourseId->get($curso->id)
                ))
                ->values();
        }

        if ($cursos->isEmpty()) {
            return "### ❌ Nenhum curso encontrado.";
        }

        $markdown = "## 🎓 Lista de Cursos\n\n";

        foreach ($cursos as $curso) {
            $markdown .= $this->buildCursoMarkdown($curso);
            $markdown .= "---\n\n";
        }

        return $markdown;
    }

    public function gancho()
    {
        $query = Curso::query();

        

        $cursos = $query->get();

        if ($cursos->isEmpty()) {
            return "### ❌ Nenhum curso encontrado.";
        }

        $markdown = "";

        foreach ($cursos as $curso) {
            $markdown .= "O que faz um {$curso->titulo}, qual o salário médio no Brasil e onde esse profissional pode trabalhar  <br>";
        }

        echo "<pre>";
        print_r($markdown);
        echo "</pre>";
    }
   public function consultarNovaCidade(Request $request){
    
    $dados = $request->input('cidades_existentes', []);

    //return response()->json($dados);

    $query = Cidades::query();;

    foreach ($dados as $estado => $cidades) {
        $query->orWhere(function ($q) use ($estado, $cidades) {
            $q->where('estado', $estado)
              ->whereNotIn('cidade', $cidades);
        });
    }

    $proximaCidade = $query->orderBy('estado')->orderBy('cidade')->first();

    $return = $proximaCidade;

    return response()->json($return);

   }

    private function buildCursoMarkdown(Curso $curso): string
    {
        $state = $this->rootDomainCoursePublicStateService->resolveEffectivePublicStateForCourse($curso);
        /** @var \App\Models\Curso $effectiveCourse */
        $effectiveCourse = $state['course'];
        $pricing = $state['pricing'];
        $config = $state['config'];
        $visible = (bool) ($state['visible'] ?? false);
        $currentMode = (string) ($effectiveCourse->modo_precos ?? 'padrao');
        $basicOfferKey = $pricing['current_basic_offer_key'] ?? null;
        $completeOfferKey = $pricing['current_complete_offer_key'] ?? 'completo_padrao';
        $offerVariants = collect($pricing['offer_variants'] ?? []);
        $completeOffer = $offerVariants->get($completeOfferKey, []);
        $basicOffer = $basicOfferKey ? $offerVariants->get($basicOfferKey, []) : null;
        $countdown = $pricing['countdown'] ?? [];

        $markdown = "### {$curso->titulo}\n";
        $markdown .= "- **ID:** {$curso->id}\n";
        $markdown .= "- **Professor:** {$curso->professor_nome}\n";
        $markdown .= "- **Carga horária:** {$curso->horas_completo} horas\n";
        $markdown .= "- **Avaliação:** {$curso->nota_avaliacao}\n";
        $markdown .= "- **Área de atuação:** {$curso->areas_de_atuacao}\n";
        $markdown .= "- **Visibilidade nos domínios raiz:** " . ($visible ? 'Visível' : 'Oculto') . "\n";
        $markdown .= "- **Formulário antes do checkout:** " . ($effectiveCourse->formulario ? 'Sim' : 'Não') . "\n";
        $markdown .= "- **Modo de preços:** {$currentMode}\n";
        $markdown .= "- **Página do curso:** https://jovemempreendedor.org/{$curso->url}\n";
        $markdown .= "- **Aulas gratuitas:** https://jovemempreendedor.org/{$curso->url}?g=1\n";
        $markdown .= "- **Plano completo:** {$this->formatOfferLine($completeOffer)}\n";
        $markdown .= "- **Pagamento plano completo:** " . ($completeOffer['checkout_url'] ?? ($effectiveCourse->link_checkout_completo ?? '#')) . "\n";

        if (is_array($basicOffer) && !empty($basicOffer)) {
            $markdown .= "- **Plano básico:** {$this->formatOfferLine($basicOffer)}\n";
            $markdown .= "- **Pagamento plano básico:** " . ($basicOffer['checkout_url'] ?? ($effectiveCourse->link_checkout_basico ?? '#')) . "\n";
        }

        if (!empty($countdown['enabled'])) {
            $markdown .= "- **Contador:** " . $this->formatCountdownLine($countdown) . "\n";
        }

        if ($config instanceof RootDomainCourseConfig) {
            $markdown .= "- **Configuração raiz personalizada:** Sim\n";
        }

        $markdown .= "- **Área de Membros:** {$curso->link_area_membros}\n";
        $markdown .= "- **Vídeo de apresentação:** https://youtube.com/watch?v={$curso->video_apresentacao}\n";
        $markdown .= "- **Por dentro do curso:** https://youtube.com/watch?v={$curso->video_dentro_do_curso}\n\n";

        return $markdown;
    }

    private function formatOfferLine(array $offer): string
    {
        $price = (string) ($offer['price_value'] ?? 'Consulte');
        $cashValue = (string) ($offer['cash_value'] ?? 'Consulte');

        if (!empty($offer['show_cash_line'])) {
            return $price . " (ou {$cashValue} à vista no PIX)";
        }

        return $price;
    }

    private function formatCountdownLine(array $countdown): string
    {
        $minutes = (int) ($countdown['minutes'] ?? 0);
        $minuteLabel = $minutes === 1 ? '1 minuto' : "{$minutes} minutos";
        $action = (string) ($countdown['action'] ?? 'nada');
        $line = $minuteLabel . " | ação: {$action}";

        if ($action === 'alterar_preco' && !empty($countdown['destination_offer'])) {
            $line .= ' | destino: ' . $this->describeDestinationOffer((string) $countdown['destination_offer']);
        }

        return $line;
    }

    private function describeDestinationOffer(string $destinationOffer): string
    {
        if ($destinationOffer === 'completo_padrao') {
            return 'Plano completo padrão';
        }

        if ($destinationOffer === 'basico_padrao') {
            return 'Plano básico padrão';
        }

        if (preg_match('/^(completo|basico)_cupom:(\d+)$/', $destinationOffer, $matches) !== 1) {
            return $destinationOffer;
        }

        $planLabel = $matches[1] === 'basico' ? 'Plano básico' : 'Plano completo';
        $cupom = Cupom::query()->find((int) $matches[2]);
        $couponLabel = $cupom?->codigo ?: ('#' . $matches[2]);

        return "{$planLabel} com cupom {$couponLabel}";
    }
}
