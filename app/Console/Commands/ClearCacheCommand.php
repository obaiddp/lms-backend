<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use Illuminate\Support\Facades\Log;
class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache, config, routes, views, and compiled files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        // Do not run config:cache here — it fails on shared hosting and breaks bootstrap when scheduled.
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        // Artisan::call('clear-compiled');
        // Artisan::call('optimize:clear');
        // Artisan::call('session:clear');

        Log::info("Successfully cleared cache and config.");
        $this->info('Successfully cleared cache and config.');
        return Command::SUCCESS;
    }
}
