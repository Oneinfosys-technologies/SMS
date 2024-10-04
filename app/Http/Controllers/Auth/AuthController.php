<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SetPushToken;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Get logged in user
     */
    public function me()
    {
        $user = \Auth::user();

        if ($user) {
            $user->validateStatus();
        }

        (new SetPushToken)->execute($user);

        return AuthUserResource::make($user);
    }
}
