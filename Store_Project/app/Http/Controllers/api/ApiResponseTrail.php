<?php

namespace App\Http\Controllers\api;

trait ApiResponseTrail
{
    public function apiResponse($data = null,$message = null, $status = null)
    {
        $array = [
            'status'  => $status,
            'message' => $message,
            'data' => $data,
        ];

        return response($array);

    }
}
