<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // get products
        $products = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->when(request()->q, function ($search) {
                $search->where('title', 'like', '%' . request()->q . '%');
            })
            ->latest()
            ->paginate(8);

        // format avg_rating to one decimal
        $products->getCollection()->transform(function ($product) {
            $product->ratings_avg_rating = number_format((float) ($product->ratings_avg_rating ?? 0), 1, '.', '');
            return $product;
        });

        // return response JSON products
        return response()->json([
            'success' => true,
            'message' => 'List Product',
            'data'    => $products,
        ]);
    }

    /**
     * productPopular
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function productPopular()
    {
        // get popular products without blocking products that don't have >= 4 rating yet
        $products = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->when(request()->q, function ($search) {
                $search->where('title', 'like', '%' . request()->q . '%');
            })
            ->orderByDesc('ratings_avg_rating')
            ->latest()
            ->when(request()->limit, function ($limit) {
                $limit->limit((int) request()->limit);
            })
            ->get();

        // format avg_rating to one decimal place
        $products->transform(function ($product) {
            $product->ratings_avg_rating = number_format((float) ($product->ratings_avg_rating ?? 0), 1, '.', '');
            return $product;
        });

        // return response JSON products
        return response()->json([
            'success' => true,
            'message' => 'List Product Populer',
            'data'    => $products,
        ]);
    }

    /**
     * show
     *
     * @param  mixed  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        // get product by slug
        $product = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->withCount('reviews')
            ->where('slug', $slug)
            ->firstOrFail();

        // format ratings_avg_rating to one decimal
        $product->ratings_avg_rating = number_format((float) ($product->ratings_avg_rating ?? 0), 1, '.', '');

        // return response JSON product
        return response()->json([
            'success' => true,
            'message' => 'Detail Product',
            'data'    => $product,
        ]);
    }
}