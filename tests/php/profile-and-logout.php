<?php

// Uses only an isolated in-memory database; never migrates the configured database.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
$pictureRoot = sys_get_temp_dir().'/archival-user-pictures-'.bin2hex(random_bytes(8));
register_shutdown_function(function () use ($pictureRoot): void {
    $resolved = realpath($pictureRoot);
    if ($resolved && str_starts_with($resolved, realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR) && str_starts_with(basename($resolved), 'archival-user-pictures-')) {
        (new Illuminate\Filesystem\Filesystem)->deleteDirectory($resolved);
    }
});
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'filesystems.disks.private' => ['driver' => 'local', 'root' => $pictureRoot, 'throw' => true],
    'session.driver' => 'array',
    'cache.default' => 'array',
    'logging.default' => 'null',
    'hashing.bcrypt.rounds' => 4,
]);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table', '2026_11_11_010000_add_profile_picture_path_to_users'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$other = User::create(['name' => 'Other', 'username' => 'other', 'email' => 'other@example.test', 'role' => 'admin', 'password' => bcrypt('Test!Password123')]);

function sendRequest(string $url, string $method, array $data = [], ?User $user = null, array $files = []): Symfony\Component\HttpFoundation\Response
{
    global $app, $kernel;
    $app['session']->driver()->flush();
    Auth::forgetGuards();
    if ($user) {
        Auth::guard('web')->setUser($user->fresh());
    }
    return $kernel->handle(Request::create($url, $method, $data, [], $files, ['HTTP_ACCEPT' => $method === 'GET' ? 'text/html' : 'application/json']));
}

check(str_ends_with(sendRequest('/profile', 'GET')->headers->get('Location', ''), '/login'), 'Guest profile access redirects to login');
check(sendRequest('/profile', 'PATCH', ['name' => 'Unauthorized'])->getStatusCode() === 401, 'Guests cannot update profiles');
foreach (['admin', 'staff', 'legislator', 'user'] as $role) {
    $user = User::create(['name' => 'Test '.$role, 'username' => 'profile-'.$role, 'email' => $role.'@example.test', 'role' => $role, 'password' => bcrypt('Test!Password123')]);
    $password = $user->password;
    $response = sendRequest('/profile', 'GET', [], $user);
    check($response->isSuccessful() && str_contains(str_replace('\/', '/', $response->getContent()), 'Settings/Profile') && ! str_contains($response->getContent(), $password), "{$role} can view their profile without password exposure");
    $data = ['name' => 'Updated '.$role, 'email' => $role.'-updated@example.test', 'username' => $user->username];
    $response = sendRequest('/profile', 'PATCH', $data, $user);
    check($response->isSuccessful() && $user->fresh()->name === $data['name'] && $user->fresh()->email === $data['email'], "{$role} can update their profile and keep their username");
    $response = sendRequest('/profile', 'PATCH', [...$data, 'id' => $other->id, 'role' => 'admin', 'password' => 'Injected!Password123'], $user);
    check($response->isSuccessful() && $user->fresh()->role === $role && $user->fresh()->password === $password && $other->fresh()->name === 'Other', "{$role} cannot change role, password, or another account through profile updates");
    foreach (['email' => $other->email, 'username' => $other->username, 'name' => ['invalid']] as $field => $value) {
        $response = sendRequest('/profile', 'PATCH', array_replace($data, [$field => $value]), $user);
        check($response->getStatusCode() === 422 && isset(json_decode($response->getContent(), true)['errors'][$field]), "{$role} receives validation errors for invalid {$field}");
    }
}
check(App\Models\SystemLog::where('description', 'like', 'Updated own profile:%')->exists(), 'Profile updates are recorded in system logs');


$payload = ['name' => $user->fresh()->name, 'email' => $user->fresh()->email, 'username' => $user->username, '_method' => 'PATCH'];
$upload = fn () => Illuminate\Http\UploadedFile::fake()->image('avatar.png', 32, 32);
$disk = Illuminate\Support\Facades\Storage::disk('private');
$response = sendRequest('/profile', 'POST', $payload, $user, ['profile_picture' => $upload()]);
$path = $user->fresh()->profile_picture_path;
check($response->isSuccessful() && $path && $disk->exists($path), 'Multipart profile upload saves the image on the private disk');
$response = sendRequest('/profile', 'GET', [], $user);
check(str_contains($response->getContent(), 'profile_picture_url') && ! str_contains($response->getContent(), $path), 'Shared account data includes an image URL and hides its storage path');
$response = sendRequest('/profile/picture', 'GET', [], $user);
ob_start(); $response->sendContent(); $bytes = ob_get_clean();
check($response->isSuccessful() && str_starts_with($bytes, "\x89PNG") && $response->headers->get('Content-Type') === 'image/png', 'Own avatar endpoint streams the uploaded image');
check(str_contains($response->headers->get('Cache-Control'), 'no-store') && $response->headers->get('X-Content-Type-Options') === 'nosniff', 'Avatar endpoint prevents shared caching and content sniffing');
check(sendRequest('/profile/picture', 'GET', [], $other)->getStatusCode() === 404, 'Another account does not receive this user avatar');
check(str_ends_with(sendRequest('/profile/picture', 'GET')->headers->get('Location', ''), '/login'), 'Guests cannot access profile pictures');
foreach ([Illuminate\Http\UploadedFile::fake()->create('fake.jpg', 10, 'text/plain'), Illuminate\Http\UploadedFile::fake()->image('large.png')->size(2049)] as $invalidPicture) {
    $response = sendRequest('/profile', 'POST', $payload, $user, ['profile_picture' => $invalidPicture]);
    check($response->getStatusCode() === 422 && $user->fresh()->profile_picture_path === $path, 'Invalid or oversized images preserve the current avatar');
}
$beforeFiles = $disk->allFiles();
App\Models\SystemLog::creating(function () { throw new RuntimeException('Simulated logging failure'); });
$response = sendRequest('/profile', 'POST', $payload, $user, ['profile_picture' => $upload()]);
App\Models\SystemLog::flushEventListeners();
check($response->getStatusCode() === 500 && $user->fresh()->profile_picture_path === $path && $disk->allFiles() === $beforeFiles, 'Database failure preserves the old avatar and cleans the new upload');
$failedDisk = Mockery::mock(Illuminate\Filesystem\FilesystemAdapter::class);
$failedDisk->shouldReceive('putFileAs')->andThrow(new RuntimeException('Simulated storage outage'));
Illuminate\Support\Facades\Storage::set('private', $failedDisk);
$response = sendRequest('/profile', 'POST', $payload, $user, ['profile_picture' => $upload()]);
Illuminate\Support\Facades\Storage::forgetDisk('private');
check($response->getStatusCode() === 422 && $user->fresh()->profile_picture_path === $path, 'Storage failures return a field error without changing the avatar');
$response = sendRequest('/profile', 'POST', $payload, $user, ['profile_picture' => $upload()]);
check($response->isSuccessful() && $user->fresh()->profile_picture_path !== $path && ! $disk->exists($path), 'Successful replacement deletes the old avatar after saving');
$path = $user->fresh()->profile_picture_path;
$response = sendRequest('/profile', 'POST', [...$payload, 'profile_picture_path' => 'untrusted.png'], $user);
check($response->isSuccessful() && $user->fresh()->profile_picture_path === $path, 'Client-supplied storage paths are ignored');
$response = sendRequest('/profile', 'POST', [...$payload, 'remove_profile_picture' => '1'], $user);
check($response->isSuccessful() && $user->fresh()->profile_picture_path === null && ! $disk->exists($path), 'Removing an avatar clears the record and stored image');
Mockery::close();

// Reproduce the POST used by both dropdowns with a live authenticated session.
Auth::guard('web')->setUser($user->fresh());
$session = $app['session']->driver();
$session->put('logout_marker', 'must be cleared');
$session->regenerateToken();
$token = $session->token();
$response = $kernel->handle(Request::create('/logout', 'POST'));
check($response->isRedirect() && str_ends_with($response->headers->get('Location', ''), '/login') && Auth::guest(), 'POST logout signs out and redirects to login');
check(! $session->has('logout_marker') && $session->token() !== $token, 'Logout invalidates session data and rotates the CSRF token');
check(str_ends_with(sendRequest('/profile', 'GET')->headers->get('Location', ''), '/login'), 'Profile is inaccessible after logout');
check(app('router')->getRoutes()->getByName('logout')->methods() === ['POST'], 'Logout route remains POST-only');
