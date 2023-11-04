<?php

namespace Feadmin\Providers;

use Feadmin\Actions\Fortify\CreateNewUser;
use Feadmin\Actions\Fortify\ResetUserPassword;
use Feadmin\Actions\Fortify\UpdateUserPassword;
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
        $this->app->bind(
            \Laravel\Fortify\Actions\AttemptToAuthenticate::class,
            \Feadmin\Actions\Fortify\AttemptToAuthenticate::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class,
            \Feadmin\Http\Controllers\Fortify\PasswordResetLinkController::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Responses\FailedPasswordResetLinkRequestResponse::class,
            \Feadmin\Http\Responses\Fortify\FailedPasswordResetLinkRequestResponse::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Responses\VerifyEmailResponse::class,
            \Feadmin\Http\Responses\Fortify\VerifyEmailResponse::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->views();

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    private function views(): void
    {
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

        Fortify::verifyEmailView(function () {
            seo()->title(__('e-Posta adresinizi doğrulayın'));

            return view('feadmin::user.verify.email');
        });
    }
}
