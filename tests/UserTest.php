<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Get user.
     *
     * @return void
     */
    public function testGeneralUserRequests()
    {
        $response = $this->call('POST', '/user', [
                'name' => 'Duarte Fernandes',
                'email' => 'duartefq@outlook.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
        ]);

        $this->assertEquals(200, $response->status());

        $response = $this->call('POST', '/authenticate', [
                'email' => 'duartefq@outlook.com',
                'password' => 'secret',
        ]);

        $this->assertEquals(200, $response->status());

        $token = json_decode( $response->content() )->token;

        $this->get('/user',
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token ] 
            )->seeJson([
                'response' => True
            ]);

        $this->get('/user/1',
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token ] 
            )->seeJson([
                'response' => True
            ]);


        // $response = $this->call('PUT', '/user/1', ['name' => 'J'],
        //         [
        //             'HTTP_AUTHORIZATION' => 'Bearer ' . $token 
        //         ]);
        // $this->assertEquals(200, $response->status());


        // $this->delete('/user/1')->assertResponseOk();

        // $this->assertEquals(200, $response->status());

    }
}
