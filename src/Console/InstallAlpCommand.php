<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Command;

final class InstallAlpCommand extends Command
{
    protected $signature = 'alp:install';

    protected $description = 'Publish ALP configuration and migrations';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'alp-config']);
        $this->info('ALP install completed.');

        return self::SUCCESS;
    }
}
