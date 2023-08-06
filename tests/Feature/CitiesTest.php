<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CitiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    /*public function test_cities_page_is_loading_and_is_empty(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('No result(s) found');
    }*/

    public function test_cities_page_is_loading_and_is_non_empty(): void
    {
        City::create([
            'city_name' => "Test City",
            'lat' => 2.0194,
            'lng' => -0.1234,
        ]);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('No result(s) found');
    }

    public function test_cities_weather_api_is_working_correctly() : void{
        $city = City::create([
            'city_name' => "Gurgaon",
            'lat' => 2.0194,
            'lng' => -0.1234,
        ]);
        $response = $this->post(route('forecast.fetch.api',$city->id));
        $response->assertStatus(200);
    }
}
