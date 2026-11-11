<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class IssueApiToken extends Command
{
    protected $signature = 'archival:issue-api-token
        {email : The dedicated API client email}
        {--name=Sangguniang Panlalawigan : A descriptive client name}
        {--expires=90 : Number of days until the token expires}';

    protected $description = 'Issue a limited Sanctum token for an archival API service account';

    public function handle(): int
    {
        $expires = filter_var($this->option('expires'), FILTER_VALIDATE_INT);
        if ($expires === false || $expires < 1 || $expires > 365) {
            $this->error('The token expiration must be between 1 and 365 days.');

            return self::FAILURE;
        }

        $client = ApiClient::firstOrCreate(
            ['email' => $this->argument('email')],
            ['name' => $this->option('name')]
        );
        $token = $client->createToken(
            $this->option('name'),
            ['documents:read'],
            now()->addDays($expires)
        );

        $this->info("API token created. It expires in {$expires} days.");
        $this->line('Store it in the Sangguniang Panlalawigan backend environment only.');
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
