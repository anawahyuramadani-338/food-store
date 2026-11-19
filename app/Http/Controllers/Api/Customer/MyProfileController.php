<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MyProfileController extends Controller
{
    public function index()
    {
        $customer = auth()->guard('api')->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan atau belum login.',
                'data'    => null,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profile Customer',
            'data'    => $customer,
        ], 200);
    }

    public function update(Request $request)
    {
        $authUser = auth()->guard('api')->user();

        if (!$authUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data'    => null,
            ], 401);
        }

        $customer = Customer::find($authUser->id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Data customer tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'name'  => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            Storage::disk('public')->putFileAs('customers', $image, $imageName);

            if (!empty($customer->image)) {
                $oldFileName = basename($customer->image);
                if (Storage::disk('public')->exists('customers/' . $oldFileName)) {
                    Storage::disk('public')->delete('customers/' . $oldFileName);
                }
            }

            $updateData['image'] = $imageName;
        }

        $customer->update($updateData);
        $customer->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Profile Berhasil Diupdate!',
            'data'    => $customer,
        ], 200);
    }
}