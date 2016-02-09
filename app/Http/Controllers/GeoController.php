<?php

namespace App\Http\Controllers;

use App\Film;

class GeoController extends Controller
{
    /**
     * Finds the nearest films. 
     * Using the Haversine formula.
     * Raw Query:.
     *
     * @param int $id
     *
     * @return Response
     */
    public function nearFilms($radius, $lat, $lng)
    {
        $films = Film::near($radius, $lat, $lng)->get();

        return json_encode($films);
    }
}
