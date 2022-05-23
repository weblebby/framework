<?php

namespace Feadmin\Console\Commands;

use Feadmin\Extension as ExtensionItem;
use Feadmin\Facades\Extension;
use Illuminate\Console\Command;

class MigrateExtension extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:extensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate extensions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Extension::enabled()->each(fn (ExtensionItem $e) => $e->migrate());

        $this->info('Extensions migrated.');
    }
}
