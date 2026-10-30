# Otimização de memória: cursos e cupons

## Medição local

Em 29/10/2026, cada cenário foi executado em processo PHP CLI 8.4.16 separado, com SQLite copiado do ambiente local e 100 cupons sintéticos adicionais: 64 cursos e 104 cupons no total. Um curso tinha configuração raiz `um_preco`. O painel foi medido com usuário local e sem middleware de autenticação/onboarding, para isolar a renderização; não é medição ponta a ponta. A memória é o pico de memória reservada pelo alocador PHP (`memory_get_peak_usage(true)`), incluindo o estado já carregado do processo. São amostras individuais, sujeitas a aquecimento de cache e variações de máquina, e não equivalem a dados nem tráfego de produção.

| Cenário | Revisão | Consultas | Tempo (ms) | Pico reservado (MiB) | Resposta (bytes) |
|---|---|---:|---:|---:|---:|
| Página de curso | Antes | 10 | 13,87 | 26 | 96.785 |
| Página de curso | Etapa 1 | 10 | 26,85 | 26 | 96.785 |
| Página de curso | Etapa 2 | 10 | 27,90 | 26 | 96.785 |
| API de cursos | Antes | 563 | 64,57 | 24 | 50.750 |
| API de cursos | Etapa 1 | 55 | 16,87 | 24 | 50.750 |
| API de cursos | Etapa 2 | 55 | 17,60 | 24 | 50.750 |
| Catálogo XML | Antes | 193 | 79,36 | 24 | 50.845 |
| Catálogo XML | Etapa 1 | 52 | 17,90 | 24 | 50.845 |
| Catálogo XML | Etapa 2 | 52 | 17,63 | 24 | 50.845 |
| Catálogo Markdown | Antes | 193 | 77,54 | 24 | 45.497 |
| Catálogo Markdown | Etapa 1 | 52 | 17,90 | 24 | 45.497 |
| Catálogo Markdown | Etapa 2 | 52 | 17,55 | 24 | 45.497 |
| Painel de afiliados | Antes | 199 | 656,16 | 74,5 | 12.063.473 |
| Painel de afiliados | Etapa 1 | 135 | 629,09 | 74,5 | 12.063.473 |
| Painel de afiliados | Etapa 2 | 135 | 360,40 | 74,5 | 12.063.473 |

As respostas da API e dos dois catálogos foram comparadas byte a byte entre a revisão inicial e a etapa 2, no cenário acima, e foram idênticas. A página e o painel também foram idênticos após normalizar tokens CSRF e versões de assets geradas pelo ambiente. O pico reservado não mudou nessa medição; não há evidência aqui de redução do pico de memória. A página pública continua gerando todas as variantes por compatibilidade, e o painel continua produzindo uma resposta grande.

O script `tests/Performance/benchmark-pricing.php` executa um cenário por processo. Requer SQLite local com pelo menos um curso publicado e um afiliado, e a tabela de configurações disponível. Ele cria uma cópia temporária, acrescenta os cupons sintéticos e não grava no banco de origem:

```sh
for scenario in page api catalog catalog2 panel; do
  php -d error_reporting=0 tests/Performance/benchmark-pricing.php "$scenario"
done
```

Use `BENCHMARK_SQLITE_SOURCE` para apontar outro arquivo de teste. `BENCHMARK_RESPONSE_PATH` permite salvar uma resposta para comparação; trate o conteúdo como potencialmente sensível. Não use uma cópia de dados pessoais de produção sem os controles apropriados.

## Testes e implantação

A suíte completa executada nesta revisão teve 239 testes, 1.711 asserções e seis falhas. A revisão inicial, executada em checkout isolado com as mesmas dependências e assets, teve as mesmas seis falhas (232 testes, 1.665 asserções): `AuthVerificationFlowTest`, `ComboPublicPageTest`, `DashboardHomeLayoutPreferenceTest`, `JourneyAdminCrudTest`, `JourneyRewardClaimTest` e o teste de layout de CTA de `PublicCoursePricingDisplayTest`. Há também uma depreciação em dependência no PHP 8.4. Os testes focados de consultas, API, catálogo, painel e configurações de preços passaram.

Antes de implantar, obter contagens reais de cursos, cupons e configurações e repetir as medições com volume representativo em homologação. Validar os testes existentes e as seis falhas preexistentes no ambiente de entrega. Implantar os commits `ae8db56` e `c262dc7` em ordem, monitorando erros de memória, latência, consultas e tamanho de resposta por rota. Correlacionar erros residuais com os logs de acesso, Laravel, PHP-FPM e workers, respeitando a privacidade de dados. Reverter em ordem inversa se necessário; não aumentar `memory_limit` como solução.
