<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * index
     */
    public function index()
    {
        $customerId = auth()->guard('api')->user()->id;

        $carts = Cart::with('product')
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        $totalPrice = $carts->sum(function ($cart) {
            $price = (int) ($cart->product->price ?? 0);
            $qty = (int) ($cart->qty ?? 0);
            return $price * $qty;
        });

        $totalWeight = $carts->sum(function ($cart) {
            $weight = (int) ($cart->product->weight ?? 0);
            $qty = (int) ($cart->qty ?? 0);
            return $weight * $qty;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data Carts',
            'data'    => [
                'total_weight' => (int) $totalWeight,
                'total_price'  => (int) $totalPrice,
                'carts'        => $carts,
            ],
        ]);
    }

    /**
     * store
     */
    public function store(Request $request)
    {
        $customerId = auth()->guard('api')->user()->id;
        $qty = max(1, (int) ($request->qty ?? 1));

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product Not Found',
                'data'    => null,
            ], 404);
        }

        $cart = Cart::where('product_id', $product->id)
            ->where('customer_id', $customerId)
            ->first();

        if ($cart) {
            $cart->update([
                'qty' => (int) $cart->qty + $qty,
            ]);
        } else {
            $cart = Cart::create([
                'product_id'  => $product->id,
                'customer_id' => $customerId,
                'qty'         => $qty,
            ]);
        }

        $cart->load('product');

        return response()->json([
            'success' => true,
            'message' => 'Success Add To Cart',
            'data'    => $cart,
        ]);
    }

    /**
     * incrementCart
     */
    public function incrementCart(Request $request)
    {
        $cart = Cart::with('product')
            ->where('id', $request->cart_id)
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->first();

        if ($cart) {
            $cart->update([
                'qty' => (int) $cart->qty + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Success Increment Cart',
                'data'    => $cart->fresh('product'),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data Cart Not Found',
            'data'    => null,
        ], 404);
    }

    /**
     * decrementCart
     */
    public function decrementCart(Request $request)
    {
        $cart = Cart::with('product')
            ->where('id', $request->cart_id)
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->first();

        if ($cart) {
            if ($cart->qty > 1) {
                $cart->update([
                    'qty' => (int) $cart->qty - 1,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Success Decrement Cart',
                    'data'    => $cart->fresh('product'),
                ], 200);
            } else {
                $cart->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Success Remove Cart',
                    'data'    => null,
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Data Cart Not Found',
            'data'    => null,
        ], 404);
    }

    /**
     * totalPrice
     */
    public function totalPrice()
    {
        $carts = Cart::with('product')
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->get();

        $totalPrice = $carts->sum(function ($cart) {
            return ((int) ($cart->product->price ?? 0)) * ((int) ($cart->qty ?? 0));
        });

        return response()->json([
            'success' => true,
            'message' => 'Total Price Carts',
            'total'   => $totalPrice,
        ]);
    }

    /**
     * totalWeight
     */
    public function totalWeight()
    {
        $carts = Cart::with('product')
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->get();

        $totalWeight = $carts->sum(function ($cart) {
            return ((int) ($cart->product->weight ?? 0)) * ((int) ($cart->qty ?? 0));
        });

        return response()->json([
            'success' => true,
            'message' => 'Total Weight Carts',
            'total'   => $totalWeight,
        ]);
    }

    /**
     * removeCart
     */
    public function removeCart(Request $request, $id = null)
    {
        $cartId = $request->cart_id ?? $id ?? $request->route('id');

        $cart = Cart::where('id', $cartId)
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->first();

        if ($cart) {
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success Remove Cart',
                'data'    => null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data Cart Not Found',
            'data'    => null,
        ], 404);
    }
}