<?php

// Isolated HTTP/storage regression tests; no configured database or R2 bucket is used.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
$root = sys_get_temp_dir().'/archival-official-pictures-'.bin2hex(random_bytes(8));
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'filesystems.disks.private' => ['driver' => 'local', 'root' => $root.'/private', 'throw' => true],
    'filesystems.disks.public' => ['driver' => 'local', 'root' => $root.'/public', 'throw' => true],
    'session.driver' => 'array', 'cache.default' => 'array', 'logging.default' => 'null',
    'hashing.bcrypt.rounds' => 4,
]);

use App\Models\SangguniangPanlalawiganMember;
use App\Models\SangguniangPanlalawiganTerm;
use App\Models\SpProfilePicture;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

set_exception_handler(function (Throwable $error): void { fwrite(STDERR, $error->getMessage()."\n"); exit(1); });

function check(bool $condition, string $message): void
{
    if (! $condition) { throw new RuntimeException($message); }
    echo "PASS: {$message}\n";
}

foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table', '2025_10_30_063930_create_sangguniang_panlalawigan_members_table', '2025_10_30_063943_create_sp_profile_pictures_table', '2026_11_10_060000_add_disk_to_sp_profile_pictures'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}
$admin = User::create(['name' => 'Test Admin', 'username' => 'admin', 'email' => 'admin@example.test', 'role' => 'admin', 'password' => 'Test!Password']);
$term = SangguniangPanlalawiganTerm::create(['term_no' => '1st', 'start_year' => '2022-01-01', 'end_year' => '2025-01-01']);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$payload = ['term_id' => $term->term_id, 'district' => '1st District', 'full_name' => 'Test Official', 'extension' => 'Hon', 'position' => 'Board Member'];

function send(string $uri, array $data = [], array $files = [], string $method = 'POST', bool $signedIn = true)
{
    global $app, $admin, $kernel;
    $app['session']->driver()->flush();
    $app['auth']->forgetGuards();
    if ($signedIn) { Auth::guard('web')->setUser($admin->fresh()); }
    return $kernel->handle(Request::create($uri, $method, $data, [], $files, ['HTTP_ACCEPT' => 'application/json']));
}

try {
    $response = send('/sp-1st/officials', $payload, ['profile_picture' => UploadedFile::fake()->image(str_repeat('a', 260).'.png', 16, 16)]);
    $member = SangguniangPanlalawiganMember::firstOrFail();
    $picture = $member->profilePicture;
    check($response->isSuccessful() && $picture->disk === 'private' && Storage::disk('private')->exists($picture->file_url), 'New officials pictures are saved on the private disk');
    check(strlen($picture->file_url) < 100 && mb_strlen($picture->file_name) <= 255 && Storage::disk('public')->allFiles() === [], 'Unsafe or long client filenames do not become storage paths');
    $response = send($picture->display_url, method: 'GET');
    ob_start(); $response->sendContent(); $image = ob_get_clean();
    check($response->isSuccessful() && str_starts_with($image, "\x89PNG") && $response->headers->get('Content-Type') === 'image/png', 'Authenticated picture route streams real image bytes inline');
    check($response->headers->get('X-Content-Type-Options') === 'nosniff', 'Picture responses prevent content type sniffing');
    check(send($picture->display_url, method: 'GET', signedIn: false)->getStatusCode() === 401, 'Guests cannot access officials pictures');

    $oldPath = $picture->file_url;
    $response = send('/sp-1st/officials/'.$member->member_id, [...$payload, '_method' => 'PUT'], ['profile_picture' => UploadedFile::fake()->image('replacement.jpg', 16, 16)]);
    $picture = $member->fresh()->profilePicture;
    check($response->isSuccessful() && $picture->file_url !== $oldPath && ! Storage::disk('private')->exists($oldPath), 'Replacing a picture removes the old object only after the save succeeds');

    $oldPath = $picture->file_url;
    $oldName = $member->fresh()->full_name;
    $beforeFiles = Storage::disk('private')->allFiles();
    SystemLog::creating(function () { throw new RuntimeException('Simulated database failure'); });
    $response = send('/sp-1st/officials/'.$member->member_id, [...$payload, 'full_name' => 'Must roll back', '_method' => 'PUT'], ['profile_picture' => UploadedFile::fake()->image('rejected.png', 16, 16)]);
    check($response->getStatusCode() === 500 && $member->fresh()->full_name === $oldName && $member->fresh()->profilePicture->file_url === $oldPath && Storage::disk('private')->allFiles() === $beforeFiles, 'Database failures preserve the old picture and clean up the replacement upload');
    SystemLog::flushEventListeners();

    $failedDisk = Mockery::mock(Illuminate\Filesystem\FilesystemAdapter::class);
    $failedDisk->shouldReceive('putFileAs')->andThrow(new RuntimeException('Simulated R2 outage'));
    Storage::set('private', $failedDisk);
    $response = send('/sp-1st/officials/'.$member->member_id, [...$payload, '_method' => 'PUT'], ['profile_picture' => UploadedFile::fake()->image('unavailable.png', 16, 16)]);
    Storage::forgetDisk('private');
    check($response->getStatusCode() === 422 && isset(json_decode($response->getContent(), true)['errors']['profile_picture']) && Storage::disk('private')->exists($oldPath), 'R2 upload failures return a picture field error and keep the existing image');

    $response = send('/sp-1st/officials', $payload, ['profile_picture' => UploadedFile::fake()->create('fake.jpg', 10, 'text/plain')]);
    check($response->getStatusCode() === 422, 'Invalid images are rejected before creating an official');
    $response = send('/sp-1st/officials', $payload, ['profile_picture' => UploadedFile::fake()->image('large.png')->size(2049)]);
    check($response->getStatusCode() === 422, 'Images larger than 2MB are rejected');

    $legacyMember = SangguniangPanlalawiganMember::create([...$payload, 'full_name' => 'Legacy Official']);
    Storage::disk('public')->put('sp-profiles/legacy.png', $image);
    $legacy = SpProfilePicture::create(['sp_member_id' => $legacyMember->member_id, 'added_by' => $admin->id, 'file_url' => 'sp-profiles/legacy.png', 'file_name' => 'legacy.png', 'file_type' => 'image/png']);
    check(send($legacy->display_url, method: 'GET')->isSuccessful(), 'Existing local pictures remain readable before copying');
    check(Artisan::call('officials:migrate-pictures', ['--dry-run' => true]) === 0 && $legacy->fresh()->disk === 'public', 'Migration preview does not change existing pictures');
    check(Artisan::call('officials:migrate-pictures') === 0, 'Legacy-picture transfer succeeds');
    $legacy->refresh();
    check($legacy->disk === 'private' && Storage::disk('private')->get($legacy->file_url) === $image && Storage::disk('public')->exists('sp-profiles/legacy.png'), 'Transferred pictures match their source and retain the local backup');
    $files = Storage::disk('private')->allFiles();
    check(Artisan::call('officials:migrate-pictures') === 0 && Storage::disk('private')->allFiles() === $files, 'Rerunning the transfer does not duplicate pictures');

    $response = send('/sp-1st/officials/'.$member->member_id, method: 'DELETE');
    check($response->isSuccessful() && ! $member->fresh() && ! Storage::disk('private')->exists($oldPath), 'Deleting an official removes the profile picture from its recorded disk');
} finally {
    Storage::forgetDisk('private');
    Storage::forgetDisk('public');
    (new Illuminate\Filesystem\Filesystem)->deleteDirectory($root);
    Mockery::close();
}
