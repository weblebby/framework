<?php

namespace Feadmin\Console\Commands;

use Feadmin\Database\Seeders\CreateDefaultRoles;
use Feadmin\Models\Locale;
use Feadmin\Models\Role;
use App\Models\User;
use Feadmin\Providers\FeadminServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InstallFeadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feadmin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup feadmin panel.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->publishVendor();
        $this->migrate();
        $this->callSeeders();
        $this->createLocales();
        $this->createAdmin();

        $this->info('Feadmin installed successfully.');

        return static::SUCCESS;
    }

    private function publishVendor(): void
    {
        $this->call('vendor:publish', [
            '--provider' => FeadminServiceProvider::class,
        ]);
    }

    private function migrate(): void
    {
        $this->call('migrate');
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
        $localeCodes = $this->ask('Enter locales (en, tr, ar, ru)', app()->getLocale());
        $localeCodes = array_map('trim', explode(',', $localeCodes));

        foreach ($localeCodes as $localeCode) {
            Locale::query()->create([
                'code' => $localeCode,
                'is_default' => $localeCode === $localeCodes[0]
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
            'password' => Hash::make($password),
        ]);

        $admin->assignRole(Role::findByName('Super Admin'));

        $this->info("Admin created.");
    }
}
