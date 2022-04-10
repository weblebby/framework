<?php

namespace Feadmin\Console\Commands;

use Feadmin\Models\Locale;
use Feadmin\Models\Role;
use App\Models\User;
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
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => \Feadmin\Providers\FeadminServiceProvider::class,
        ]);

        $this->call('migrate');

        $localeCode = $this->ask('Enter locale (en, tr, ar, ru)', app()->getLocale());
        $locale = Locale::create(['code' => $localeCode, 'is_default' => true]);

        $this->info("Locale [{$localeCode}] created.");

        $email = $this->ask('Enter admin email', 'admin@gmail.com');
        $password = $this->secret('Enter admin password');

        $role = Role::create(['name' => 'Super Admin']);

        $this->info("Role [{$role->name}] created.");

        $admin = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $admin->assignRole($role);

        $this->info("Admin created.");

        $this->info('Feadmin installed successfully.');
    }
}
