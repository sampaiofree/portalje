<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Curso;
use App\Models\PurchaseEvent;

class AdminController extends Controller
{

    public function dashboard(){
        $afiliados = User::getMonthlyRegistrationsLastSixMonths();
    
        // Extrair os dados do mês, total e total com domínio
        $meses = [];
        $totalCadastros = [];
        $totalComDominio = [];

        $tot_cadastros = 0;
        $tot_dominio = 0;
    
        foreach ($afiliados as $afiliado) {
            $meses[] = "{$afiliado['month']}/{$afiliado['year']}";
            $totalCadastros[] = $afiliado['total'];
            $totalComDominio[] = $afiliado['total_with_dominio'];

            $tot_cadastros+=$afiliado['total'];
            $tot_dominio += $afiliado['total_with_dominio'];
        }

        $aproveitamento = $tot_cadastros > 0
            ? number_format(($tot_dominio / $tot_cadastros) * 100, 2, ",", "")
            : "0,00";

        $afiliados = [
            "tot_cadastros" =>  $tot_cadastros,
            "tot_dominio" => $tot_dominio,
            "aproveitamento" => $aproveitamento
        ];
    
        return view('adm.dashboard_adm', compact('meses', 'totalCadastros', 'totalComDominio', 'afiliados'));
    }

    public function adm_cursos_lista() 
    {
        $cursos = Curso::orderBy('ordem')->orderBy('id')->get();
        return view('adm.cursos.cursos_lista', compact('cursos'));
    }
    

    public function adm_editar_curso($id) {
        $curso = Curso::where('id', $id)->first();
        if($curso){
            return view('adm.cursos.editar_curso', compact('curso'));
        }
    }

    public function create()
    {
        $timestamp = now()->format('YmdHis');
        $tempUrl = 'curso-temp-' . $timestamp . '-' . substr((string) microtime(true), -6);

        $curso = Curso::create([
            'titulo' => 'Novo curso',
            'url' => $tempUrl,
        ]);

        // Obtém o ID do registro recém-criado
        $newCursoId = $curso->id;
        return redirect()->route('adm_editar_curso', ['id' => $newCursoId]);
    }

    public function adm_cursos_lista_editar(Request $request, $id=null){

        $dados = $request->except('_token', '_method');

        //echo json_encode($id);
        //exit;
        
        if(!$id){$id=$dados['id'];}

        // Encontre o curso pelo ID e atualize os dados
        $curso = Curso::find($id);

        if($dados['publicado']==='true'){$dados['publicado']=1;}else{$dados['publicado']=null;}
        if($dados['permitir_afiliacao']==='true'){$dados['permitir_afiliacao']=1;}else{$dados['permitir_afiliacao']=null;}
        if($dados['mostrar_na_pagina']==='true'){$dados['mostrar_na_pagina']=1;}else{$dados['mostrar_na_pagina']=null;}
        if($dados['gratuito']==='true'){$dados['gratuito']=1;}else{$dados['gratuito']=null;}

        if($curso->update($dados)) {
            return response()->json(['message' => json_encode($dados['gratuito'])]);
            //return response()->json(['message' => 'Curso atualizado com sucesso!']);
        } else {
            return response()->json(['message' => 'Erro na atualização! Contate o Dev'], 500);
        }

    }

    public function adm_editar_curso_post(Request $request, $id){

        $dados = $request->except('_token', '_method');

        // Encontrar o curso pelo ID e atualizar os dados
        $curso = Curso::find($id);

        if($curso){

            if ($request->file('capa_quadrada')) {
                $capa_quadrada = $request->file('capa_quadrada')->store('uploads/capa_cursos', 'public');
                $dados['capa_quadrada'] = $capa_quadrada;
                $this->deleteOldImage($curso->capa_quadrada);
            }
            if ($request->file('capa_vertical')) {
                
                $capa_vertical = $request->file('capa_vertical')->store('uploads/capa_cursos', 'public');
                $dados['capa_vertical'] = $capa_vertical;
                
                $this->deleteOldImage($curso->capa_vertical);
            }
            if ($request->file('capa_horizontal')) {
                $capa_horizontal = $request->file('capa_horizontal')->store('uploads/capa_cursos', 'public');
                $dados['capa_horizontal'] = $capa_horizontal;
                $this->deleteOldImage($curso->capa_horizontal);
            }

            if ($request->file('professor_foto')) {
                $professor_foto = $request->file('professor_foto')->store('uploads/capa_cursos', 'public');
                $dados['professor_foto'] = $professor_foto;
                if($curso->professor_foto){$this->deleteOldImage($curso->professor_foto);}
                
            }

            if(
                !$curso['url'] ||
                str_starts_with($curso['url'], 'curso-temp-')
            ){
                $dados['url'] = $this->createSlug($dados['titulo']);
                if(!$dados['url']){return redirect()->back()->with('error', 'Curso com mesmo nome já cadastrado');}
            }

            if(!isset($dados['publicado'])){$dados['publicado']=false;}
            if(!isset($dados['permitir_afiliacao'])){$dados['permitir_afiliacao']=false;}
            if(!isset($dados['mostrar_na_pagina'])){$dados['mostrar_na_pagina']=false;}
            if(!isset($dados['gratuito'])){$dados['gratuito']=false;}
            
            
            if ($curso->update($dados)) {
                return redirect()->back()->with('success', json_encode($dados['gratuito']));
                //return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
            }

        }else{
            return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
        }
    }

    private function deleteOldImage($imagePath)
    {
        if ($imagePath) {
            $fullImagePath = storage_path('app/public/' . $imagePath);
            if (is_file($fullImagePath) && file_exists($fullImagePath)) {
                unlink($fullImagePath);
            }
        }
    }


    public function createSlug($string) {
        // Converter para minúsculas
        $string = strtolower($string);

        // Substituir caracteres acentuados por suas versões não acentuadas
        $unwanted_array = array(
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
            'Á'=>'a','À'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a',
            'É'=>'e','È'=>'e','Ê'=>'e','Ë'=>'e',
            'Í'=>'i','Ì'=>'i','Î'=>'i','Ï'=>'i',
            'Ó'=>'o','Ò'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o',
            'Ú'=>'u','Ù'=>'u','Û'=>'u','Ü'=>'u',
            'Ç'=>'c','Ñ'=>'n'
        );
        $string = strtr($string, $unwanted_array);

        // Substituir qualquer caractere que não seja letra ou número por um espaço
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        // Substituir múltiplos espaços por um único espaço
        $string = preg_replace('/\s+/', ' ', $string);

        // Substituir espaços por hífens
        $string = str_replace(' ', '-', $string);

        $curso = Curso::where('url', $string)->first();

        if($curso){
            return false;
        }else{
            return $string;
        }

        
    }

    public function leads_hotmart()
    {      
        // Pagina os resultados e mantém os parâmetros da query string
        $data = PurchaseEvent::orderBy('created_at', 'desc')->paginate(150);

        $hotmart_leads = $data;
        $titulo_pagina = "Todos os leads";
        // Retorna a view com os leads paginados
        
        return view('adm.leads.leads', compact('hotmart_leads', 'titulo_pagina'));
    }

    public function purchase_events(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, true);

        $filters['per_page'] = min(max($filters['per_page'], 10), 200);

        if ($filters['date_start'] !== '' && $filters['date_end'] !== '' && $filters['date_start'] > $filters['date_end']) {
            $swap = $filters['date_start'];
            $filters['date_start'] = $filters['date_end'];
            $filters['date_end'] = $swap;
        }

        $statusOptions = [
            'WAITING_PAYMENT',
            'BILLET_PRINTED',
            'APPROVED',
            'COMPLETED',
            'CANCELED',
            'CANCELLED',
            'EXPIRED',
            'DELAYED',
        ];

        if (!Schema::hasTable('purchase_events')) {
            return view('dashboard.admin.purchase-events', [
                'purchaseEventsGrouped' => null,
                'filters' => $filters,
                'statusOptions' => $statusOptions,
                'tagFieldLabels' => $tagFieldLabels,
                'warningMessage' => 'A tabela purchase_events não existe neste ambiente.',
                'totalGroupedRecords' => 0,
            ]);
        }

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels);

        $totalGroupedRecords = (clone $baseQuery)
            ->distinct('buyer_checkout_phone')
            ->count('buyer_checkout_phone');

        $purchaseEventsGrouped = (clone $baseQuery)
            ->selectRaw('MAX(buyer_name) as buyer_name')
            ->selectRaw('buyer_checkout_phone')
            ->selectRaw('COUNT(*) as total_events')
            ->selectRaw('COUNT(DISTINCT `transaction`) as total_transactions')
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(purchase_status, ''))) IN ('APPROVED', 'COMPLETED') THEN 1 ELSE 0 END) as total_approved")
            ->selectRaw('MAX(created_at) as last_event_at')
            ->groupBy('buyer_checkout_phone')
            ->orderByRaw('MAX(created_at) DESC')
            ->simplePaginate($filters['per_page'])
            ->appends($request->query());

        return view('dashboard.admin.purchase-events', [
            'purchaseEventsGrouped' => $purchaseEventsGrouped,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'tagFieldLabels' => $tagFieldLabels,
            'warningMessage' => null,
            'totalGroupedRecords' => $totalGroupedRecords,
        ]);
    }

    public function purchase_events_suggestions(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $field = (string) $request->query('field', '');
        $queryText = trim((string) $request->query('q', ''));

        if (!array_key_exists($field, $tagFieldLabels)) {
            return response()->json([]);
        }

        if (mb_strlen($queryText) < 2 || !Schema::hasTable('purchase_events')) {
            return response()->json([]);
        }
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, false);

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels, $field);

        $query = (clone $baseQuery)
            ->selectRaw($field)
            ->whereNotNull($field)
            ->where($field, '<>', '');

        if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
            $query->whereRaw("UPPER(COALESCE({$field}, '')) LIKE ?", ['%' . Str::upper($queryText) . '%']);
        } elseif ($field === 'purchase_full_price_value') {
            $query->whereRaw("CAST({$field} AS TEXT) LIKE ?", ['%' . $queryText . '%']);
        } else {
            $query->where($field, 'like', '%' . $queryText . '%');
        }

        $results = $query
            ->distinct()
            ->orderBy($field)
            ->limit(12)
            ->pluck($field)
            ->filter()
            ->values()
            ->all();

        return response()->json($results);
    }

    public function purchase_events_related(Request $request)
    {
        $phone = preg_replace('/\D/', '', (string) $request->query('phone', ''));

        if ($phone === '' || !Schema::hasTable('purchase_events')) {
            return response()->json(['records' => []]);
        }

        $records = PurchaseEvent::query()
            ->where('buyer_checkout_phone', $phone)
            ->orderByDesc('created_at')
            ->get([
                'created_at',
                'buyer_name',
                'product_name',
                'event',
                'purchase_full_price_value',
                'purchase_status',
                'purchase_payment_type',
                'affiliate_name',
                'affiliate_code',
                'transaction',
            ])
            ->map(function ($record) {
                return [
                    'created_at' => optional($record->created_at)->format('Y-m-d H:i:s'),
                    'buyer_name' => $record->buyer_name,
                    'product_name' => $record->product_name,
                    'event' => $record->event,
                    'purchase_full_price_value' => $record->purchase_full_price_value,
                    'purchase_status' => $record->purchase_status,
                    'purchase_payment_type' => $record->purchase_payment_type,
                    'affiliate_name' => $record->affiliate_name,
                    'affiliate_code' => $record->affiliate_code,
                    'transaction' => $record->transaction,
                ];
            })
            ->values();

        return response()->json(['records' => $records]);
    }

    public function purchase_events_export_csv(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, true);

        if ($filters['date_start'] !== '' && $filters['date_end'] !== '' && $filters['date_start'] > $filters['date_end']) {
            $swap = $filters['date_start'];
            $filters['date_start'] = $filters['date_end'];
            $filters['date_end'] = $swap;
        }

        if (!Schema::hasTable('purchase_events')) {
            return redirect()
                ->route('admin.purchase_events')
                ->with('error', 'A tabela purchase_events não existe neste ambiente.');
        }

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels);

        $exportQuery = (clone $baseQuery)
            ->selectRaw('MAX(buyer_name) as buyer_name')
            ->selectRaw('buyer_checkout_phone')
            ->groupBy('buyer_checkout_phone')
            ->orderBy('buyer_checkout_phone');

        $fileName = 'purchase_events_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($exportQuery) {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                return;
            }

            // BOM para compatibilidade de acentuação no Excel.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Nome', 'Telefone'], ';');

            foreach ($exportQuery->cursor() as $row) {
                fputcsv($output, [
                    (string) ($row->buyer_name ?? ''),
                    (string) ($row->buyer_checkout_phone ?? ''),
                ], ';');
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function purchaseEventTagFieldLabels(): array
    {
        return [
            'product_name' => 'Produto',
            'event' => 'Evento',
            'purchase_full_price_value' => 'Valor compra',
            'purchase_status' => 'Status',
            'purchase_payment_type' => 'Forma pagamento',
            'affiliate_name' => 'Afiliado nome',
            'affiliate_code' => 'Afiliado código',
        ];
    }

    private function parsePurchaseEventFilters(Request $request, array $tagFieldLabels, bool $useDefaultDates): array
    {
        $filters = [
            'date_start' => (string) $request->query('date_start', $useDefaultDates ? now()->subDays(90)->toDateString() : ''),
            'date_end' => (string) $request->query('date_end', $useDefaultDates ? now()->toDateString() : ''),
            'phone' => preg_replace('/\D/', '', (string) $request->query('phone', '')),
            'free_search' => trim((string) $request->query('free_search', '')),
            'per_page' => (int) $request->query('per_page', 50),
        ];

        foreach (array_keys($tagFieldLabels) as $field) {
            $includeRaw = $request->query($field, []);
            if (!is_array($includeRaw)) {
                $includeRaw = trim((string) $includeRaw) !== '' ? [$includeRaw] : [];
            }

            $excludeRaw = $request->query($field . '_exclude', []);
            if (!is_array($excludeRaw)) {
                $excludeRaw = trim((string) $excludeRaw) !== '' ? [$excludeRaw] : [];
            }

            $include = $this->normalizeTagValues($field, $includeRaw);
            $exclude = $this->normalizeTagValues($field, $excludeRaw);

            // Não permitir conflito do mesmo valor entre inclusão e exclusão.
            $exclude = array_values(array_diff($exclude, $include));

            $filters[$field] = [
                'include' => $include,
                'exclude' => $exclude,
            ];
        }

        return $filters;
    }

    private function normalizeTagValues(string $field, array $rawValues): array
    {
        $values = collect($rawValues)
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->values()
            ->unique()
            ->all();

        if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
            $values = array_values(array_unique(array_map(static fn ($v) => strtoupper((string) $v), $values)));
        }

        if ($field === 'purchase_full_price_value') {
            $values = collect($values)
                ->map(fn ($value) => $this->normalizeMoneyFilterValue($value))
                ->filter(fn ($value) => $value !== null)
                ->values()
                ->all();
        }

        return $values;
    }

    private function normalizeMoneyFilterValue(string $value): ?string
    {
        $raw = preg_replace('/[^0-9,.-]/', '', $value);
        if ($raw === null || $raw === '') {
            return null;
        }

        if (str_contains($raw, ',') && str_contains($raw, '.')) {
            $raw = str_replace('.', '', $raw);
        }

        $raw = str_replace(',', '.', $raw);

        return is_numeric($raw) ? (string) $raw : null;
    }

    private function purchaseEventFiltersWithoutExcludes(array $filters, array $tagFieldLabels): array
    {
        $cloned = $filters;

        foreach (array_keys($tagFieldLabels) as $field) {
            $include = $cloned[$field]['include'] ?? [];
            $cloned[$field] = [
                'include' => is_array($include) ? $include : [],
                'exclude' => [],
            ];
        }

        return $cloned;
    }

    private function applyPurchaseEventFilters($query, array $filters, array $tagFieldLabels, ?string $excludeTagField = null): void
    {
        if (($filters['date_start'] ?? '') !== '') {
            $query->whereDate('created_at', '>=', $filters['date_start']);
        }

        if (($filters['date_end'] ?? '') !== '') {
            $query->whereDate('created_at', '<=', $filters['date_end']);
        }

        if (($filters['phone'] ?? '') !== '') {
            $query->where('buyer_checkout_phone', 'like', '%' . $filters['phone'] . '%');
        }

        if (($filters['free_search'] ?? '') !== '') {
            $freeSearch = trim((string) $filters['free_search']);
            $freeSearchDigits = preg_replace('/\D/', '', $freeSearch);

            $query->where(function ($subQuery) use ($freeSearch, $freeSearchDigits) {
                $subQuery
                    ->where('buyer_name', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_email', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_document', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_checkout_phone', 'like', '%' . $freeSearch . '%');

                if ($freeSearchDigits !== '' && $freeSearchDigits !== $freeSearch) {
                    $subQuery
                        ->orWhere('buyer_document', 'like', '%' . $freeSearchDigits . '%')
                        ->orWhere('buyer_checkout_phone', 'like', '%' . $freeSearchDigits . '%');
                }
            });
        }

        $filtersWithoutExcludes = $this->purchaseEventFiltersWithoutExcludes($filters, $tagFieldLabels);

        foreach (array_keys($tagFieldLabels) as $field) {
            if ($excludeTagField === $field) {
                continue;
            }

            $includeValues = $filters[$field]['include'] ?? [];
            $excludeValues = $filters[$field]['exclude'] ?? [];

            if (empty($includeValues) && empty($excludeValues)) {
                continue;
            }

            if ($field === 'purchase_full_price_value') {
                if (!empty($includeValues)) {
                    $query->whereIn($field, array_map('floatval', $includeValues));
                }

                if (!empty($excludeValues)) {
                    $query->whereNotIn($field, array_map('floatval', $excludeValues));
                }
            } elseif (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
                if (!empty($includeValues)) {
                    $placeholders = implode(',', array_fill(0, count($includeValues), '?'));
                    $query->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) IN ({$placeholders})", $includeValues);
                }

                if (!empty($excludeValues)) {
                    $placeholders = implode(',', array_fill(0, count($excludeValues), '?'));
                    $query->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) NOT IN ({$placeholders})", $excludeValues);
                }
            } else {
                if (!empty($includeValues)) {
                    $query->whereIn($field, $includeValues);
                }

                if (!empty($excludeValues)) {
                    $query->whereNotIn($field, $excludeValues);
                }
            }

            if (!empty($excludeValues)) {
                $query->whereNotIn('buyer_checkout_phone', function ($subQuery) use ($filtersWithoutExcludes, $tagFieldLabels, $excludeTagField, $field, $excludeValues) {
                    $subQuery
                        ->from('purchase_events as pe_ex')
                        ->select('pe_ex.buyer_checkout_phone')
                        ->whereNotNull('pe_ex.buyer_checkout_phone')
                        ->where('pe_ex.buyer_checkout_phone', '<>', '');

                    $this->applyPurchaseEventFilters($subQuery, $filtersWithoutExcludes, $tagFieldLabels, $excludeTagField);

                    if ($field === 'purchase_full_price_value') {
                        $subQuery->whereIn($field, array_map('floatval', $excludeValues));
                        return;
                    }

                    if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
                        $placeholders = implode(',', array_fill(0, count($excludeValues), '?'));
                        $subQuery->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) IN ({$placeholders})", $excludeValues);
                        return;
                    }

                    $subQuery->whereIn($field, $excludeValues);
                });
            }
        }
    }
  
}
