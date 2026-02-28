# Plano Técnico - Tela Administrador de Purchase Events Agrupados por Telefone

## Objetivo
Criar a rota `GET /administrador/purchase_events` para exibir uma tabela de `purchase_events` agrupada por `buyer_checkout_phone`, com foco em desempenho para base com ~300 mil registros.

## Escopo da Primeira Entrega (MVP)
- Nova rota administrativa protegida por autenticação e permissão de admin.
- Nova action no `AdminController`.
- Nova view com tabela agrupada por telefone.
- Filtros de consulta:
  - período (`date_start`, `date_end`) com padrão de últimos 90 dias;
  - telefone (`phone`, busca parcial);
  - status (`purchase_status`, opcional).
- Paginação leve (`simplePaginate`) para reduzir custo de contagem total.

## Requisitos Funcionais
- Agrupar por `buyer_checkout_phone`.
- Exibir por grupo:
  - telefone;
  - total de eventos;
  - total de transações distintas;
  - total de aprovadas (`APPROVED` e `COMPLETED`);
  - última ocorrência (`MAX(created_at)`).
- Ordenar pela última ocorrência (desc).

## Requisitos Não Funcionais
- Evitar carga total em memória (sem `->get()` para dataset completo).
- Evitar agregações em Collection PHP; usar agregação SQL.
- Paginar no banco.
- Trabalhar com filtros por período desde o primeiro release.

## Riscos e Brechas
1. **Normalização de telefone ausente**
   - Mesmo cliente pode aparecer em mais de um grupo por variação de formato.
2. **Tabela sem migration versionada no repositório**
   - Risco de drift entre ambientes (estrutura e índices divergentes).
3. **Índices possivelmente insuficientes**
   - `GROUP BY buyer_checkout_phone` + `MAX(created_at)` pode degradar sem índices adequados.
4. **Segurança de rotas admin heterogênea**
   - Parte das rotas usa checagem inline em closure.

## Estratégia Técnica
### MVP (agora)
- Entregar rota + controller + view com agregação SQL e filtros.
- Usar `simplePaginate`.
- Garantir proteção da nova rota com middleware (`auth`, `check.admin`).

### Evolução recomendada (fase 2)
- Adicionar coluna normalizada (`phone_normalized`) e índices:
  - `phone_normalized`
  - `(phone_normalized, created_at)`
  - `(purchase_status, created_at)`
- Backfill gradual da coluna normalizada via comando/queue job.
- Trocar agrupamento para `phone_normalized`.

### Escala futura (fase 3)
- Criar tabela de resumo materializado por telefone (`purchase_event_phone_stats`) alimentada por job incremental.
- Usar a tabela de resumo para listagem principal e manter `purchase_events` apenas para drill-down.

## Query-base do MVP (conceitual)
```sql
SELECT
  buyer_checkout_phone AS phone,
  COUNT(*) AS total_events,
  COUNT(DISTINCT transaction) AS total_transactions,
  SUM(CASE WHEN UPPER(COALESCE(purchase_status, '')) IN ('APPROVED', 'COMPLETED') THEN 1 ELSE 0 END) AS total_approved,
  MAX(created_at) AS last_event_at
FROM purchase_events
WHERE buyer_checkout_phone IS NOT NULL
  AND buyer_checkout_phone <> ''
  AND created_at BETWEEN :date_start AND :date_end
GROUP BY buyer_checkout_phone
ORDER BY MAX(created_at) DESC
LIMIT :per_page;
```

## Critérios de Aceite
- A rota `administrador/purchase_events` abre sem erro para admin autenticado.
- A tabela renderiza dados agrupados por telefone.
- Filtros aplicam corretamente.
- Paginação funciona sem explosão de memória.

## Checklist de Implementação
- [x] Documento técnico criado.
- [x] Rota criada.
- [x] Controller implementado.
- [x] View implementada.
- [x] Validação local da rota.

## Observações
- Como a tabela `purchase_events` não existe no banco local atual, a implementação deve tratar ausência da tabela de forma graciosa (mensagem de indisponibilidade), evitando erro 500 em desenvolvimento.
