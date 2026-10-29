<?php

// Run one scenario per process: php tests/Performance/benchmark-pricing.php api
// A disposable copy of the local SQLite database is used; production data is not required.
$root = dirname(__DIR__, 2);
$source = getenv('BENCHMARK_SQLITE_SOURCE') ?: $root . '/database/database.sqlite';
$scenario = $argv[1] ?? '';
if (!in_array($scenario, ['page', 'api', 'catalog', 'catalog2', 'panel'], true) || !is_file($source)) {
    fwrite(STDERR, "Specify page, api, catalog, catalog2 or panel and an existing SQLite source.\n");
    exit(1);
}
$db = tempnam(sys_get_temp_dir(), 'portalje-bench-');
if ($db === false || !copy($source, $db)) {
    throw new RuntimeException('Could not create temporary database.');
}
register_shutdown_function(static function () use ($db): void {
    foreach ([$db, $db . '-wal', $db . '-shm', $db . '-journal'] as $file) {
        if (is_file($file)) unlink($file);
    }
});
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . $db);
putenv('SESSION_DRIVER=array');
putenv('CACHE_STORE=array');
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$connection = Illuminate\Support\Facades\DB::connection();
for ($i = 0; $i < 100; $i++) {
    $connection->table('cupons')->insert(['codigo' => 'BENCH' . $i, 'desconto' => $i % 70]);
}
$course = $connection->table('curso')->where('publicado', '1')->first();
$user = App\Models\User::where('nivel_acesso', 'user')->first();
if (!$course || !$user) throw new RuntimeException('The fixture needs a published course and an affiliate user.');
$couponId = $connection->table('cupons')->where('codigo', 'BENCH1')->value('id');
$connection->table('root_domain_course_configs')->updateOrInsert(['curso_id' => $course->id], [
    'mostrar_curso' => 1, 'formulario_pre_checkout' => 1, 'modo_precos' => 'um_preco', 'cupom_principal_id' => $couponId,
]);
$urls = [
    'page' => 'http://portalje.org/' . $course->url,
    'api' => 'http://portalje.org/api/consultar/cursos',
    'catalog' => 'http://portalje.org/afiliado/catalogo',
    'catalog2' => 'http://portalje.org/afiliado/catalogo2',
    'panel' => 'http://portalje.org/user/afiliados_cadastrar_curso',
];
if ($scenario === 'panel') {
    // Isolate rendering cost from authentication and onboarding middleware.
    Illuminate\Support\Facades\Auth::guard('web')->setUser($user);
    $app->instance('middleware.disable', true);
    Illuminate\Support\Facades\View::share('errors', new Illuminate\Support\ViewErrorBag());
}
$courses = $connection->table('curso')->count();
$coupons = $connection->table('cupons')->count();
$queryCount = 0;
$connection->listen(static function () use (&$queryCount): void { $queryCount++; });
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create($urls[$scenario], 'GET');
if (function_exists('memory_reset_peak_usage')) memory_reset_peak_usage();
$start = hrtime(true);
$response = $kernel->handle($request);
$content = $response->getContent();
if ($output = getenv('BENCHMARK_RESPONSE_PATH')) file_put_contents($output, $content);
echo json_encode([
    'scenario' => $scenario, 'status' => $response->getStatusCode(), 'queries' => $queryCount,
    'ms' => round((hrtime(true) - $start) / 1e6, 2), 'peak_bytes' => memory_get_peak_usage(true),
    'response_bytes' => strlen($content), 'courses' => $courses, 'coupons' => $coupons,
], JSON_THROW_ON_ERROR) . PHP_EOL;
