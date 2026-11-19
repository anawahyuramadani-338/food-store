<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // 1. Set Validation
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ]);

        // 2. If Validation Fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data'    => $validator->errors()
            ], 422);
        }

        // 3. Get Credentials From Request
        $credentials = $request->only('email', 'password');

        // 4. If Auth Fails
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        // 5. If Auth Success
        return response()->json([
            'success'  => true,
            'customer' => auth()->guard('api')->user(),
            'token'    => $token,
        ], 200);
    }
}
