<?php

namespace App\Http\Controllers\Api\Public;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RajaOngkirController extends Controller
{
    public function searchDestination(Request $request)
    {
        $mockData = [
            [
                "id" => 5621,
                "label" => "Diwek, Jombang, Jawa Timur",
                "subdistrict_id" => 751,
                "subdistrict_name" => "Diwek",
                "city_id" => 162,
                "city_name" => "Jombang",
                "province_id" => 11,
                "province_name" => "Jawa Timur"
            ],
            [
                "id" => 5622,
                "label" => "Cukir, Diwek, Jombang, Jawa Timur",
                "subdistrict_id" => 751,
                "subdistrict_name" => "Diwek",
                "city_id" => 162,
                "city_name" => "Jombang",
                "province_id" => 11,
                "province_name" => "Jawa Timur"
            ]
        ];

        return response()->json($mockData);
    }

    public function checkOngkir(Request $request)
    {
        $courierInput = $request->input('courier', 'jne');
        $courierName = strtoupper($courierInput);

        $mockOngkir = [
            [
                "code" => $courierInput,
                "name" => "Kurir Resmi " . $courierName,
                "costs" => [
                    [
                        "service" => "REG",
                        "description" => "Layanan Reguler " . $courierName,
                        "cost" => [
                            [
                                "value" => 22000,
                                "etd" => "2-3",
                                "note" => ""
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($mockOngkir);
    }
}