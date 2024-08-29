<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponses
{
    public static function error($message = [], $status = null): JsonResponse
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
}
