<?php

// Exercise the real MySQL/MariaDB enum using an isolated, randomly prefixed table.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

set_exception_handler(function (Throwable $error): void {
    fwrite(STDERR, $error->getMessage()."\n");
    exit(1);
});

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

$config = config('database.connections.'.config('database.default'));
if (! in_array($config['driver'], ['mysql', 'mariadb'], true)) {
    throw new RuntimeException('Run this regression test with a MySQL or MariaDB connection.');
}
$config['prefix'] = 'role_test_'.bin2hex(random_bytes(6)).'_';
config(['database.connections.role_migration_test' => $config]);
DB::setDefaultConnection('role_migration_test');

$migration = require __DIR__.'/../../database/migrations/2026_11_10_030000_add_legislator_to_users_role.php';

try {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->enum('role', ['admin', 'staff', 'user'])->default('user');
    });
    foreach (['admin', 'staff', 'user'] as $role) {
        DB::table('users')->insert(['role' => $role]);
    }

    try {
        DB::table('users')->insert(['role' => 'legislator']);
        throw new RuntimeException('The old enum unexpectedly accepted legislator.');
    } catch (QueryException $error) {
        check(($error->errorInfo[1] ?? null) === 1265, 'Old schema reproduces the reported enum truncation error');
    }

    $migration->up();
    $migration->up();
    DB::table('users')->insert(['role' => 'legislator']);
    DB::table('users')->insert(['role' => DB::raw('DEFAULT')]);
    check(DB::table('users')->orderBy('id')->pluck('role')->all() === ['admin', 'staff', 'user', 'legislator', 'user'], 'Migration accepts legislator and preserves existing roles and the default');

    try {
        $migration->down();
        throw new LogicException('Rollback unexpectedly removed a role in use.');
    } catch (RuntimeException $error) {
        check(str_contains($error->getMessage(), 'Reassign legislator accounts'), 'Rollback refuses to truncate legislator accounts');
    }
    DB::table('users')->where('role', 'legislator')->delete();
    $migration->down();
    check(DB::table('users')->orderBy('id')->pluck('role')->all() === ['admin', 'staff', 'user', 'user'], 'Rollback preserves all remaining accounts');
    $migration->up();
    DB::table('users')->insert(['role' => 'legislator']);
    check(DB::table('users')->where('role', 'legislator')->exists(), 'Migration can be reapplied after rollback');
} finally {
    Schema::dropIfExists('users');
}
