<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Film;

class GeoController extends Controller
{
    /**
     * Finds the nearest films. 
     * Using the Haversine formula.
     * Raw Query:
     *
     * @param  int  $id
     * @return Response
     */
    public function filmsAround($distance, $lat, $lng)
    {

        $lat = intval($lat);
        $lng = intval($lng);
        $films = Film::near($distance, $lat, $lng)->get();

        return json_encode($films); 
    }
}
