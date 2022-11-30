<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CarTest extends TestCase
{
    use WithFaker;

    private static int|null $CAR_ID = null;

    public function testCreateNewCar()
    {
        $response = $this->post('/api/cars', [
            'name' => $this->faker->unique()->text(10),
        ]);

        $this->assertEquals(201, $response->status());

        self::$CAR_ID = $response->decodeResponseJson()['message']['id'];
    }

    public function testGetCars()
    {
        $response = $this->get('/api/cars');
        $this->assertEquals(200, $response->status());
    }

    public function testGetCarById()
    {
        $response = $this->get("/api/cars/" . self::$CAR_ID);
        $this->assertEquals(200, $response->status());
    }

    public function testGetCarByInvalidId()
    {
        $response = $this->get('/api/cars/10000');
        $this->assertEquals(404, $response->status());
    }

    public function testCreateNewCarWithInvalidName()
    {
        $response = $this->post('/api/cars', [
            'name' => null,
        ]);
        $this->assertEquals(401, $response->status());
    }

    public function testUpdateExistingCar()
    {
        $response = $this->put("/api/cars/" . self::$CAR_ID, [
            'name' => $this->faker->unique()->text(10),
        ]);
        $this->assertEquals(200, $response->status());
    }

    public function testUpdateExistingCarWithInvalidName()
    {
        $response = $this->put("/api/cars/" . self::$CAR_ID, [
            'name' => null,
        ]);
        $this->assertEquals(401, $response->status());
    }

/*    public function testDeleteExistingCar()
    {
//        $response = $this->delete("/api/cars/" . self::$CAR_ID);
//        $this->assertEquals(200, $response->status());
    }*/
}
