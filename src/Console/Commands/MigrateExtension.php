<?php

namespace Feadmin\Console\Commands;

use Feadmin\Facades\Extension;
use Feadmin\Items\ExtensionItem as ExtensionItem;
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
     */
    public function handle(): int
    {
        Extension::get()->each(function (ExtensionItem $extension) {
            $this->alert("Migrate for {$extension->name()} extension.");

            $this->call('migrate', [
                '--path' => $extension->path('database/migrations'),
            ]);
        });

        $this->info('Extensions migrated.');

        return static::SUCCESS;
    }
}
