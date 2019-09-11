<?php

namespace Illuminated\Testing\Tests\App\Console\Commands;

use Illuminate\Console\Command;

class ConfirmationCommand extends Command
{
    protected $signature = 'confirmation';

    public function handle()
    {
        if (!$this->confirm('Are you sure?')) {
            return;
        }

        $this->info('Done!');
    }
}
