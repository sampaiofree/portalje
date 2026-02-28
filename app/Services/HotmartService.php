<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class HotmartService
{
    protected string $apiBaseUrl;
    protected string $authBaseUrl;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $basicToken;
    protected int $timeout;

    protected ?string $accessToken = null;
    protected ?Carbon $accessTokenExpiresAt = null;
    protected array $lastTokenPayload = [];

    /** Objetivo: Configurar credenciais e URLs base para autenticacao e chamadas da API Hotmart. */
    public function __construct(
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $basicToken = null,
        ?string $apiBaseUrl = null,
        ?string $authBaseUrl = null,
        ?int $timeout = null
    ) {
        $this->clientId = $clientId ?? config('services.hotmart.client_id');
        $this->clientSecret = $clientSecret ?? config('services.hotmart.client_secret');
        $this->basicToken = $basicToken ?? config('services.hotmart.basic_token');
        $this->apiBaseUrl = rtrim($apiBaseUrl ?? config('services.hotmart.api_base_url', 'https://developers.hotmart.com'), '/');
        $this->authBaseUrl = rtrim($authBaseUrl ?? config('services.hotmart.auth_base_url', 'https://api-sec-vlc.hotmart.com'), '/');
        $this->timeout = (int) ($timeout ?? config('services.hotmart.timeout', 30));
    }

    /** Objetivo: Gerar ou reutilizar o access token OAuth da Hotmart. */
    public function getToken(bool $forceRefresh = false): array
    {
        if (!$forceRefresh && !$this->tokenExpired() && $this->lastTokenPayload !== []) {
            return $this->lastTokenPayload;
        }

        $query = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->getRequiredClientId(),
            'client_secret' => $this->getRequiredClientSecret(),
        ];

        $url = $this->buildUrl($this->authBaseUrl, '/security/oauth/token') . '?' . http_build_query($query);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $this->resolveBasicToken(),
        ])->timeout($this->timeout)->post($url, []);

        $payload = $this->decodeResponse($response);

        if (!$response->successful() || empty($payload['access_token'])) {
            return $payload;
        }

        $this->accessToken = $payload['access_token'];
        $this->lastTokenPayload = $payload;

        $ttl = isset($payload['expires_in']) ? (int) $payload['expires_in'] : 0;
        $safeTtl = max($ttl - 60, 30);
        $this->accessTokenExpiresAt = now()->addSeconds($safeTtl);

        return $payload;
    }

    /** Objetivo: Retornar apenas o access token atual, renovando quando necessario. */
    public function getAccessToken(bool $forceRefresh = false): ?string
    {
        if ($forceRefresh || $this->tokenExpired()) {
            $payload = $this->getToken(true);
            return $payload['access_token'] ?? null;
        }

        return $this->accessToken;
    }

    /** Objetivo: Executar uma requisicao autenticada para qualquer endpoint da Hotmart. */
    public function request(
        string $method,
        string $path,
        array $query = [],
        array $body = [],
        array $headers = []
    ): array {
        $response = $this->sendAuthenticatedRequest($method, $path, $query, $body, $headers);

        if ($response->status() === 401) {
            $this->getToken(true);
            $response = $this->sendAuthenticatedRequest($method, $path, $query, $body, $headers);
        }

        return $this->decodeResponse($response);
    }

    /** Objetivo: Fazer requisicoes GET autenticadas. */
    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->request('GET', $path, $query, [], $headers);
    }

    /** Objetivo: Fazer requisicoes POST autenticadas. */
    public function post(string $path, array $body = [], array $query = [], array $headers = []): array
    {
        return $this->request('POST', $path, $query, $body, $headers);
    }

    /** Objetivo: Fazer requisicoes PUT autenticadas. */
    public function put(string $path, array $body = [], array $query = [], array $headers = []): array
    {
        return $this->request('PUT', $path, $query, $body, $headers);
    }

    /** Objetivo: Fazer requisicoes PATCH autenticadas. */
    public function patch(string $path, array $body = [], array $query = [], array $headers = []): array
    {
        return $this->request('PATCH', $path, $query, $body, $headers);
    }

    /** Objetivo: Fazer requisicoes DELETE autenticadas. */
    public function delete(string $path, array $body = [], array $query = [], array $headers = []): array
    {
        return $this->request('DELETE', $path, $query, $body, $headers);
    }

    /** Objetivo: Enviar a requisicao HTTP com Bearer token e payload padronizado. */
    private function sendAuthenticatedRequest(
        string $method,
        string $path,
        array $query,
        array $body,
        array $headers
    ): Response {
        $accessToken = $this->getAccessToken();

        if (empty($accessToken)) {
            throw new InvalidArgumentException('Unable to get Hotmart access token. Check credentials.');
        }

        $requestHeaders = array_merge([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ], $headers);

        $url = $this->resolveApiUrl($path);
        $httpMethod = strtoupper($method);
        $options = [];

        if ($query !== []) {
            $options['query'] = $query;
        }

        if (!in_array($httpMethod, ['GET', 'HEAD'], true)) {
            $options['json'] = $body === [] ? (object) [] : $body;
        }

        return $this->client($requestHeaders)->send($httpMethod, $url, $options);
    }

    /** Objetivo: Montar o cliente HTTP base com timeout configurado. */
    private function client(array $headers = []): PendingRequest
    {
        return Http::withHeaders($headers)->timeout($this->timeout);
    }

    /** Objetivo: Resolver se o caminho recebido e absoluto ou relativo ao dominio da API. */
    private function resolveApiUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $this->buildUrl($this->apiBaseUrl, $path);
    }

    /** Objetivo: Concatenar URL base e caminho garantindo barras corretas. */
    private function buildUrl(string $base, string $path): string
    {
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    /** Objetivo: Resolver o token Basic usando env direto ou client_id/client_secret. */
    private function resolveBasicToken(): string
    {
        if (!empty($this->basicToken)) {
            $basicToken = trim($this->basicToken);

            if (str_starts_with(strtolower($basicToken), 'basic ')) {
                return trim(substr($basicToken, 6));
            }

            return $basicToken;
        }

        return base64_encode($this->getRequiredClientId() . ':' . $this->getRequiredClientSecret());
    }

    /** Objetivo: Garantir que o client_id obrigatorio foi informado. */
    private function getRequiredClientId(): string
    {
        if (empty($this->clientId)) {
            throw new InvalidArgumentException('HOTMART_CLIENT_ID is required.');
        }

        return $this->clientId;
    }

    /** Objetivo: Garantir que o client_secret obrigatorio foi informado. */
    private function getRequiredClientSecret(): string
    {
        if (empty($this->clientSecret)) {
            throw new InvalidArgumentException('HOTMART_CLIENT_SECRET is required.');
        }

        return $this->clientSecret;
    }

    /** Objetivo: Verificar se o token atual nao existe ou esta expirado. */
    private function tokenExpired(): bool
    {
        return empty($this->accessToken)
            || $this->accessTokenExpiresAt === null
            || now()->greaterThanOrEqualTo($this->accessTokenExpiresAt);
    }

    /** Objetivo: Converter a resposta HTTP para array padronizado. */
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
}
