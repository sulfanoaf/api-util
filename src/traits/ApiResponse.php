<?php

namespace DAI\Utils\Traits;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use DAI\Utils\Exceptions\BLoCException;

trait ApiResponse
{
    private static function send($data, $http_status = 200)
    {
        return response()->json($data, $http_status);
    }

    public static function success($value = null, $message = "Success")
    {
        $response = [];
        $response['message'] = $message;
        $response['errors'] = null;
        $response['data'] = $value;

        return self::send($response);
    }

    public static function failed($error = null, $message = "Failed")
    {
        if (env('APP_ENV', 'local') != 'production') {
            Log::debug($error);
        }
        $response = [];
        $response['data'] = null;
        $response['errors'] = [];

        $http_status = 500;

        if ($error instanceof ValidationException) {
            $response['message'] = $message;
            $response['errors'] = $error->errors();
            $http_status = 422;
        } else if ($error instanceof BLoCException) {
            $response['message'] = $error->message();
            $response['errors'] = $error->errors();
            $http_status = 400;
        } elseif ($error instanceof QueryException) {
            $errors = [];
            if (env('APP_ENV', 'local') != 'production') {
                $errors['debug']['code'][] = $error->getCode();
                $errors['debug']['sql'][] = $error->getSql();
                $errors['debug']['bindings'][] = $error->getBindings();
            }
            $response['message'] = $error->getMessage();
            $response['errors'] = $errors;
        } elseif ($error instanceof Exception) {
            $response['message'] = $error->getMessage();
        } else {
            if (is_object($error) || is_array($error)) {
                $response['message'] = json_encode($error);
            } else {
                $response['message'] = $error;
            }
        }

        return self::send($response, $http_status);
    }

    public static function unauthorized($message = "Unauthorized")
    {
        $response['message'] = $message;
        $response['errors'] = null;
        $response['data'] = null;

        return self::send($response, 401);
    }

    public static function forbidden($message = "Forbidden")
    {
        $response['message'] = $message;
        $response['errors'] = null;
        $response['data'] = null;

        return self::send($response, 403);
    }

    public static function serviceUnavailable($message = "Service Unavailable")
    {
        $response['message'] = $message;
        $response['errors'] = null;
        $response['data'] = null;

        return self::send($response, 503);
    }

    public static function paymentRequired($message = "Payment Required")
    {
        $response['message'] = $message;
        $response['errors'] = null;
        $response['data'] = null;

        return self::send($response, 402);
    }
}
