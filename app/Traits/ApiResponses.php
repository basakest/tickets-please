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

    protected function error($message = [], $status = null): JsonResponse
    {
        if (is_string($message)) {
            return response()->json(
                compact('message', 'status'),
                $status
            );
        }
        return response()->json([
            'errors' => $message,
        ]);
    }

    protected function notAuthorized($message): JsonResponse
    {
        return $this->error([
            'status'  => 401,
            'message' => $message,
            'source'  => '',
        ]);
    }
}
