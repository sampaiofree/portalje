# Ranking diário de XP

O ranking é apurado pelo comando `ranking:xp:rebuild` às 03h de
`America/Sao_Paulo`. A página lê o último resultado publicado em
`xp_ranking_snapshots`; mudanças de pontuação, nome e regras aparecem após a
próxima apuração. O top 100 é exibido em páginas de 10, e os totais consideram
todos os usuários com XP positivo.

## Implantação

1. Aplicar as migrações antes de direcionar tráfego ao código novo:
   `php artisan migrate --force`.
2. Confirmar a extensão PHP Redis e a conexão `database.redis.default` em
   todos os executores. O comando usa explicitamente o cache store `redis`
   para o bloqueio, mesmo quando `CACHE_STORE=file`. Servidores da mesma
   aplicação devem compartilhar servidor, banco e prefixos do Redis.
3. Executar `php artisan ranking:xp:rebuild` via CLI e verificar código de
   saída zero, horário publicado e top 100 antes de liberar a leitura nova.
4. Confirmar um executor do scheduler com cron a cada minuto, por exemplo
   `* * * * * cd /caminho/portalje && php artisan schedule:run >> /caminho/scheduler.log 2>&1`.
   `php artisan schedule:list` deve mostrar a apuração às 03h de Brasília.
   Não é necessário worker de fila. Em múltiplos servidores, preferir um
   único executor do cron; o bloqueio do comando também impede concorrência
   com execuções manuais.

O bloqueio dura duas horas. A apuração verifica a propriedade do bloqueio a
cada lote e, dentro da transação de publicação, após obter o bloqueio da linha
no banco. Se perder o bloqueio ou se aproximar do prazo de duas horas,
interrompe sem publicar. Redis indisponível também provoca falha, sem fallback
para um bloqueio local. Não remover um bloqueio para forçar outra execução
enquanto o processo original estiver ativo.

## Operação

- A saída e o log `Ranking de XP publicado.` incluem duração, pico de memória
  PHP, usuários processados, participantes, pontos e horário publicado.
- Configurar o monitoramento para alertar se `generated_at` do registro de
  ID 1 estiver nulo após a implantação ou tiver mais de 26 horas. A aba XP
  também sinaliza atualização em atraso. O comando retorna código 1 em caso
  de falha ou concorrência; consultar os logs antes de repetir manualmente.
- Um erro preserva integralmente a apuração anterior. Não há reconstrução
  HTTP, mesmo após limpeza de cache. Antes da primeira apuração, a página
  informa que aguarda o processamento.
- A paginação consulta a apuração atual a cada requisição, como anteriormente;
  uma página aberta durante a troca diária pode precisar ser recarregada.
- O middleware da página completa ainda calcula a jornada do usuário
  autenticado para o layout. O endpoint JSON não executa esse middleware.

## Verificação

```sh
php artisan test --compact --filter=XpRankingTest
php artisan test --compact --filter='Journey(DashboardRender|RewardClaim|AdminCrud)Test'
php artisan test --compact tests/Performance/XpRankingBenchmarkTest.php
```

O benchmark usa SQLite em memória com 50 mil usuários, 50 mil códigos e
50 mil eventos de compra, verificando o limite de consultas por lote.
Seus números medem uma carga sintética local; validar também o tempo e o
plano de execução no banco de produção com volume representativo.

Na validação local desta mudança, a apuração fez 510 consultas em 3,789 segundos,
com pico de memória PHP de 40,5 MiB. Esse pico não inclui a memória nativa do
SQLite; a medição de tempo exclui a preparação dos dados de teste.

Para testar o bloqueio real, iniciar um Redis isolado com porta desabilitada
e socket UNIX em diretório temporário. Executar:

```sh
XP_RANKING_REDIS_SOCKET=/caminho/redis-isolado.sock php artisan test --compact tests/Integration/XpRankingRedisLockTest.php
```

Os testes usam somente esse socket, com prefixo aleatório, e não limpam o Redis
da aplicação. Encerrar o servidor isolado ao terminar.
