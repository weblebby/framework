<?php

namespace Feadmin\Http\Responses\Fortify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\PasswordResetResponse as Contract;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\PasswordResetResponse as Base;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetResponse extends Base implements Contract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request): JsonResponse|Response
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($this->status)], 200)
            : redirect(Fortify::redirects('password-reset', config('fortify.views', true) ? panel()->route('login') : null))->with('status', trans($this->status));
    }
}
