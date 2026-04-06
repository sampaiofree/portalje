<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Services\RootDomainCoursePublicStateService;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function __construct(
        private readonly RootDomainCoursePublicStateService $rootDomainCoursePublicStateService
    ) {
    }

    public function novo()
    {
        // Cria registro em branco com valores padrao.
        $combo = new Combo();
        $combo->titulo = '';
        $combo->headline = '';
        $combo->descricao_curta = '';
        $combo->url = '';
        $combo->link_checkout = '';
        $combo->preco_parcelado = 0;
        $combo->preco = 0;
        $combo->save();

        return view('adm.combos.criar_editar', compact('combo'));
    }

    public function editar(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:combos,id',
            'titulo' => 'nullable|string',
            'headline' => 'nullable|string',
            'descricao_curta' => 'nullable|string',
            'url' => 'nullable|string',
            'link_checkout' => 'nullable|string',
            'link_checkout_completo' => 'nullable|string',
            'preco_parcelado' => 'nullable|string',
            'preco' => 'nullable|string',
        ]);

        $combo = Combo::findOrFail($validated['id']);
        $combo->update($validated);

        return $this->index();
    }

    public function index()
    {
        $combos = Combo::all();

        return view('adm.combos.index', compact('combos'));
    }

    public function editarForm($id)
    {
        $combo = Combo::findOrFail($id);

        return view('adm.combos.criar_editar', compact('combo'));
    }

    public function formCursos($comboId)
    {
        $combo = Combo::findOrFail($comboId);
        $cursos = \App\Models\Curso::all();
        $selected = \App\Models\ComboCurso::where('id_combo', $combo->id)->pluck('id_curso')->toArray();

        return view('adm.combos.cursos', compact('combo', 'cursos', 'selected'));
    }

    public function salvarCursos(Request $request, $comboId)
    {
        $combo = Combo::findOrFail($comboId);
        $cursosSelecionados = $request->input('cursos', []);

        \App\Models\ComboCurso::where('id_combo', $combo->id)->delete();

        foreach ($cursosSelecionados as $cursoId) {
            \App\Models\ComboCurso::create([
                'id_combo' => $combo->id,
                'id_curso' => $cursoId,
            ]);
        }

        return redirect()->route('combo.index')
            ->with('success', 'Cursos atualizados!');
    }

    public function excluirCurso($comboId, $cursoId)
    {
        $combo = Combo::findOrFail($comboId);
        $combo->cursos()->detach($cursoId);

        return redirect()->back()->with('success', 'Curso removido do combo com sucesso!');
    }

    public function pagina($url)
    {
        $combo = Combo::with([
            'cursos' => static function ($query) {
                $query->orderBy('ordem')->orderBy('id');
            },
        ])->where('url', $url)->firstOrFail();

        foreach ($combo->cursos as $curso) {
            $curso->conteudo = !empty($curso->conteudo_principal)
                ? $this->lista_conteudo($curso->conteudo_principal)
                : [];
            $curso->areas_de_atuacao_lista = $this->parseAreas($curso->areas_de_atuacao);
        }

        $comboResumo = $this->buildComboResumo($combo);
        $pagina = $this->buildPaginaPublica($combo);
        $comboCheckoutUrl = $this->buildCheckoutUrl($combo->link_checkout);

        return view('home_e_cursos.combo_individual', compact('combo', 'comboResumo', 'pagina', 'comboCheckoutUrl'));
    }

    private function buildComboResumo(Combo $combo): array
    {
        $uniqueAreas = [];
        $totalHours = 0;
        $courseNames = [];

        foreach ($combo->cursos as $curso) {
            $titulo = trim((string) $curso->titulo);
            if ($titulo !== '') {
                $courseNames[] = $titulo;
            }

            $horas = (int) ($curso->horas_completo ?? 0);
            if ($horas > 0) {
                $totalHours += $horas;
            }

            foreach ($curso->areas_de_atuacao_lista ?? [] as $area) {
                $normalizedArea = function_exists('mb_strtolower')
                    ? mb_strtolower($area, 'UTF-8')
                    : strtolower($area);

                if (!array_key_exists($normalizedArea, $uniqueAreas)) {
                    $uniqueAreas[$normalizedArea] = $area;
                }
            }
        }

        return [
            'course_count' => $combo->cursos->count(),
            'total_hours' => $totalHours,
            'has_total_hours' => $totalHours > 0,
            'unique_areas' => array_values($uniqueAreas),
            'course_names' => $courseNames,
        ];
    }

    private function buildPaginaPublica(Combo $combo): array
    {
        $portalData = $this->rootDomainCoursePublicStateService->portalData();
        $companyName = 'Programa Jovem Empreendedor';
        $logoPadraoUrl = asset('img/home_page/logojecolor.webp');
        $logoDarkUrl = asset('img/home_page/logowhite.png');
        $whatsappAtendimento = (string) ($portalData['telefone_suporte_alunos'] ?? '5511982671533');
        $comboTitle = trim((string) $combo->titulo);
        $comboContext = $comboTitle !== '' ? "sobre o combo de {$comboTitle}" : "sobre os combos do {$companyName}";
        $imgBotaoWhatsapp = asset('img/home_page/whatsapp.gif');
        $whatsappMessage = rawurlencode("Ola quero saber {$comboContext}");

        return [
            'headline' => $comboTitle !== '' ? $comboTitle : 'Combo de cursos profissionalizantes',
            'headline_sub' => trim((string) $combo->headline) !== ''
                ? (string) $combo->headline
                : 'Conheca os cursos incluidos e comece sua qualificacao hoje.',
            'headline_botao' => 'Conhecer o combo',
            'whatsapp_atendimento_tempo' => (int) ($portalData['whatsapp_atendimento_tempo'] ?? 0),
            'whatsapp' => $whatsappAtendimento,
            'whatsapp_atendimento_id' => null,
            'whatsapp_float_atendimento' => $whatsappAtendimento,
            'whatsapp_float_atendimento_id' => null,
            'whatsapp_mostrar' => true,
            'formulario_whatsapp' => (bool) ($portalData['formulario_whatsapp'] ?? true),
            'formulario_pre_checkout' => (bool) ($portalData['formulario_pre_checkout'] ?? true),
            'botao_whatsapp_flutuante' => "<a id=\"whatsapp_botao\" href=\"https://api.whatsapp.com/send/?phone={$whatsappAtendimento}&text={$whatsappMessage}\" target=\"_blank\" class=\"jump bg-success rounded-circle d-flex justify-content-center align-items-center position-fixed bottom-0 end-0 m-3\" style=\"width: 70px; height: 70px; z-index: 9; visibility:hidden;\"><img alt='Portal Jovem Empreendedor' loading='lazy' src=\"{$imgBotaoWhatsapp}\" width='70' height='70'></a>",
            'form_lead_titulo' => 'Para receber mais informacoes sobre o combo, preencha o formulario abaixo.',
            'form_lead_botao' => 'Continuar',
            'pidel_id' => null,
            'company_name' => $companyName,
            'logo_padrao_url' => $logoPadraoUrl,
            'logo_dark_url' => $logoDarkUrl,
        ];
    }

    private function buildCheckoutUrl(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '#planos';
        }

        $fragment = '';
        $hashPosition = strpos($url, '#');
        if ($hashPosition !== false) {
            $fragment = substr($url, $hashPosition);
            $url = substr($url, 0, $hashPosition);
        }

        [$base, $queryString] = array_pad(explode('?', $url, 2), 2, '');
        parse_str($queryString, $queryParameters);
        $queryParameters['sck'] = 'plano_completo';

        $rebuiltQuery = http_build_query($queryParameters);

        return $base . ($rebuiltQuery !== '' ? '?' . $rebuiltQuery : '') . $fragment;
    }

    private function parseAreas(?string $areas): array
    {
        $areas = trim((string) $areas);
        if ($areas === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($area) => trim((string) $area),
            explode('/', $areas)
        )));
    }

    public function lista_conteudo($html)
    {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if (!$html) {
            return null;
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $listItems = $dom->getElementsByTagName('li');

        $courses = [];
        $currentCourse = null;

        foreach ($listItems as $item) {
            $text = $item->textContent;
            $class = $item->getAttribute('class');

            if (strpos($class, 'ql-indent-1') === false) {
                if ($currentCourse) {
                    $courses[] = $currentCourse;
                }

                $currentCourse = ['title' => $text, 'topics' => []];
            } else {
                $currentCourse['topics'][] = $text;
            }
        }

        if ($currentCourse) {
            $courses[] = $currentCourse;
        }

        return $courses;
    }
}
