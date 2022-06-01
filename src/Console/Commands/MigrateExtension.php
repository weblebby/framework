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
        Extension::enabled()->each(function (ExtensionItem $extension) {
            $this->alert("Migrate for {$extension->name} Extension");

            $this->call('migrate', [
                '--path' => $extension->originalPath('Database/migrations'),
            ]);
        });

        $this->info('Extensions migrated.');
    }
}
