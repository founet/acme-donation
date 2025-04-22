<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param array<object>|object|null $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success(array|object $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * @param string $message
     * @param array<string>|string|null $errors
     * @param int $status
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', array|string $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}