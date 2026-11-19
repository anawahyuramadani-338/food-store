<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // 1. Set Validation
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:customers',
            'password' => 'required|min:8|confirmed',
        ]);

        // 2. Response Validation Fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Create Customer
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // 4. Return Response JSON User Is Created
        if ($customer) {
            return response()->json([
                'success' => true,
                'message' => 'Register Berhasil',
                'data'    => $customer,
            ], 201);
        }

        // 5. Return Response JSON Insert Failed
        return response()->json([
            'success' => false,
            'message' => 'Register Gagal',
        ], 409);
    }
}
