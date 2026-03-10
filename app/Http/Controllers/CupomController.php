<?php

namespace App\Http\Controllers;

use App\Models\Cupom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CupomController extends Controller
{
    public function index(): View
    {
        $cupons = Cupom::orderBy('desconto')->orderBy('codigo')->get();

        return view('dashboard.admin.cupons', compact('cupons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validatedData($request);

        Cupom::create($dados);

        return redirect()
            ->route('admin.cupons.index')
            ->with('success', 'Cupom criado com sucesso.');
    }

    public function update(Request $request, Cupom $cupom): RedirectResponse
    {
        $dados = $this->validatedData($request, $cupom);

        $cupom->update($dados);

        return redirect()
            ->route('admin.cupons.index')
            ->with('success', 'Cupom atualizado com sucesso.');
    }

    public function destroy(Cupom $cupom): RedirectResponse
    {
        $cupom->delete();

        return redirect()
            ->route('admin.cupons.index')
            ->with('success', 'Cupom excluído com sucesso.');
    }

    private function validatedData(Request $request, ?Cupom $cupom = null): array
    {
        $descontoRaw = trim((string) $request->input('desconto'));
        $descontoNormalizado = str_replace(',', '.', $descontoRaw);

        $request->merge([
            'codigo' => Str::upper(trim((string) $request->input('codigo'))),
            'desconto' => $descontoNormalizado,
        ]);

        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('cupons', 'codigo')->ignore($cupom?->id),
            ],
            'desconto' => ['required', 'numeric', 'between:1,100', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);
    }
}
