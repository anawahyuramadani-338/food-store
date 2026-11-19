<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Invoice;
use App\Models\Transaction; // <- Ini adalah Import Model Transaction sesuai catatan di gambar
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallbackController extends Controller
{
    /**
     * __invoke (Handle the webhook request)
     * 
     * @param mixed $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        $payload      = $request->getContent();
        $notification = json_decode($payload);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid Signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type        = $notification->payment_type;
        $orderId     = $notification->order_id;

        // Cari data transaksi di tabel Invoice berdasarkan nomor invoice
        $data_transaction = Invoice::where('invoice', $orderId)->first();

        if ($transaction == 'capture') {
            
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                // Logika challenge credit card bisa disesuaikan, atau jika sukses langsung update
                $data_transaction->update([
                    'status' => 'success'
                ]);
            }

        } elseif ($transaction == 'settlement') {

            /**
             * update invoice to success
             */
            $data_transaction->update([
                'status' => 'success'
            ]);

        } elseif ($transaction == 'pending') {

            /**
             * update invoice to pending
             */
            $data_transaction->update([
                'status' => 'pending'
            ]);

        } elseif ($transaction == 'deny') {

            /**
             * update invoice to failed
             */
            $data_transaction->update([
                'status' => 'failed'
            ]);

        } elseif ($transaction == 'expire') {

            /**
             * update invoice to expired
             */
            $data_transaction->update([
                'status' => 'expired'
            ]);

        } elseif ($transaction == 'cancel') {

            /**
             * update invoice to failed
             */
            $data_transaction->update([
                'status' => 'failed'
            ]);

        }
    }
}
