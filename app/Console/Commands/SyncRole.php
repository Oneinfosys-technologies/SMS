<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:role {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');

        if (\App::environment('production') && ! $force) {
            $this->error('Could not sync in production mode');
            exit;
        }

        activity()->disableLogging();

        \Artisan::call('cache:clear');

        \Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => $force ? true : false]);

        activity()->enableLogging();

        $this->info('Roles synced.');
    }
}
