<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AffiliateDomainResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AffiliateDomainResolutionTest extends TestCase
{
    use RefreshDatabase;

    private const PAGE = '/__domain/test/page';

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get(self::PAGE, function () {
            $resolver = app(AffiliateDomainResolver::class);
            $user = $resolver->resolve(request()->getHost());
            $again = $resolver->resolve(request()->getHost());

            return response()->json(['user_id' => $user?->id, 'same_instance' => $user === $again]);
        })->name('domain-test');

        Route::middleware('web')->match(['GET', 'HEAD', 'POST'], '/__domain/test/redirect',
            fn () => redirect(request()->query('target', request()->fullUrl())));

        Route::middleware('web')->get('/__domain/test/error/{status}', fn (int $status) => abort($status));
        Route::middleware('api')->get('/__domain/test/api', fn () => response()->json(['ok' => true]));
    }

    public function test_unknown_hosts_redirect_before_rendering_or_loading_courses_on_all_web_routes(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        foreach (['/', '/cursos', '/w3', '/login', '/user/dashboard', '/lp/aprenda-uma-profissao', self::PAGE] as $path) {
            $this->get('https://as.jemp.me'.$path.'?ref=secret')
                ->assertStatus(302)
                ->assertRedirect('https://portalje.org/')
                ->assertHeader('Cache-Control', 'no-store, private');
        }

        $this->assertEmpty(array_filter($queries, fn ($sql) => str_contains($sql, 'from "curso"')));
        $this->assertCount(1, array_filter($queries, fn ($sql) => str_contains($sql, '"dominio" =')));
        $this->post('https://as.jemp.me/user/dominio')->assertRedirect('https://portalje.org/');
    }

    public function test_registered_domains_and_normalized_variants_reuse_the_same_user(): void
    {
        $user = User::factory()->create(['dominio' => 'afiliado.jemp.me', 'dominio_externo' => 'https://www.Externo.test:443/']);
        $this->assertSame('externo.test', $user->dominio_externo);

        foreach (['afiliado.jemp.me', 'www.afiliado.jemp.me', 'externo.test', 'WWW.EXTERNO.TEST.:8443'] as $host) {
            $this->get('https://'.$host.self::PAGE)
                ->assertOk()->assertJson(['user_id' => $user->id, 'same_instance' => true]);
        }
    }

    public function test_only_exact_official_domains_are_allowed_without_an_affiliate(): void
    {
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        foreach (['portalje.org', 'dns.portalje.org', 'jovemempreendedor.org', 'dns.jovemempreendedor.org',
            'jemp.me', $appHost] as $host) {
            $this->get('https://'.$host.self::PAGE)->assertOk()->assertJson(['user_id' => null]);
        }

        $this->get('https://afiliados.jovemempreendedor.org'.self::PAGE)
            ->assertRedirect('https://portalje.org/login');

        $this->get('https://unknown.portalje.org'.self::PAGE)->assertRedirect('https://portalje.org/');
    }

    public function test_api_and_health_check_do_not_require_a_registered_host(): void
    {
        $this->get('https://as.jemp.me/__domain/test/api')->assertOk()->assertJson(['ok' => true]);
        $this->get('https://as.jemp.me/up')->assertOk();
    }

    public function test_positive_and_negative_cache_expire_and_users_are_loaded_once_per_request(): void
    {
        $user = User::factory()->create(['dominio' => 'cached.test']);
        $domainQueries = 0;
        $userLoads = 0;
        DB::listen(function ($query) use (&$domainQueries, &$userLoads) {
            if (str_contains($query->sql, 'from "users"')) {
                str_contains($query->sql, '"dominio" =') ? $domainQueries++ : $userLoads++;
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $this->get('https://cached.test'.self::PAGE)->assertOk()->assertJson(['user_id' => $user->id]);
            $this->get('https://missing.test'.self::PAGE)->assertRedirect('https://portalje.org/');
        }
        $this->assertSame(2, $domainQueries);
        $this->assertSame(2, $userLoads);

        $this->travel(61)->seconds();
        $this->get('https://cached.test'.self::PAGE)->assertOk();
        $this->get('https://missing.test'.self::PAGE)->assertRedirect('https://portalje.org/');
        $this->assertSame(4, $domainQueries);
    }

    public function test_create_change_remove_and_delete_invalidate_old_and_new_domains(): void
    {
        $this->get('https://old.test'.self::PAGE)->assertRedirect('https://portalje.org/');
        $user = User::factory()->create(['dominio' => 'old.test', 'dominio_externo' => 'external.test']);
        $this->get('https://old.test'.self::PAGE)->assertOk();
        $this->get('https://external.test'.self::PAGE)->assertOk();
        $this->get('https://new.test'.self::PAGE)->assertRedirect('https://portalje.org/');

        $user->update(['dominio' => 'new.test', 'dominio_externo' => null]);
        $this->get('https://old.test'.self::PAGE)->assertRedirect('https://portalje.org/');
        $this->get('https://external.test'.self::PAGE)->assertRedirect('https://portalje.org/');
        $this->get('https://new.test'.self::PAGE)->assertOk();

        $user->delete();
        $this->get('https://new.test'.self::PAGE)->assertRedirect('https://portalje.org/');
    }

    public function test_cache_is_invalidated_only_after_a_domain_change_commits(): void
    {
        $resolver = app(AffiliateDomainResolver::class);
        $user = User::factory()->create(['dominio' => 'old.test']);
        $this->get('https://old.test'.self::PAGE)->assertOk();
        $this->get('https://new.test'.self::PAGE)->assertRedirect('https://portalje.org/');

        DB::beginTransaction();
        $user->update(['dominio' => 'new.test']);
        $this->assertTrue(Cache::has($resolver->cacheKey('old.test')));
        $this->assertTrue(Cache::has($resolver->cacheKey('new.test')));
        DB::commit();

        $this->assertFalse(Cache::has($resolver->cacheKey('old.test')));
        $this->assertFalse(Cache::has($resolver->cacheKey('new.test')));
        $this->get('https://new.test'.self::PAGE)->assertOk();
        $this->get('https://old.test'.self::PAGE)->assertRedirect('https://portalje.org/');
    }

    public function test_database_failures_are_not_cached_as_unknown_domains(): void
    {
        $resolver = app(AffiliateDomainResolver::class);
        DB::connection()->beforeExecuting(function (string $sql) {
            if (str_contains($sql, 'from "users"')) {
                throw new \RuntimeException('Database unavailable');
            }
        });

        try {
            $resolver->resolve('failure.test');
            $this->fail('Database errors must propagate.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Database unavailable', $e->getMessage());
        }
        $this->assertFalse(Cache::has($resolver->cacheKey('failure.test')));
    }

    public function test_rolling_back_a_domain_change_preserves_the_cached_mapping(): void
    {
        $resolver = app(AffiliateDomainResolver::class);
        $user = User::factory()->create(['dominio' => 'old.test']);
        $this->get('https://old.test'.self::PAGE)->assertOk();
        $this->get('https://new.test'.self::PAGE)->assertRedirect('https://portalje.org/');

        DB::beginTransaction();
        $user->update(['dominio' => 'new.test']);
        DB::rollBack();

        $this->assertSame($user->id, Cache::get($resolver->cacheKey('old.test')));
        $this->assertSame(0, Cache::get($resolver->cacheKey('new.test')));
        $this->get('https://old.test'.self::PAGE)->assertOk();
        $this->get('https://new.test'.self::PAGE)->assertRedirect('https://portalje.org/');
    }

    public function test_redirect_comparison_handles_relative_urls_fragments_ports_and_root_slashes(): void
    {
        $middleware = app(\App\Http\Middleware\ResolveAffiliateDomain::class);
        foreach ([
            ['https://portalje.org', 'https://PORTALJE.ORG:443/#fragment'],
            ['https://portalje.org/a/b?x=1', 'b?x=1#fragment'],
            ['https://portalje.org/a/b?x=1', '?x=1'],
            ['https://portalje.org/a/b?x=1', '//portalje.org/a/b?x=1'],
        ] as [$source, $target]) {
            $request = \Illuminate\Http\Request::create($source);
            $response = $middleware->handle($request, fn () => new \Symfony\Component\HttpFoundation\RedirectResponse($target));
            $this->assertSame(500, $response->getStatusCode(), $source.' -> '.$target);
            $this->assertFalse($response->headers->has('Location'));
        }
    }

    public function test_error_pages_preserve_their_status_and_never_redirect(): void
    {
        foreach ([403, 404, 500] as $status) {
            $this->get('https://portalje.org/__domain/test/error/'.$status)
                ->assertStatus($status)->assertHeaderMissing('Location');
        }

        Route::getRoutes()->getByName('nova_home')->uses(fn () => abort(500));
        $this->get('https://portalje.org/')->assertStatus(500)->assertHeaderMissing('Location');
    }

    public function test_self_redirects_are_blocked_for_get_and_head_without_logging_query_values(): void
    {
        Log::spy();
        $url = 'https://portalje.org/__domain/test/redirect?token=secret';
        $this->get($url)->assertStatus(500)->assertHeaderMissing('Location');
        $this->head($url)->assertStatus(500)->assertHeaderMissing('Location');
        Log::shouldHaveReceived('warning')->twice()->with('Blocked a web redirect to the current URL.', [
            'host' => 'portalje.org', 'route' => null, 'status' => 302,
        ]);
    }

    public function test_post_to_get_redirects_and_redirects_to_other_urls_are_preserved(): void
    {
        $url = 'https://portalje.org/__domain/test/redirect';
        $this->post($url)->assertRedirect($url);
        foreach (['/login', 'https://jovemempreendedor.org/', 'http://portalje.org/', '?different=1'] as $target) {
            $this->get($url.'?target='.urlencode($target))->assertStatus(302);
        }
    }
}
