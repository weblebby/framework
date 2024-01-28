<?php

namespace Feadmin\Http\Responses\Fortify;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as Contract;
use Laravel\Fortify\Http\Responses\FailedPasswordResetLinkRequestResponse as Base;
use Symfony\Component\HttpFoundation\Response;

class FailedPasswordResetLinkRequestResponse extends Base implements Contract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     *
     * @throws ValidationException
     */
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($this->status)],
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($this->status)], 'forgot');
    }
}
