<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_login()
    {
        $this->post('/api/login', [
            'username' => 'admin@email.com',
            'password' => 'admin'
        ])->assertStatus(200);
    }

    public function test_register()
    {
        $this->post('/api/register', [
            'name' => 'Gunawan',
            'email' => 'gunawan@email.com',
            'phone' => '082136789876',
            'password' => 'password'
        ])->assertStatus(201);
    }

    public function test_login_with_phone_number()
    {
        $this->post('/api/login', [
            'username' => '082136789876',
            'password' => 'password'
        ])->assertStatus(200);
    }
}
