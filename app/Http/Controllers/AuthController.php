<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * @param ApiLoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        return $this->ok($request->email);
    }

    public function register(): JsonResponse
    {
        return $this->ok('register');
    }
}
