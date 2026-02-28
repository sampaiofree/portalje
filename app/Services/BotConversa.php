<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class BotConversa
{
    protected string $baseUrl;
    protected string $basePath;
    protected ?string $apiKey;
    protected int $timeout;

    /** Objetivo: Configurar URL base, path da API, chave e timeout da integracao com BotConversa. */
    public function __construct(
        ?string $apiKey = null,
        ?string $baseUrl = null,
        ?string $basePath = null,
        ?int $timeout = null
    )
    {
        $this->apiKey = $apiKey ?? env('BOTCONVERSA_API_KEY', env('BOTCONVERSA_TOKEN'));

        $configuredBaseUrl = $baseUrl ?? env('BOTCONVERSA_BASE_URL') ?? env('BOTCONVERSA_URL') ?? 'https://backend.botconversa.com.br';
        $configuredBasePath = $basePath ?? env('BOTCONVERSA_BASE_PATH');

        [$this->baseUrl, $pathFromUrl] = $this->splitBaseUrlAndPath($configuredBaseUrl);

        if ($configuredBasePath === null || $configuredBasePath === '') {
            $configuredBasePath = $pathFromUrl !== '' ? $pathFromUrl : '/api/v1/webhook';
        }

        $this->basePath = '/' . trim($configuredBasePath, '/');
        $this->timeout = (int) ($timeout ?? env('BOTCONVERSA_TIMEOUT', 30));
    }

    /** Objetivo: Enviar um payload para um webhook externo especifico. */
    public function enviar_webhook(string $webhook, array $dados): array
    {
        $response = Http::timeout($this->timeout)->post($webhook, $dados);

        return $this->decodeResponse($response);
    }

    /** Objetivo: Listar os campos de bot disponiveis. */
    public function bot_fields_list(): array
    {
        return $this->requestJson('GET', '/bot_fields/');
    }

    /** Objetivo: Definir valor para um campo de bot pelo ID. */
    public function bot_fields_create(int|string $bot_variable_id, array $data): array
    {
        return $this->requestJson(
            'POST',
            '/bot_fields/{bot_variable_id}/',
            ['bot_variable_id' => $bot_variable_id],
            $data
        );
    }

    /** Objetivo: Listar campanhas cadastradas. */
    public function campaigns_list(): array
    {
        return $this->requestJson('GET', '/campaigns/');
    }

    /** Objetivo: Criar uma nova campanha. */
    public function campaigns_create_create(array $data): array
    {
        return $this->requestJson('POST', '/campaigns/create/', [], $data);
    }

    /** Objetivo: Buscar detalhes de uma campanha especifica. */
    public function campaigns_read(int|string $id): array
    {
        return $this->requestJson('GET', '/campaigns/{id}/', ['id' => $id]);
    }

    /** Objetivo: Excluir uma campanha pelo ID. */
    public function campaigns_delete(int|string $id, array $data = []): array
    {
        return $this->requestJson('DELETE', '/campaigns/{id}/', ['id' => $id], $data);
    }

    /** Objetivo: Listar os custom fields disponiveis. */
    public function custom_fields_list(): array
    {
        return $this->requestJson('GET', '/custom_fields/');
    }

    /** Objetivo: Listar fluxos cadastrados. */
    public function flows_list(): array
    {
        return $this->requestJson('GET', '/flows/');
    }

    /** Objetivo: Listar gerentes da conta. */
    public function managers_list(): array
    {
        return $this->requestJson('GET', '/managers/');
    }

    /** Objetivo: Criar um gerente com permissoes na conta. */
    public function managers_create(array $data): array
    {
        return $this->requestJson('POST', '/managers/', [], $data);
    }

    /** Objetivo: Buscar detalhes de um gerente por ID. */
    public function managers_read(int|string $id): array
    {
        return $this->requestJson('GET', '/managers/{id}/', ['id' => $id]);
    }

    /** Objetivo: Atualizar parcialmente dados de um gerente. */
    public function managers_partial_update(int|string $id, array $data): array
    {
        return $this->requestJson('PATCH', '/managers/{id}/', ['id' => $id], $data);
    }

    /** Objetivo: Remover um gerente da conta. */
    public function managers_delete(int|string $id, array $data = []): array
    {
        return $this->requestJson('DELETE', '/managers/{id}/', ['id' => $id], $data);
    }

    /** Objetivo: Listar sequencias de automacao. */
    public function sequences_list(): array
    {
        return $this->requestJson('GET', '/sequences/');
    }

    /** Objetivo: Criar um novo subscriber. */
    public function subscriber_create(array $data): array
    {
        return $this->requestJson('POST', '/subscriber/', [], $data);
    }

    /** Objetivo: Buscar subscriber pelo numero de telefone. */
    public function subscriber_get_by_phone_read(string $phone): array
    {
        return $this->requestJson(
            'GET',
            '/subscriber/get_by_phone/{phone}/',
            ['phone' => $phone]
        );
    }

    /** Objetivo: Adicionar subscriber a uma campanha. */
    public function subscriber_campaigns_create(
        int|string $subscriber_id,
        int|string $campaign_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/campaigns/{campaign_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'campaign_id' => $campaign_id,
            ],
            $data
        );
    }

    /** Objetivo: Remover subscriber de uma campanha. */
    public function subscriber_campaigns_delete(
        int|string $subscriber_id,
        int|string $campaign_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'DELETE',
            '/subscriber/{subscriber_id}/campaigns/{campaign_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'campaign_id' => $campaign_id,
            ],
            $data
        );
    }

    /** Objetivo: Abrir ou fechar conversa de um subscriber. */
    public function subscriber_change_conversation_status_create(
        int|string $subscriber_id,
        array $data
    ): array {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/change_conversation_status/',
            ['subscriber_id' => $subscriber_id],
            $data
        );
    }

    /** Objetivo: Definir valor de custom field para um subscriber. */
    public function subscriber_custom_fields_create(
        int|string $subscriber_id,
        int|string $custom_field_id,
        array $data
    ): array {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/custom_fields/{custom_field_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'custom_field_id' => $custom_field_id,
            ],
            $data
        );
    }

    /** Objetivo: Limpar custom field de um subscriber. */
    public function subscriber_custom_fields_delete(
        int|string $subscriber_id,
        int|string $custom_field_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'DELETE',
            '/subscriber/{subscriber_id}/custom_fields/{custom_field_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'custom_field_id' => $custom_field_id,
            ],
            $data
        );
    }

    /** Objetivo: Excluir subscriber da base. */
    public function subscriber_delete_delete(int|string $subscriber_id, array $data = []): array
    {
        return $this->requestJson(
            'DELETE',
            '/subscriber/{subscriber_id}/delete/',
            ['subscriber_id' => $subscriber_id],
            $data
        );
    }

    /** Objetivo: Enviar fluxo para um subscriber. */
    public function subscriber_send_flow_create(int|string $subscriber_id, array $data): array
    {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/send_flow/',
            ['subscriber_id' => $subscriber_id],
            $data
        );
    }

    /** Objetivo: Enviar mensagem para um subscriber. */
    public function subscriber_send_message_create(int|string $subscriber_id, array $data): array
    {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/send_message/',
            ['subscriber_id' => $subscriber_id],
            $data
        );
    }

    /** Objetivo: Adicionar subscriber a uma sequencia. */
    public function subscriber_sequences_create(
        int|string $subscriber_id,
        int|string $sequence_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/sequences/{sequence_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'sequence_id' => $sequence_id,
            ],
            $data
        );
    }

    /** Objetivo: Remover subscriber de uma sequencia. */
    public function subscriber_sequences_delete(
        int|string $subscriber_id,
        int|string $sequence_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'DELETE',
            '/subscriber/{subscriber_id}/sequences/{sequence_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'sequence_id' => $sequence_id,
            ],
            $data
        );
    }

    /** Objetivo: Adicionar tag a um subscriber. */
    public function subscriber_tags_create(
        int|string $subscriber_id,
        int|string $tag_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'POST',
            '/subscriber/{subscriber_id}/tags/{tag_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'tag_id' => $tag_id,
            ],
            $data
        );
    }

    /** Objetivo: Remover tag de um subscriber. */
    public function subscriber_tags_delete(
        int|string $subscriber_id,
        int|string $tag_id,
        array $data = []
    ): array {
        return $this->requestJson(
            'DELETE',
            '/subscriber/{subscriber_id}/tags/{tag_id}/',
            [
                'subscriber_id' => $subscriber_id,
                'tag_id' => $tag_id,
            ],
            $data
        );
    }

    /** Objetivo: Listar subscribers com suporte a query string. */
    public function subscribers_list(array $query = []): array
    {
        return $this->requestJson('GET', '/subscribers/', [], [], $query);
    }

    /** Objetivo: Listar tags cadastradas. */
    public function tags_list(): array
    {
        return $this->requestJson('GET', '/tags/');
    }

    /** Objetivo: Executar request e retornar resposta em array padrao. */
    private function requestJson(
        string $method,
        string $path,
        array $routeParams = [],
        array $body = [],
        array $query = []
    ): array {
        $response = $this->request($method, $path, $routeParams, $body, $query);

        return $this->decodeResponse($response);
    }

    /** Objetivo: Montar e enviar requisicao HTTP para a API BotConversa. */
    private function request(
        string $method,
        string $path,
        array $routeParams = [],
        array $body = [],
        array $query = []
    ): Response {
        $url = $this->buildApiUrl($path, $routeParams);
        $options = [];

        if ($query !== []) {
            $options['query'] = $query;
        }

        $upperMethod = strtoupper($method);

        if (!in_array($upperMethod, ['GET', 'HEAD'], true)) {
            $options['json'] = $body === [] ? (object) [] : $body;
        }

        return $this->client()->send($upperMethod, $url, $options);
    }

    /** Objetivo: Criar cliente HTTP com headers padrao e API-KEY. */
    private function client(): PendingRequest
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if (!empty($this->apiKey)) {
            $headers['API-KEY'] = $this->apiKey;
        }

        return Http::withHeaders($headers)->timeout($this->timeout);
    }

    /** Objetivo: Construir URL final da API com basePath e parametros de rota. */
    private function buildApiUrl(string $path, array $routeParams = []): string
    {
        $resolvedPath = '/' . ltrim($this->interpolatePath($path, $routeParams), '/');

        if ($this->basePath === '/') {
            return $this->baseUrl . $resolvedPath;
        }

        return $this->baseUrl . $this->basePath . $resolvedPath;
    }

    /** Objetivo: Substituir placeholders do path por valores reais e validar faltas. */
    private function interpolatePath(string $path, array $routeParams = []): string
    {
        foreach ($routeParams as $key => $value) {
            $path = str_replace('{' . $key . '}', rawurlencode((string) $value), $path);
        }

        if (preg_match('/\{[^}]+\}/', $path)) {
            throw new InvalidArgumentException("Missing route parameter for path: {$path}");
        }

        return $path;
    }

    /** Objetivo: Normalizar a resposta HTTP em formato array consistente. */
    private function decodeResponse(Response $response): array
    {
        $decoded = $response->json();

        if (is_array($decoded)) {
            return $decoded;
        }

        return [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
        ];
    }

    /** Objetivo: Separar a origem da URL e o path quando vierem juntos na configuracao. */
    private function splitBaseUrlAndPath(string $baseUrl): array
    {
        $parsed = parse_url($baseUrl);

        if ($parsed === false || !isset($parsed['scheme'], $parsed['host'])) {
            return [rtrim($baseUrl, '/'), ''];
        }

        $origin = $parsed['scheme'] . '://' . $parsed['host'];

        if (isset($parsed['port'])) {
            $origin .= ':' . $parsed['port'];
        }

        $path = $parsed['path'] ?? '';

        return [rtrim($origin, '/'), $path];
    }
}
