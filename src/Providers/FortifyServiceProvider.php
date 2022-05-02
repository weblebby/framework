<?php

namespace Feadmin\Providers;

use Feadmin\Actions\Fortify\CreateNewUser;
use Feadmin\Actions\Fortify\ResetUserPassword;
use Feadmin\Actions\Fortify\UpdateUserPassword;
use Feadmin\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(function () {
            seo()->title(__('Oturum açın'));

            return view('feadmin::guest.login');
        });

        Fortify::resetPasswordView(function () {
            seo()->title(__('Parolamı sıfırla'));

            return view('feadmin::guest.passwords.reset');
        });

        Fortify::requestPasswordResetLinkView(function () {
            seo()->title(__('Parolamı unuttum'));

            return view('feadmin::guest.passwords.email');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
