<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Curso;

class DataController extends Controller
{
    public function user(Request $request)
    {
        try {
            // Verificar se o JSON recebido é válido
            $dados = $request->all();

            $dados['user_registration'] = strtotime($dados['user_registration']);
            $dados['user_registration'] = date('Y-m-d H:i:s', $dados['user_registration']);

            // Criação do novo usuário
            $novoUsuario = [
                'id' => $dados['user_id'],
                'name' => $dados['user_name'] . " " . $dados['user_lastname'],
                'email' => $dados['user_email'] ,
                'password' => bcrypt($dados['user_password']),
                'created_at' => $dados['user_registration'] ?? "",
                'updated_at' => now(),
                'thumb' => "",
                'cpf' => $dados['user_document'] ?? "",
                'telefone_pessoal_1' => $dados['user_cell'] ?? "",
                'telefone_pessoal_2' => $dados['user_cell'] ?? "",
                'apelido' => $dados['user_apelido'] ?? "",
                'mentorado' => $dados['user_mentorado'] ?? "",
                'meta_pixel_id' => $dados['user_aff_pixelfb'] ?? "",
                'meta_pixel_api' => $dados['pixelFaceAPI'] ?? "",
                'meta_pixel_eventcode' => $dados['pixeltesteventcode'] ?? "",
                'google_ads' => $dados['user_aff_pixelggads'] ?? "",
                'whatsapp_atendimento' => $dados['user_aff_whats'] ?? "",
                'whatsapp_atendimento_tempo' => $dados['user_aff_domain_whatsapp_tempo'] ?? "",
                'dominio' => $dados['user_aff_domain'] ?? "",
                'meta_conta_anuncios_id' => $dados['user_ad_account_id'] ?? "",
                'meta_pagina_id' => $dados['user_page_id'] ?? "",
                'meta_instagram_id' => $dados['user_instagram_actor_id'] ?? "",
                'meta_app_id' => $dados['appId'] ?? "",
                'faturamento_total' => $dados['user_total_faturamento'] ?? "",
                'comissao_total' => $dados['user_total_comissao'] ?? "",
                'numero_total_vendas' => $dados['user_total_vendas'] ?? ""
            ];

            // Armazene os dados no banco de dados
            $user = User::create($novoUsuario);

//            return response( $dados['user_registration']);
            return response( $user);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar usuário'], 500);
        }
    }

    public function curso(Request $request)
    {
        //$data = 
        
        /*$data = $request->validate([
            'cursos' => 'array',
            'cursos.*.course_id' => 'integer',
            'cursos.*.course_vendor_id' => 'string',
            'cursos.*.course_alias_aff' => 'string',
            'cursos.*.course_title' => 'string',
            'cursos.*.course_headline' => 'nullable|string',
            'cursos.*.course_desc' => 'nullable|string',
            'cursos.*.conteudo' => 'nullable|string',
            'cursos.*.bonus' => 'nullable|string',
            'cursos.*.course_cover' => 'string',
            'cursos.*.img_capa_vertical' => 'string',
            'cursos.*.img_capa_horizontal' => 'string',
            'cursos.*.course_reviews' => 'nullable|string',
            'cursos.*.course_students' => 'nullable|string',
            'cursos.*.course_works' => 'nullable|string',
            'cursos.*.course_salary' => 'nullable|string',
            'cursos.*.video_curso_por_dentro' => 'nullable|string',
            'cursos.*.video_apresentacao_venda' => 'nullable|string',
            'cursos.*.course_vendor_checkout' => 'string',
            'cursos.*.course_vendor_checkout2' => 'string',
            'cursos.*.course_aff_sellid_2' => 'string',
            'cursos.*.course_aff_sellid_1' => 'string',
            'cursos.*.preco_parcelado_basico' => 'nullable|string',
            'cursos.*.preco_parcelado_completo' => 'nullable|string',
            'cursos.*.preco_cheio_basico' => 'nullable|string',
            'cursos.*.preco_cheio_completo' => 'nullable|string',
            'cursos.*.horas_basico' => 'nullable|string',
            'cursos.*.horas_completo' => 'nullable|string',
            'cursos.*.course_material' => 'nullable|string',
            'cursos.*.course_affiliation_link' => 'nullable|string',
            'cursos.*.course_classes_link' => 'nullable|string',
            'cursos.*.user_name' => 'string',
            'cursos.*.user_lastname' => 'string',
            'cursos.*.user_twitter' => 'nullable|string',
            'cursos.*.user_thumb' => 'string',
        ]);*/

        $dados = $request->all();

        $cursos = [
            'id' => $dados['course_id'],
            'codigo_id_hotmart' => $dados['course_vendor_id'],
            'url' => $dados['course_alias_aff'],
            'titulo' => $dados['course_title'],
            'headline' => $dados['course_headline'] ?? null,
            'descricao_curta' => $dados['course_desc'] ?? null,
            'conteudo_principal' => $dados['conteudo'] ?? null,
            'conteudo_bonus' => $dados['bonus'] ?? null,
            'capa_quadrada' => "https://jovemempreendedor.org/uploads/" . $dados['course_cover'],
            'capa_vertical' => "https://jovemempreendedor.org/uploads/" . $dados['img_capa_vertical'],
            'capa_horizontal' => "https://jovemempreendedor.org/uploads/" . $dados['img_capa_horizontal'],
            'nota_avaliacao' => $dados['course_reviews'] ?? null,
            'numero_alunos' => $dados['course_students'] ?? null,
            'areas_de_atuacao' => $dados['course_works'] ?? null,
            'salario_maximo' => $dados['course_salary'] ?? null,
            'video_dentro_do_curso' => $dados['video_curso_por_dentro'] ?? null,
            'video_apresentacao' => $dados['video_apresentacao_venda'] ?? null,
            'link_checkout_basico' => $dados['course_vendor_checkout'],
            'link_checkout_completo' => $dados['course_vendor_checkout2'],
            'codigo_afiliado_plano_basico' => $dados['course_aff_sellid_2'],
            'codigo_afiliado_plano_completo' => $dados['course_aff_sellid_1'],
            'preco_parcelado_basico' => $dados['preco_parcelado_basico'] ?? null,
            'preco_parcelado_completo' => $dados['preco_parcelado_completo'] ?? null,
            'preco_cheio_basico' => $dados['preco_cheio_basico'] ?? null,
            'preco_cheio_completo' => $dados['preco_cheio_completo'] ?? null,
            'horas_basico' => $dados['horas_basico'] ?? null,
            'horas_completo' => $dados['horas_completo'] ?? null,
            'link_materiais' => $dados['course_material'] ?? null,
            'link_afiliacao' => $dados['course_affiliation_link'] ?? null,
            'link_area_membros' => $dados['course_classes_link'] ?? null,
            'professor_nome' => $dados['user_name'] . " " . $dados['user_lastname'],
            'professor_biografia' => $dados['user_twitter'] ?? null,
            'professor_foto' => "https://jovemempreendedor.org/uploads/" . $dados['user_thumb'],
        ];


        //return response()->json($cursos);
        //exit();

        Curso::create($cursos);
        //return response()->json(['message' => 'Cursos adicionados com sucesso'], 201);
    }
}
