<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function ok($message, $data = []): JsonResponse
    {
        return $this->success($message, $data);
    }

    protected function success($message, $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json(
            compact('data', 'message', 'statusCode'),
            $statusCode
        );
    }

    protected function error($message, $statusCode): JsonResponse
    {
        return response()->json(
            compact('message', 'statusCode'),
            $statusCode
        );
    }
}
