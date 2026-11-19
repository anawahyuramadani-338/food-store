<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = (bool) config('services.midtrans.isProduction', false);
        Config::$isSanitized  = (bool) config('services.midtrans.isSanitized', true);
        Config::$is3ds        = (bool) config('services.midtrans.is3ds', true);
    }

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'error'   => 'User not authenticated',
            ], 401);
        }

        $carts = Cart::with('product')
            ->where('customer_id', $user->id)
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout Gagal!',
                'error'   => 'Keranjang belanja kosong',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $courier        = (string) $request->input('shipping_courier', $request->input('courier', ''));
            $courierService = (string) $request->input('shipping_service', $request->input('courier_service', ''));
            $courierCost    = max(0, (int) $request->input('shipping_cost', $request->input('courier_cost', 0)));
            $weight         = max(0, (int) $request->input('weight', 0));
            $address        = trim((string) $request->input('full_address', $request->input('address', '')));
            $name           = trim((string) $request->input('name', $user->name ?? 'Customer'));
            $phone          = trim((string) $request->input('phone', $user->phone ?? ''));
            $provinceId     = (int) $request->input('province_id', 11);
            $cityId         = (int) $request->input('city_id', 162);

            if ($name === '') {
                $name = 'Customer';
            }
            if ($address === '') {
                $address = '-';
            }
            if ($phone === '') {
                $phone = '081234567890';
            }

            $item_details = [];
            $calculatedTotal = 0;

            foreach ($carts as $cart) {
                $product = $cart->product;
                $unitPrice = max(1, (int) ($product->price ?? 0));
                $qty       = max(1, (int) ($cart->qty ?? 1));
                $productName = $product->title ?? $product->name ?? ('Product #' . $cart->product_id);

                $item_details[] = [
                    'id'       => (string) $cart->product_id,
                    'price'    => $unitPrice,
                    'quantity' => $qty,
                    'name'     => Str::limit((string) $productName, 50, ''),
                ];

                $calculatedTotal += ($unitPrice * $qty);
            }

            if ($courierCost > 0) {
                $item_details[] = [
                    'id'       => 'shipping_cost',
                    'price'    => $courierCost,
                    'quantity' => 1,
                    'name'     => Str::limit('Shipping Cost ' . strtoupper($courier), 50, ''),
                ];

                $calculatedTotal += $courierCost;
            }

            $grandTotal = (int) $calculatedTotal;
            $no_invoice = 'INV-' . Str::upper(Str::random(10));

            $invoice = Invoice::create([
                'invoice'         => $no_invoice,
                'customer_id'     => (int) $user->id,
                'courier'         => $courier,
                'courier_service' => $courierService,
                'courier_cost'    => $courierCost,
                'weight'          => $weight,
                'name'            => $name,
                'phone'           => $phone,
                'province_id'     => $provinceId,
                'city_id'         => $cityId,
                'address'         => $address,
                'grand_total'     => $grandTotal,
                'status'          => 'pending',
            ]);

            foreach ($carts as $cart) {
                $unitPrice = max(1, (int) ($cart->product->price ?? 0));
                $qty       = max(1, (int) ($cart->qty ?? 1));

                $invoice->orders()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => (int) $cart->product_id,
                    'qty'        => $qty,
                    'price'      => $unitPrice * $qty,
                ]);
            }

            $payload = [
                'transaction_details' => [
                    'order_id'     => (string) $invoice->invoice,
                    'gross_amount' => (int) $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $name,
                    'email'      => (string) ($user->email ?? 'customer@example.com'),
                    'phone'      => $phone,
                    'billing_address' => [
                        'first_name' => $name,
                        'phone'      => $phone,
                        'address'    => $address,
                    ],
                    'shipping_address' => [
                        'first_name' => $name,
                        'phone'      => $phone,
                        'address'    => $address,
                    ],
                ],
                'item_details' => $item_details,
            ];

            $snapToken = Snap::getSnapToken($payload);

            $invoice->snap_token = $snapToken;
            $invoice->save();

            Cart::where('customer_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout Berhasil!',
                'data'    => $invoice->fresh('orders'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout Gagal!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}