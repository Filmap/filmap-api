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
        $response = $this->get('near/2,-5.811922,-35.213377')->seeJson([
                "distance" => 0.111,
             ]);
    }
}