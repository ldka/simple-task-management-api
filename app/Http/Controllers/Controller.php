<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function sendResponse($status, $data, $message = null, $statusCode = 200)
    {
        $response = [
            'success' => $status,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }
}
