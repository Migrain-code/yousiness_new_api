<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

/**
 *
 * @group City
 * */
class CityController extends Controller
{
    /**
     * POST api/city/search
     *
     * Şehir arama apisi sadeece city_name göndermeniz yeterli
     *
     *
     */
    public function search(Request $request)
    {
        $cities = City::where('name', 'like', '%' . $request->city_name . '%')
            ->orWhere('post_code', 'like', '%' . $request->city_name . '%')
            ->take(50)
            ->get();

        return response()->json([
            'cities' => $cities,
        ]);
    }

    /**
     * POST api/city/list
     *
     * Şehir listesi
     *
     *
     */
    public function list()
    {
        return response()->json([
           'cities' => City::all(),
        ]);
    }
}
