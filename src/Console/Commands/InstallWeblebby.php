<?php

namespace Weblebby\Framework\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Weblebby\Framework\Database\Seeders\CreateDefaultRoles;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Models\Role;
use Weblebby\Framework\Providers\WeblebbyServiceProvider;

class InstallWeblebby extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weblebby:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup weblebby panel.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->confirm('This action regenerate all your database!');

        $this->publishVendor();
        $this->migrate();
        $this->callSeeders();
        $this->createLocales();
        $this->createAdmin();

        $this->info('Weblebby installed successfully.');

        return static::SUCCESS;
    }

    private function publishVendor(): void
    {
        $this->call('vendor:publish', [
            '--provider' => WeblebbyServiceProvider::class,
        ]);
    }

    private function migrate(): void
    {
        $this->call('migrate:fresh');

        foreach (Extension::get() as $extension) {
            $extension->migrate();
        }
    }

    private function callSeeders(): void
    {
        $this->info('Seeding default data...');

        $seeders = [
            CreateDefaultRoles::class,
        ];

        foreach ($seeders as $seeder) {
            $this->call('db:seed', [
                '--class' => $seeder,
            ]);
        }
    }

    private function createLocales(): void
    {
        if (Extension::has('multilingual')) {
            $localeCodes = $this->ask('Enter locales (en, tr, ar, ru)', app()->getLocale());
            $localeCodes = array_map('trim', explode(',', $localeCodes));
        } else {
            $localeCodes = [app()->getLocale()];
        }

        foreach ($localeCodes as $localeCode) {
            DB::table('locales')->insert([
                'code' => $localeCode,
                'is_default' => $localeCode === $localeCodes[0],
            ]);
        }

        $this->info(sprintf('Locales [%s] created.', implode(', ', $localeCodes)));
    }

    private function createAdmin(): void
    {
        $name = $this->ask('Enter admin name', 'Admin');
        $email = $this->ask('Enter admin email', 'admin@gmail.com');
        $password = $this->secret('Enter admin password', 'password');

        $admin = User::query()->create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
        ]);

        $admin->assignRole(Role::findByName('Super Admin'));

        $this->info('Admin created.');
    }
}
