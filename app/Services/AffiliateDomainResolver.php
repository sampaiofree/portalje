<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AffiliateDomainResolver
{
    public const REQUEST_ATTRIBUTE = 'affiliate_domains';

    public const CACHE_SECONDS = 60;

    public function normalize(?string $domain): string
    {
        $domain = strtolower(trim((string) $domain));
        if ($domain === '') {
            return '';
        }

        $host = parse_url(str_contains($domain, '://') ? $domain : 'https://'.$domain, PHP_URL_HOST);

        return preg_replace('/^www\./', '', rtrim((string) $host, '.'));
    }

    public function isOfficial(string $host): bool
    {
        return in_array($this->normalize($host), array_filter([
            'portalje.org',
            'dns.portalje.org',
            'jovemempreendedor.org',
            'dns.jovemempreendedor.org',
            'jemp.me',
            'afiliados.jovemempreendedor.org',
            $this->normalize(config('app.url')),
        ]), true);
    }

    public function cacheKey(string $host): string
    {
        return 'affiliate-domain:v1:'.hash('sha256', $this->normalize($host));
    }

    public function resolve(string $host): ?User
    {
        $host = $this->normalize($host);
        if ($host === '' || $this->isOfficial($host)) {
            return null;
        }

        $request = request();
        $resolved = $request->attributes->get(self::REQUEST_ATTRIBUTE, []);
        if (array_key_exists($host, $resolved)) {
            return $resolved[$host];
        }

        // Zero is an explicit negative result; exceptions never enter the cache.
        $id = Cache::remember($this->cacheKey($host), self::CACHE_SECONDS, fn () => User::query()
            ->where('dominio', $host)
            ->orWhere('dominio_externo', $host)
            ->value('id') ?? 0);

        $user = $id ? User::find($id) : null;
        if ($id && ! $user) {
            Cache::forget($this->cacheKey($host));
        }

        $resolved[$host] = $user;
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $resolved);

        return $user;
    }

    public function forget(array $domains): void
    {
        $request = request();
        $resolved = $request->attributes->get(self::REQUEST_ATTRIBUTE, []);
        foreach (array_unique(array_map($this->normalize(...), $domains)) as $host) {
            if ($host !== '') {
                Cache::forget($this->cacheKey($host));
                unset($resolved[$host]);
            }
        }
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $resolved);
    }
}
