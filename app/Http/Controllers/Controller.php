<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $code);
        }

        return $data;
    }

    protected function errorResponse($message = 'Error', $code = 400, $errors = [])
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        }

        return redirect()->back()->with('error', $message)->withErrors($errors);
    }

    protected function generateNumber($prefix, $model, $field = 'id', $digits = 6)
    {
        $lastRecord = $model::withTrashed()->latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $number = str_pad($lastId + 1, $digits, '0', STR_PAD_LEFT);
        return $prefix . $number;
    }
}