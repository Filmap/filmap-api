<?php


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
                'distance' => 0.111,
             ]);
    }
}
