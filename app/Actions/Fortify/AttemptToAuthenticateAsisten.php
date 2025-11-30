<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\AttemptToAuthenticate;

class AttemptToAuthenticateAsisten extends AttemptToAuthenticate
{
    protected function guard()
    {
        return Auth::guard('asisten');
    }
}
