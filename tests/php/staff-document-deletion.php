<?php

// Uses SQLite in memory and a mocked private disk; no real records or files are deleted.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'session.driver' => 'array',
    'cache.default' => 'array',
    'logging.default' => 'null',
    'hashing.bcrypt.rounds' => 4,
]);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

set_exception_handler(function (Throwable $error): void {
    fwrite(STDERR, $error->getMessage()."\n".$error->getTraceAsString()."\n");
    exit(1);
});

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

foreach (glob(__DIR__.'/../../database/migrations/*create*.php') as $file) {
    (require $file)->up();
}
$users = [];
foreach (['staff', 'admin', 'legislator', 'user'] as $role) {
    $users[$role] = User::create(['name' => $role, 'username' => $role, 'email' => $role.'@example.test', 'role' => $role, 'password' => 'Test!Password123']);
}
$other = User::create(['name' => 'Other Staff', 'username' => 'other', 'email' => 'other@example.test', 'role' => 'staff', 'password' => 'Test!Password123']);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$objects = [];
$disk = Mockery::mock(Illuminate\Filesystem\FilesystemAdapter::class);
$disk->shouldReceive('exists')->andReturnUsing(function ($path) use (&$objects) {
    return isset($objects[$path]);
});
$disk->shouldReceive('delete')->andReturnUsing(function ($paths) use (&$objects) {
    foreach ((array) $paths as $path) {
        unset($objects[$path]);
    }
    return true;
});
Storage::set('private', $disk);

function removeRecord(string $uri, ?User $user): Symfony\Component\HttpFoundation\Response
{
    global $app, $kernel;
    $app['session']->driver()->flush();
    Auth::forgetGuards();
    if ($user) {
        Auth::guard('web')->setUser($user);
    }
    return $kernel->handle(Request::create($uri, 'DELETE', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'HTTP_REFERER' => 'http://localhost/dashboard']));
}

$types = [
    'ordinances' => App\Models\Ordinance::class,
    'resolutions' => App\Models\Resolution::class,
    'draft-ordinances' => App\Models\DraftOrdinance::class,
    'draft-resolutions' => App\Models\DraftResolution::class,
    'accomplishments' => App\Models\Accomplishment::class,
    'projects' => App\Models\Project::class,
    'memorandum-of-agreements' => App\Models\MemorandumOfAgreement::class,
    'committee-reports' => App\Models\CommitteeReport::class,
    'municipal-communications' => App\Models\MunicipalitiesCommunicationList::class,
    'national-office-communications' => App\Models\NationalOfficeCommunicationList::class,
    'provincial-offices' => App\Models\ProvincialOffice::class,
];

foreach ($types as $route => $class) {
    $model = new $class;
    $ownerField = $class === App\Models\Accomplishment::class ? 'uploaded_by' : 'added_by';
    $make = function (User $owner) use ($model, $class, $ownerField, $route, &$objects) {
        $id = DB::table($model->getTable())->insertGetId([$ownerField => $owner->id]);
        $record = $class::findOrFail($id);
        $path = $route.'/'.$id.'.pdf';
        $objects[$path] = true;
        if (method_exists($record, 'files')) {
            $record->files()->create(['file_url' => $path, 'file_name' => 'test.pdf', 'uploaded_by' => $owner->id]);
        } elseif (Schema::hasColumn($model->getTable(), 'file_url')) {
            DB::table($model->getTable())->where($model->getKeyName(), $id)->update(['file_url' => $path]);
        }
        return [$id, $path];
    };

    [$id, $path] = $make($users['staff']);
    $response = removeRecord('/'.$route.'/'.$id, $users['staff']);
    check($response->getStatusCode() < 400 && ! $class::find($id) && ! isset($objects[$path]), "Staff can delete their own {$route} record and file");

    [$id, $path] = $make($other);
    $response = removeRecord('/'.$route.'/'.$id, $users['staff']);
    check($response->getStatusCode() >= 400 && $class::find($id) && isset($objects[$path]), "Staff cannot delete another creator's {$route} record or file");
    foreach (['legislator', 'user'] as $role) {
        [$deniedId, $deniedPath] = $make($users[$role]);
        $response = removeRecord('/'.$route.'/'.$deniedId, $users[$role]);
        check($response->getStatusCode() >= 400 && $class::find($deniedId) && isset($objects[$deniedPath]), "{$role} cannot delete even their own {$route} record");
    }
    $response = removeRecord('/'.$route.'/'.$id, null);
    check($response->getStatusCode() === 401 && $class::find($id), "Guests cannot delete {$route} records");
    $response = removeRecord('/'.$route.'/'.$id, $users['admin']);
    check($response->getStatusCode() < 400 && ! $class::find($id) && ! isset($objects[$path]), "Admins retain deletion access to {$route}");
}
Mockery::close();
