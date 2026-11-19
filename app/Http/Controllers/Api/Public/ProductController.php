<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get products
        $products = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->when(request()->q, function ($search) {
                $search->where('title', 'like', '%' . request()->q . '%');
            })
            ->paginate(8);

        //format avg_rating to one decimal
        $products->getCollection()->transform(function ($product) {
            $product->ratings_avg_rating = number_format($product->ratings_avg_rating, 1);
            return $product;
        });

        //return response JSON products
        return response()->json([
            'success' => true,
            'message' => 'List Product',
            'data'    => $products
        ]);
    }

    /**
     * productPopular
     *
     * @return void
     */
    public function productPopular()
    {
        //get popular products
        $products = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->when(request()->q, function ($search) {
                $search->where('title', 'like', '%' . request()->q . '%');
            })
            ->when(request()->limit, function ($limit) {
                $limit->limit(request()->limit);
            })
            ->having('ratings_avg_rating', '>=', 4)
            ->get();

        //format avg_rating to one decimal place
        $products->transform(function ($product) {
            $product->ratings_avg_rating = number_format($product->ratings_avg_rating, 1);
            return $product;
        });

        //return response JSON products
        return response()->json([
            'success' => true,
            'message' => 'List Product Populer',
            'data'    => $products
        ]);
    }

    /**
     * show
     *
     * @param mixed $slug
     * @return void
     */
    public function show($slug)
    {
        //get product by slug
        $product = Product::query()
            ->with('category', 'ratings.customer')
            ->withAvg('ratings', 'rating')
            ->withCount('reviews')
            ->where('slug', $slug)
            ->firstOrFail();

        //format ratings_avg_rating to one decimal
        $product->ratings_avg_rating = number_format($product->ratings_avg_rating, 1);

        //return response JSON product
        return response()->json([
            'success' => true,
            'message' => 'Detail Product',
            'data'    => $product
        ]);
    }
}