<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\Film;
use App\Geo;

class GeoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNear()
    {
        $response = $this->get('/near/50,37,-122')->seeJson([
                "distance" => 43.626,
             ]);
    }
}
