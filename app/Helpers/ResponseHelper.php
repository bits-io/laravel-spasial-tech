<?php
namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'meta' => [
                'success' => true,
                'message' => $message,
                'code' => $code,
            ],
            'data' => $data,
            'errors' => null,
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'meta' => [
                'success' => false,
                'message' => $message,
                'code' => $code,
            ],
            'data' => null,
            'errors' => $errors,
        ], $code);
    }
}
