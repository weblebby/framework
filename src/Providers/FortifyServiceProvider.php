<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Weblebby\Framework\Actions\Fortify\CreateNewUser;
use Weblebby\Framework\Actions\Fortify\ResetUserPassword;
use Weblebby\Framework\Actions\Fortify\UpdateUserPassword;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class,
            \Weblebby\Framework\Http\Controllers\Fortify\PasswordResetLinkController::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Responses\FailedPasswordResetLinkRequestResponse::class,
            \Weblebby\Framework\Http\Responses\Fortify\FailedPasswordResetLinkRequestResponse::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Responses\VerifyEmailResponse::class,
            \Weblebby\Framework\Http\Responses\Fortify\VerifyEmailResponse::class,
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Responses\PasswordResetResponse::class,
            \Weblebby\Framework\Http\Responses\Fortify\PasswordResetResponse::class,
        );

        Fortify::$registersRoutes = false;
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->views();
        $this->actions();
        $this->rateLimiters();
    }

    private function actions(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }

    private function views(): void
    {
        Fortify::loginView(function () {
            seo()->title(__('Oturum açın'));

            return view('weblebby::guest.login');
        });

        Fortify::registerView(function () {
            seo()->title(__('Hesap oluşturun'));

            return view('weblebby::guest.register');
        });

        Fortify::resetPasswordView(function () {
            seo()->title(__('Parolamı sıfırla'));

            return view('weblebby::guest.passwords.reset');
        });

        Fortify::requestPasswordResetLinkView(function () {
            seo()->title(__('Parolamı unuttum'));

            return view('weblebby::guest.passwords.email');
        });

        Fortify::verifyEmailView(function () {
            seo()->title(__('e-Posta adresinizi doğrulayın'));

            return view('weblebby::user.verify.email');
        });
    }

    private function rateLimiters(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
