<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice; // Pastikan nama model Invoice Anda sesuai di proyek
use Illuminate\Http\Request;

class MyOrderController extends Controller
{
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->guard('api')->user();

        // Validasi jika user/token tidak ditemukan atau tidak valid
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized / Token Tidak Valid'
            ], 401);
        }

        // Get invoices by customer ID yang sedang login via JWT
        $invoices = Invoice::where('customer_id', $user->id)
            ->latest()
            ->get();

        // Return JSON response success
        return response()->json([
            'success' => true,
            'message' => 'List Data Orders : ' . $user->name,
            'data'    => $invoices
        ], 200);
    }

    /**
     * show
     *
     * @param  mixed $snap_token
     * @return \Illuminate\Http\Response
     */
    public function show($snap_token)
    {
        $user = auth()->guard('api')->user();

        // Validasi jika user/token tidak ditemukan atau tidak valid
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized / Token Tidak Valid'
            ], 401);
        }

        // Get detail invoice berdasarkan customer_id dan snap_token
        $invoice = Invoice::with('orders.product')
            ->where('customer_id', $user->id)
            ->where('snap_token', $snap_token)
            ->first();

        if ($invoice) {
            // Return JSON response detail invoice found
            return response()->json([
                'success' => true,
                'message' => 'Detail Data Order',
                'data'    => $invoice
            ], 200);
        }

        // Return JSON response detail invoice not found
        return response()->json([
            'success' => false,
            'message' => 'Detail Data Order Tidak Ditemukan',
        ], 404);
    }
}