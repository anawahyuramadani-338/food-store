<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Slider;
use App\Http\Controllers\Controller;

class SliderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        // Get sliders
        $sliders = Slider::latest()->get();

        // Return response JSON sliders
        return response()->json([
            'success' => true,
            'message' => 'List Slider',
            'data'    => $sliders
        ]);
    }
}