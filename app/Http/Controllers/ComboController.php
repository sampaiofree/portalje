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
            $curso->bonus_titulos = $this->parseBonusTitles($curso->conteudo_bonus);
        }

        $comboResumo = $this->buildComboResumo($combo);
        $comboCheckoutUrl = $this->buildCheckoutUrl($combo->link_checkout);
        $comboLp = $this->buildComboLpContext();

        return view('novapagina_combo', compact('combo', 'comboResumo', 'comboCheckoutUrl', 'comboLp'));
    }

    private function buildComboResumo(Combo $combo): array
    {
        $uniqueAreas = [];
        $totalHours = 0;
        $courseNames = [];
        $moduleGroups = [];
        $bonusTitles = [];
        $heroImagePath = null;

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

            if ($heroImagePath === null) {
                $heroImagePath = $this->resolveHeroImagePath($curso);
            }

            $moduleTopics = [];
            $headline = trim(str_replace('"', '', (string) ($curso->headline ?? '')));
            if ($headline !== '') {
                $moduleTopics[] = $headline;
            }

            foreach ($curso->conteudo ?? [] as $conteudo) {
                $moduleTitle = trim((string) ($conteudo['title'] ?? ''));
                if ($moduleTitle !== '') {
                    $moduleTopics[] = $moduleTitle;
                }
            }

            $moduleTopics = array_values(array_unique(array_slice($moduleTopics, 0, 4)));

            $moduleGroups[] = [
                'title' => $titulo !== '' ? $titulo : 'Curso do combo',
                'topics' => $moduleTopics,
            ];

            foreach ($curso->bonus_titulos ?? [] as $bonusTitle) {
                $normalizedBonus = function_exists('mb_strtolower')
                    ? mb_strtolower($bonusTitle, 'UTF-8')
                    : strtolower($bonusTitle);

                if (!array_key_exists($normalizedBonus, $bonusTitles)) {
                    $bonusTitles[$normalizedBonus] = $bonusTitle;
                }
            }
        }

        return [
            'course_count' => $combo->cursos->count(),
            'total_hours' => $totalHours,
            'has_total_hours' => $totalHours > 0,
            'unique_areas' => array_values($uniqueAreas),
            'course_names' => $courseNames,
            'module_groups' => $moduleGroups,
            'bonus_titles' => array_values($bonusTitles),
            'hero_image_path' => $heroImagePath,
        ];
    }

    private function buildComboLpContext(): array
    {
        $portalData = $this->rootDomainCoursePublicStateService->portalData();
        $pixelIds = array_values(array_unique(array_filter([
            env('META_PIXEL_ID_PRIMARY', '419961365827965'),
            env('META_PIXEL_ID_SECONDARY', '948808649224691'),
        ])));

        return [
            'company_name' => 'Programa Jovem Empreendedor',
            'logo_padrao_url' => asset('img/home_page/logojecolor.webp'),
            'logo_dark_url' => asset('img/home_page/logowhite.png'),
            'whatsapp_atendimento' => (string) ($portalData['telefone_suporte_alunos'] ?? '5511982671533'),
            'pixel_ids' => $pixelIds,
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

    private function parseBonusTitles(?string $html): array
    {
        if (empty($html)) {
            return [];
        }

        $conteudos = $this->lista_conteudo($html) ?? [];

        $titles = [];
        foreach ($conteudos as $conteudo) {
            $title = trim((string) ($conteudo['title'] ?? ''));
            if ($title !== '') {
                $titles[] = $title;
            }
        }

        return array_values(array_unique($titles));
    }

    private function resolveHeroImagePath(object $curso): ?string
    {
        foreach (['capa_horizontal', 'capa_vertical', 'capa_quadrada'] as $field) {
            $path = trim((string) ($curso->{$field} ?? ''));
            if ($path !== '') {
                return $path;
            }
        }

        return null;
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
