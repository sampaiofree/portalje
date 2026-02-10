<?php

namespace App\Http\Controllers;

use App\Models\Dados_portal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DadosPortalController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        if (!Schema::hasTable('portal_informacoes')) {
            return redirect('dashboard')->with('error', 'A tabela portal_informacoes ainda não foi criada.');
        }

        $dadosPortal = Dados_portal::first();

        return view('dashboard.admin.portal-informacoes', compact('dadosPortal'));
    }

    public function update(Request $request): RedirectResponse
    {
        if (!Auth::check() || Auth::user()->nivel_acesso !== 'admin') {
            return redirect('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        if (!Schema::hasTable('portal_informacoes')) {
            return redirect()->route('adm_portal_informacoes')->with('error', 'A tabela portal_informacoes ainda não foi criada.');
        }

        $dados = $request->validate([
            'telefone_suporte_alunos' => 'required|string|max:25',
            'telefone_suporte_afiliados' => 'nullable|string|max:25',
            'whatsapp_atendimento_tempo' => 'nullable|string|max:25',
            'endereco' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:25',
        ]);

        $dados['formulario_whatsapp'] = $request->boolean('formulario_whatsapp');
        $dados['formulario_pre_checkout'] = $request->boolean('formulario_pre_checkout');

        $registroAtual = Dados_portal::first();

        if ($registroAtual) {
            $registroAtual->update($dados);
        } else {
            Dados_portal::create($dados);
        }

        return redirect()->route('adm_portal_informacoes')->with('success', 'Informações do portal atualizadas com sucesso.');
    }
}
