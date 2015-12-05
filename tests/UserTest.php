<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    public function createUser()
    {
        return $this->call('POST', '/user', [
                'name' => 'Test User',
                'email' => 'test@mail.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
        ]);
    }

    public function authenticateUser()
    {

        return $this->call('POST', '/authenticate', [
                'email' => 'test@mail.com',
                'password' => 'secret',
        ]);
    }
    
    /**
     * Get user.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $response = $this->createUser();

        $this->assertEquals(200, $response->status());
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function testAuthenticateUser()
    {
        $response = $this->authenticateUser();

        $this->assertEquals(200, $response->status());
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function testGetUser()
    {
        $response = $this->authenticateUser();
        
        $token = (json_decode($response->content())->token);
        $user = JWTAuth::setToken($token)->authenticate();

        $this->get('/user/' . $user->id, 
            // ['title' => 'awesome blog post'], 
            ['HTTP_Authorization' => 'Bearer ' . $token])
            ->seeJson([
                'response' => True
            ]);
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function testGetAllUsers()
    {
        
        $response = $this->authenticateUser();
        
        $token = (json_decode($response->content())->token);

        $this->get('/user', 
            ['HTTP_Authorization' => 'Bearer ' . $token])
            ->seeJson([
                'response' => True
            ]);
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function testUpdateUser()
    {
        
        $response = $this->authenticateUser();
        
        $token = (json_decode($response->content())->token);
        $user = JWTAuth::setToken($token)->authenticate();

        $this->put('/user/' . $user->id, 
            ['name' => 'Jeff'], 
            ['HTTP_Authorization' => 'Bearer ' . $token])
            ->assertResponseOk();
    }

    /**
     * Get user.
     *
     * @return void
     */
    public function testDeleteUser()
    {
        
        $response = $this->authenticateUser();
        
        $token = (json_decode($response->content())->token);
        $user = JWTAuth::setToken($token)->authenticate();

        $this->delete('user/' . $user->id, 
            ['HTTP_Authorization' => 'Bearer ' . $token])
            ->assertResponseOk();
    }
}
