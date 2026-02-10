<?php

namespace Database\Seeders;

use App\Models\Curso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('curso')) {
            return;
        }

        $cursos = [
            [
                'ordem' => 1,
                'codigo_id_hotmart' => 'CURSO-001',
                'url' => 'auxiliar-administrativo',
                'categorias' => 'Administracao',
                'titulo' => 'Auxiliar Administrativo',
                'headline' => 'Aprenda rotinas administrativas e comece a trabalhar mais rapido.',
                'descricao_curta' => 'Formacao pratica para atuar em escritorio, atendimento e suporte operacional.',
                'areas_de_atuacao' => 'Escritorio/Atendimento/Financeiro',
                'salario_maximo' => 3200.00,
                'nota_avaliacao' => 4.9,
                'numero_alunos' => 12500,
                'preco_parcelado_basico' => '12xR$19,90',
                'preco_parcelado_completo' => '12xR$39,90',
                'preco_cheio_basico' => 'R$239,90',
                'preco_cheio_completo' => 'R$479,90',
                'horas_basico' => 40,
                'horas_completo' => 80,
            ],
            [
                'ordem' => 2,
                'codigo_id_hotmart' => 'CURSO-002',
                'url' => 'auxiliar-de-contabilidade',
                'categorias' => 'Contabilidade',
                'titulo' => 'Auxiliar de Contabilidade',
                'headline' => 'Domine lancamentos, documentos fiscais e controle financeiro.',
                'descricao_curta' => 'Capacitacao para iniciantes em rotinas de contabilidade empresarial.',
                'areas_de_atuacao' => 'Contabilidade/Financeiro/Fiscal',
                'salario_maximo' => 3800.00,
                'nota_avaliacao' => 4.8,
                'numero_alunos' => 9800,
                'preco_parcelado_basico' => '12xR$24,90',
                'preco_parcelado_completo' => '12xR$44,90',
                'preco_cheio_basico' => 'R$299,90',
                'preco_cheio_completo' => 'R$539,90',
                'horas_basico' => 45,
                'horas_completo' => 90,
            ],
            [
                'ordem' => 3,
                'codigo_id_hotmart' => 'CURSO-003',
                'url' => 'atendente-de-farmacia',
                'categorias' => 'Saude',
                'titulo' => 'Atendente de Farmacia',
                'headline' => 'Atenda com seguranca, tecnica e foco no cliente.',
                'descricao_curta' => 'Curso completo para quem quer entrar no setor farmaceutico.',
                'areas_de_atuacao' => 'Farmacia/Atendimento/Vendas',
                'salario_maximo' => 2900.00,
                'nota_avaliacao' => 4.9,
                'numero_alunos' => 15000,
                'preco_parcelado_basico' => '12xR$17,90',
                'preco_parcelado_completo' => '12xR$34,90',
                'preco_cheio_basico' => 'R$214,90',
                'preco_cheio_completo' => 'R$419,90',
                'horas_basico' => 36,
                'horas_completo' => 72,
            ],
            [
                'ordem' => 4,
                'codigo_id_hotmart' => 'CURSO-004',
                'url' => 'assistente-de-rh',
                'categorias' => 'Recursos Humanos',
                'titulo' => 'Assistente de RH',
                'headline' => 'Aprenda recrutamento, admissao e rotinas de departamento pessoal.',
                'descricao_curta' => 'Formacao para atuar em RH e DP com visao pratica.',
                'areas_de_atuacao' => 'RH/Departamento Pessoal/Administracao',
                'salario_maximo' => 3600.00,
                'nota_avaliacao' => 4.7,
                'numero_alunos' => 7600,
                'preco_parcelado_basico' => '12xR$22,90',
                'preco_parcelado_completo' => '12xR$42,90',
                'preco_cheio_basico' => 'R$274,90',
                'preco_cheio_completo' => 'R$514,90',
                'horas_basico' => 42,
                'horas_completo' => 84,
            ],
            [
                'ordem' => 5,
                'codigo_id_hotmart' => 'CURSO-005',
                'url' => 'informatica-profissional',
                'categorias' => 'Informatica',
                'titulo' => 'Informatica Profissional',
                'headline' => 'Pacote Office, internet e produtividade para o mercado de trabalho.',
                'descricao_curta' => 'Curso para iniciantes com foco em empregabilidade.',
                'areas_de_atuacao' => 'Escritorio/Comercial/Atendimento',
                'salario_maximo' => 3400.00,
                'nota_avaliacao' => 4.8,
                'numero_alunos' => 18400,
                'preco_parcelado_basico' => '12xR$14,90',
                'preco_parcelado_completo' => '12xR$29,90',
                'preco_cheio_basico' => 'R$179,90',
                'preco_cheio_completo' => 'R$359,90',
                'horas_basico' => 30,
                'horas_completo' => 60,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::updateOrCreate(
                ['url' => $curso['url']],
                array_merge($curso, [
                    'gratuito' => false,
                    'publicado' => true,
                    'permitir_afiliacao' => true,
                    'mostrar_na_pagina' => true,
                    'beneficios_lista' => '<ul><li>Certificado valido</li><li>Suporte ao aluno</li></ul>',
                    'conteudo_principal' => '<ul><li>Modulo 1</li><li class="ql-indent-1">Fundamentos</li><li>Modulo 2</li><li class="ql-indent-1">Aplicacao pratica</li></ul>',
                    'conteudo_bonus' => '<ul><li>Bono de carreira</li><li class="ql-indent-1">Curriculo e entrevista</li></ul>',
                    'capa_quadrada' => 'img/home_page/portal-jovem-empreendedor.webp',
                    'capa_vertical' => 'img/home_page/portal-jovem-empreendedor.webp',
                    'capa_horizontal' => 'img/home_page/portal-jovem-empreendedor.webp',
                    'video_dentro_do_curso' => 'dQw4w9WgXcQ',
                    'video_apresentacao' => 'dQw4w9WgXcQ',
                    'link_checkout_basico' => 'https://go.hotmart.com/T00000000B?ap=basico',
                    'link_checkout_completo' => 'https://go.hotmart.com/T00000000B?ap=completo',
                    'codigo_afiliado_plano_basico' => 'b001',
                    'codigo_afiliado_plano_completo' => 'c001',
                    'link_materiais' => 'https://portalje.test/materiais',
                    'link_afiliacao' => 'https://portalje.test/afiliados',
                    'link_area_membros' => 'https://portalje.test/alunos',
                    'professor_nome' => 'Equipe PortalJE',
                    'professor_biografia' => 'Instrutores com experiencia em cursos profissionalizantes.',
                    'professor_foto' => 'img/home_page/portal-jovem-empreendedor.webp',
                ])
            );
        }
    }
}
