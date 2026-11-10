<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
config([
    'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null,
    'session.driver' => 'array', 'cache.default' => 'array', 'logging.default' => 'null', 'hashing.bcrypt.rounds' => 4,
]);

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

set_exception_handler(function (Throwable $error): void { fwrite(STDERR, $error->getMessage()."\n"); exit(1); });
foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_15_100001_create_resolution_lists_table', '2025_10_15_200000_create_ordinance_lists_table', '2025_10_16_102709_create_settings_table', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}
$admin = User::create(['name' => 'Test Admin', 'email' => 'admin@example.test', 'username' => 'admin', 'password' => 'password', 'role' => 'admin']);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
function check(bool $condition, string $message): void
{
    if (! $condition) throw new RuntimeException($message);
    echo "PASS: {$message}\n";
}

foreach ([false, true] as $enabled) {
    Auth::guard('web')->setUser($admin);
    $response = $kernel->handle(Request::create('/settings/auto-fill', 'PATCH', ['auto_fill' => $enabled], [], [], ['HTTP_ACCEPT' => 'application/json']));
    check($response->isSuccessful() && json_decode($response->getContent(), true)['auto_fill'] === $enabled && Setting::getAutoFillEnabled() === $enabled, 'The auto-fill endpoint saves and confirms the requested preference');

    foreach (['ordinances', 'resolutions', 'draft-ordinances', 'draft-resolutions', 'municipal-ordinances', 'municipal-resolutions', 'municipal-communications'] as $type) {
        $request = Request::create('/'.$type.'/create', 'GET', [], [], [], ['HTTP_X_INERTIA' => 'true', 'HTTP_ACCEPT' => 'text/html']);
        $request->headers->set('X-Inertia-Version', app(App\Http\Middleware\HandleInertiaRequests::class)->version($request));
        $response = $kernel->handle($request);
        $page = json_decode($response->getContent(), true);
        check($response->isSuccessful() && ($page['props']['autoFillEnabled'] ?? null) === $enabled, "{$type} create form receives the saved auto-fill preference");
    }
}
