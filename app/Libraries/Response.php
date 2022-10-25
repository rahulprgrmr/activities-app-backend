<?php

namespace App\Libraries;

class Response
{

    public static function success($message = NULL, $data = NULL)
    {
        if (!is_string($message)) {
            $data = $message;
            $message = NULL;
        }
        return self::_setResponse(true, $message, $data);
    }

    public static function error($error = NULL)
    {
        return self::_setResponse(false, NULL, NULL, $error);
    }

    private static function _setResponse(bool $success, string $successMessage = NULL, string|array|\JsonSerializable $data = NULL, string|array|\JsonSerializable $error = NULL, $statusCode = 200)
    {
        $response = [
            'success'   => $success
        ];
        if ($success) {
            $successMessage ? $response['message'] = $successMessage : NULL;
            $data ? $response['data'] = $data : NULL;
        }
        else {
            if (is_string($error)) {
                $response['error'] = [
                    'message'   => $error
                ];
            } else {
                $response['error'] = $error;
            }
        }
        return response()->json($response, $statusCode);
    }
}
