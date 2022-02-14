<?php

namespace Feadmin\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feadmin:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate feadmin tables';

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
        $this->call('migrate', [
            '--path' => dirname(__DIR__) . '/../../database/migrations',
            '--realpath' => true,
        ]);
    }
}
