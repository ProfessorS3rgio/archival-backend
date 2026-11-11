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
    Auth::shouldUse('web');
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
        $record = $class::withoutGlobalScopes()->findOrFail($id);
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
    check($response->getStatusCode() < 400 && ! $class::withoutGlobalScopes()->find($id) && ! isset($objects[$path]), "Staff can delete their own {$route} record and file");

    [$id, $path] = $make($other);
    $response = removeRecord('/'.$route.'/'.$id, $users['staff']);
    check($response->getStatusCode() >= 400 && $class::withoutGlobalScopes()->find($id) && isset($objects[$path]), "Staff cannot delete another creator's {$route} record or file");
    foreach (['legislator', 'user'] as $role) {
        [$deniedId, $deniedPath] = $make($users[$role]);
        $response = removeRecord('/'.$route.'/'.$deniedId, $users[$role]);
        check($response->getStatusCode() >= 400 && $class::withoutGlobalScopes()->find($deniedId) && isset($objects[$deniedPath]), "{$role} cannot delete even their own {$route} record");
    }
    $response = removeRecord('/'.$route.'/'.$id, null);
    check($response->getStatusCode() === 401 && $class::withoutGlobalScopes()->find($id), "Guests cannot delete {$route} records");
    $response = removeRecord('/'.$route.'/'.$id, $users['admin']);
    check($response->getStatusCode() < 400 && ! $class::withoutGlobalScopes()->find($id) && ! isset($objects[$path]), "Admins retain deletion access to {$route}");
}

function accessRequest(string $uri, ?User $actor, string $method = 'GET', array $data = [], ?string $token = null)
{
    global $app, $kernel;
    $app['session']->driver()->flush();
    Auth::forgetGuards();
    Auth::shouldUse('web');
    if ($actor) Auth::guard('web')->setUser($actor);
    return $kernel->handle(Request::create($uri, $method, $data, [], [], ['HTTP_ACCEPT' => 'application/json', 'HTTP_REFERER' => 'http://localhost/dashboard', 'HTTP_AUTHORIZATION' => $token ? 'Bearer '.$token : '']));
}
$disk->shouldReceive('response')->andReturnUsing(fn () => response('fixture file', 200));
$staff = $users['staff'];
$ownRecords = [];
$downloadTypes = [
    'ordinances' => 'ordinance', 'resolutions' => 'resolution',
    'draft-ordinances' => 'draft-ordinance', 'draft-resolutions' => 'draft-resolution',
    'accomplishments' => 'accomplishment', 'projects' => 'project',
    'memorandum-of-agreements' => 'memorandum-of-agreement', 'committee-reports' => 'committee-report',
    'municipal-communications' => 'municipal-communication',
    'national-office-communications' => 'national-office-communication', 'provincial-offices' => 'provincial-office',
];
foreach ($types as $route => $class) {
    $model = new $class;
    $ownerField = $class === App\Models\Accomplishment::class ? 'uploaded_by' : 'added_by';
    $titleField = $class === App\Models\Project::class ? 'project_name' : 'title';
    foreach (['own' => $staff, 'foreign' => $other] as $kind => $actor) {
        $attributes = [$ownerField => $actor->id, $titleField => $kind.'-private-'.$route];
        if (Schema::hasColumn($model->getTable(), 'author')) $attributes['author'] = $kind.'-author';
        $id = DB::table($model->getTable())->insertGetId($attributes);
        $record = $class::withoutGlobalScopes()->findOrFail($id);
        $path = 'access/'.$kind.'-'.$route.'.pdf';
        $objects[$path] = true;
        if (method_exists($record, 'files')) {
            $file = $record->files()->create(['file_url' => $path, 'file_name' => 'fixture.pdf', 'uploaded_by' => $actor->id]);
            $downloadIds[$kind] = $file->getKey();
        } else {
            DB::table($model->getTable())->where($model->getKeyName(), $id)->update(['file_url' => $path]);
        }
        $ids[$kind] = $id;
        if (! method_exists($record, 'files')) $downloadIds[$kind] = $id;
    }
    $ownRecords[$route] = $ids['own'];
    check(accessRequest('/download/'.$downloadTypes[$route].'/'.$downloadIds['own'], $staff)->isSuccessful(), "Staff can download their own {$route} file");
    check(accessRequest('/download/'.$downloadTypes[$route].'/'.$downloadIds['foreign'], $staff)->isSuccessful(), "Staff can download another creator's {$route} file");
    check(accessRequest('/'.$route.'/'.$ids['own'].'/edit', $staff)->isSuccessful(), "Staff can open their own {$route} edit form");
    $response = accessRequest('/'.$route, $staff);
    check($response->isSuccessful() && str_contains($response->getContent(), 'own-private-'.$route) && str_contains($response->getContent(), 'foreign-private-'.$route), "Staff {$route} listing includes all records");
    check(accessRequest('/'.$route.'/'.$ids['own'], $staff)->isSuccessful(), "Staff can view their own {$route} record");
    foreach (['', '/edit'] as $suffix) {
        check(accessRequest('/'.$route.'/'.$ids['foreign'].$suffix, $staff)->isSuccessful(), "Staff can open another creator's {$route}{$suffix} URL");
    }
    $response = accessRequest('/'.$route.'/'.$ids['foreign'], $staff, 'PUT', [$titleField => 'Tampered']);
    check($response->getStatusCode() >= 400 && $class::withoutGlobalScopes()->find($ids['foreign'])->{$titleField} === 'foreign-private-'.$route, "Staff cannot update another creator's {$route} record");
    check(accessRequest('/serve-file/access/foreign-'.$route.'.pdf', $staff)->isSuccessful(), "Staff can stream another creator's {$route} file directly");
    check(accessRequest('/serve-file/access/own-'.$route.'.pdf', $staff)->isSuccessful(), "Staff can stream their own {$route} file");
    check(accessRequest('/'.$route.'/create', $staff)->isSuccessful(), "Staff retain {$route} create-form access");
}
foreach (['/settings', '/settings/manage-accounts', '/settings/add-accounts', '/sp-terms', '/system-logs', '/system-logs/1', '/log-viewer', '/log-viewer/api/logs', '/debug-user', '/sp-1st/officials'] as $uri) {
    check(! accessRequest($uri, $staff)->isSuccessful(), "Staff cannot enter administrative page {$uri}");
}
foreach (['/profile', '/settings/change-password'] as $uri) {
    check(accessRequest($uri, $staff)->isSuccessful(), "Staff retain account access to {$uri}");
}
$response = accessRequest('/dashboard', $staff);
check($response->isSuccessful() && str_contains($response->getContent(), 'foreign-private-'), 'Staff dashboard includes other creators records for review');
$html = $response->getContent();
preg_match('/data-page="([^"]+)"/', $html, $match);
$page = json_decode(html_entity_decode($match[1] ?? '', ENT_QUOTES), true);
check(isset($page['props']) && $page['props']['recentSystemLogs'] === [] && $page['props']['spTerms'] === [], 'Staff dashboard receives no logs or SP management data');
check($page['props']['stats']['nationalOfficeCommunications'] === 4 && $page['props']['stats']['totalFiles'] === 0, 'Staff dashboard statistics include all visible records and file metadata');
check(in_array('foreign-author', $page['props']['availableAuthors'], true), 'Staff author filters include other creators data');
foreach (['/api/v1/Documents/projects', '/api/v1/documents/projects'] as $uri) {
    check(accessRequest($uri, null)->getStatusCode() === 401, 'Anonymous APIs cannot bypass Staff record restrictions');
    $response = accessRequest($uri, $staff);
    check($response->isSuccessful() && str_contains($response->getContent(), 'own-private-projects') && str_contains($response->getContent(), 'foreign-private-projects'), 'Authenticated Staff APIs can review all records');
}
$readToken = $staff->createToken('staff-read-test', ['documents:read'])->plainTextToken;
$response = accessRequest('/api/v1/Documents/projects', null, 'GET', [], $readToken);
check($response->isSuccessful() && str_contains($response->getContent(), 'own-private-projects') && str_contains($response->getContent(), 'foreign-private-projects'), 'Bearer-token Staff access can review all records');
$wrongToken = $staff->createToken('staff-wrong-ability', ['other:read'])->plainTextToken;
check(accessRequest('/api/v1/Documents/projects', null, 'GET', [], $wrongToken)->getStatusCode() === 403, 'Document API rejects bearer tokens without the read ability');
$response = accessRequest('/projects', $users['admin']);
check($response->isSuccessful() && str_contains($response->getContent(), 'foreign-private-projects'), 'Administrators still see all creators records');
$response = accessRequest('/projects', $staff, 'POST', ['project_name' => 'Staff created', 'added_by' => $other->id]);
check($response->isSuccessful() && App\Models\Project::where('project_name', 'Staff created')->first()->added_by === $staff->id, 'Staff can create records and cannot forge their creator ID');
foreach (['ordinances', 'resolutions', 'draft-ordinances', 'draft-resolutions'] as $route) {
    $response = accessRequest('/'.$route, $staff, 'POST', ['title' => 'Staff-owned new '.$route, 'added_by' => $other->id]);
    $class = $types[$route];
    check($response->getStatusCode() < 400 && $class::where('title', 'Staff-owned new '.$route)->first()?->added_by === $staff->id, "Staff {$route} creation uses their authenticated identity");
}
$beforeSettings = App\Models\Setting::all()->toArray();
$response = accessRequest('/settings/auto-fill', $staff, 'PATCH', ['auto_fill' => false]);
check($response->isSuccessful() && App\Models\Setting::all()->toArray() === $beforeSettings, 'Staff document auto-fill preference cannot change system-wide settings');
Mockery::close();
