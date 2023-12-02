<?php

namespace Feadmin\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\AttemptToAuthenticate as Base;
use Laravel\Fortify\Fortify;

class AttemptToAuthenticate extends Base
{
    /**
     * Throw a failed authentication validation exception.
     *
     * @param  Request  $request
     *
     * @throws ValidationException
     */
    protected function throwFailedAuthenticationException($request): void
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ])->errorBag('login');
    }
}
