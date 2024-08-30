<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the user and returns the user's API token.
     *
     * @unauthenticated
     * @group Authentication
     * @response 200 {
    "data": {
    "token": "{YOUR_AUTH_KEY}"
    },
    "message": "Authenticated",
    "status": 200
    }
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        // The function guard is only available for routes with web middleware
        if (! Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }
        $user = User::firstWhere('email', $request->email);

        return $this->ok('Authenticated', [
            'token' => $user->createToken(
                'API token for ' . $user->email,
                Abilities::getAbilities($user),
                now()->addMonth())->plainTextToken,
        ]);
    }

    /**
     * Logout
     *
     * Signs out the user and destroy the API token.
     *
     * @group Authentication
     * @response 200 {}
     */
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->ok('');
    }

    public function register(): JsonResponse
    {
        return $this->ok('register', []);
    }
}
