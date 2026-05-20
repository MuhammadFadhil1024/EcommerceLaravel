<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as  RegistrationResponseContract;

class RegistrationResponse implements RegistrationResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */

    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->route('products');
    }
}
