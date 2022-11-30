<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;

    private static int|null $USER_ID = null;

    public function testCreateNewUser()
    {
        $response = $this->post('/api/users', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'password' => $this->faker->password(10),
            'password_confirmation' => $this->faker->password(10),
            'car_id' => $this->faker->numberBetween(10,99)
        ]);

        $this->assertEquals(201, $response->status());

        self::$USER_ID = $response->decodeResponseJson()['message']['id'];

    }

    public function testGetUsers()
    {
        $response = $this->get('/api/users');
        $this->assertEquals(200, $response->status());
    }

    public function testGetUserById()
    {
        $response = $this->get('/api/users/' . self::$USER_ID);
        $this->assertEquals(200, $response->status());
    }

    public function testGetUserByInvalidId()
    {
        $response = $this->get('/api/users/' . 1000);
        $this->assertEquals(404, $response->status());
    }

    public function testCreateNewUserWithInvalidName()
    {
        $response = $this->post('/api/cars', [
            'name' => null,
        ]);
        $this->assertEquals(401, $response->status());
    }

    public function testUpdateExistingUser()
    {
        $response = $this->put('/api/users/' . self::$USER_ID, [
            'name' => $this->faker->name,
        ]);
        $this->assertEquals(200, $response->status());
    }

    public function testUpdateExistingUserWithInvalidName()
    {
        $response = $this->put('/api/users/' . self::$USER_ID, [
            'name' => null
        ]);
        $this->assertEquals(401, $response->status());
    }

    public function testDeleteExistingUser()
    {
        $response = $this->delete('/api/users/' . self::$USER_ID);
        $this->assertEquals(200, $response->status());
    }
}
