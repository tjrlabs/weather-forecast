<?php

namespace Tests\Unit;

use App\Classes\WeatherApi;
use PHPUnit\Framework\TestCase;

class CityWeatherForecastTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_city_weather_forecast_data_is_valid(): void
    {
        $data = (new WeatherApi())->getCityWeatherForecastData('London');

        // If weather is found
        $this->assertNotContains('No weather found for this city. Please make sure the entered city is correct.', $data);

        // If array is empty
        //$this->assertEquals(array(), $data['data']);
    }
}
