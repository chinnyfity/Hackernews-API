<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */


    /* public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('auth/signin');
 
        $response->assertStatus(200);
    } */


    public function test_users_can_authenticate_using_the_login_screen()
    {
 
        $response = $this->post('auth/login', [
            'email' => 'chinny@yahoo.com',
            'pass' => '123456',
        ]);
 
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }


    /* public function test_users_can_not_authenticate_with_invalid_password()
    {
        // $user = User::factory()->create();
 
        $this->post('auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
 
        $this->assertGuest();
    } */



}
