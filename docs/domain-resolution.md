# Resolução de domínios

O middleware `ResolveAffiliateDomain` valida todas as rotas do grupo `web` antes de executar a página. Um domínio desconhecido recebe HTTP 302 para `https://portalje.org/`, sem caminho nem query string, com `Cache-Control: no-store`. API, `/up` e arquivos servidos diretamente pelo servidor web ficam fora dessa validação.

Os domínios oficiais permitidos são `portalje.org`, `dns.portalje.org`, `jovemempreendedor.org`, `dns.jovemempreendedor.org`, `jemp.me`, `afiliados.jovemempreendedor.org` e o hostname de `APP_URL`. A permissão é exata, incluindo a equivalência com `www.`; subdomínios adicionais precisam estar cadastrados em `users.dominio` ou `users.dominio_externo`.

O serviço `AffiliateDomainResolver` normaliza os hosts e guarda o ID do afiliado ou um resultado negativo por 60 segundos no cache configurado. O usuário é carregado uma vez por requisição. O observer de `User` normaliza os domínios alterados e invalida os domínios antigos e novos após o commit, incluindo criação e exclusão. Importações via Eloquent também disparam esses eventos. Alterações por SQL direto, `insert`, `upsert` ou atualizações em massa sem eventos dependem da expiração do cache.

As páginas 403, 404 e 500 mantêm o status de erro, sem redirecionamentos. Redirecionamentos GET/HEAD para a própria URL recebem HTTP 500 e geram o aviso `Blocked a web redirect to the current URL.` no log, sem registrar parâmetros de consulta. POST seguido de redirecionamento GET para a mesma página permanece permitido.

## Publicação

Após publicar a branch `portalje2`, execute no servidor, na pasta do projeto:

```sh
php artisan config:cache
php artisan route:cache
php artisan view:clear
```

Esses comandos atualizam a configuração e as rotas e removem as views compiladas antigas. Não é necessário executar migrations, `cache:clear` ou `optimize:clear` para esta alteração. Se houver múltiplos servidores, todos devem usar o mesmo cache compartilhado para que a invalidação seja imediata em todos eles.

Valide com requisições GET (sem seguir redirecionamentos inicialmente):

```sh
curl -sS -D - -o /dev/null https://as.jemp.me/
curl -sS -D - -o /dev/null https://qualificaparatodos.jemp.me/
curl -sS -L --max-redirs 5 -o /dev/null -w '%{http_code} %{url_effective}\n' https://as.jemp.me/
```

Se `as.jemp.me` continuar sem cadastro, a primeira resposta deve ser 302 com `Location: https://portalje.org/` e `no-store`; a cadeia deve terminar na página do portal. O domínio cadastrado deve continuar exibindo seu site. Cadastre e depois altere um domínio pelo painel para confirmar que o novo endereço funciona e o antigo redireciona imediatamente, inclusive após uma consulta negativa anterior.

Se persistir um ciclo que não aparece nos testes da aplicação, confira as regras de redirecionamento e cache do servidor e da Cloudflare.
