<?php

namespace App\Classes;

use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherApi
{

    public function getCityWeatherForecastData($city) : array
    {
        $responseData = $this->getCityWeatherForecastFromAPI($city);
        $returnArr = array(
            'err' => 1,
            'message' => 'No weather found for this city. Please make sure the entered city is correct.',
            'data' => array()
        );

        if ($responseData['cod'] == 200) {
            $data = $this->parseWeatherResponse($responseData['list']);
            $returnArr = array(
                'err' => 0,
                'message' => '',
                'data' => $data
            );
        }
        return $returnArr;
    }

    protected function parseWeatherResponse($response): array
    {

        $returnArr = array();
        foreach ($response as $item) {
            $dmIndex = date('d-M-Y', $item['dt']);
            $hmIndex = date('h:ia', $item['dt']);

            $returnArr[$dmIndex][] = array(
                'timeIndex' => $hmIndex,
                'temp' => $item['main']['temp'] . 'C',
                'humidity' => $item['main']['humidity'] . '%',
                'pressure' => $item['main']['pressure'] . 'hPa',
                'weather' => $item['weather'][0]['description'] ?? $item['weather'][0]['main'],
                'wind' => $item['wind']['speed'] . 'm/s',
                'visibility' => ceil($item['visibility'] / 1000) . 'km'
            );
        }
        return $returnArr;
    }

    protected function getCityWeatherForecastFromAPI($city)
    { // Facing issues with GuzzleHTTP in localhost, so using CURL

//        $url = env('OW_FORECAST_URL') . 'lat=' . $city->lat . '&lon=' . $city->lng . '&units=metric&appid=' . env("OW_KEY");

        $url = env('OW_FORECAST_URL') . 'q=' . urlencode($city). '&units=metric&appid=' . env("OW_KEY");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /*protected function getCityLatLongFromWeatherAPI($city)
    {
        $url = env('OW_CITY_URL') . '?q=' . urlencode($city) . '&appid=' . env("OW_KEY");
        return Http::acceptJson()->timeout(-1)->get($url);
    }*/

    /*public function getCityCoordinatesFromWeatherAPI($cityName): array
    {
        $latLongResponse = $this->getCityLatLongFromWeatherAPI($cityName);
        $latLongStatusCode = $latLongResponse->getStatusCode();

        $returnArr = array(
            'err' => 1,
            'message' => 'Invalid City',
            'data' => array()
        );

        if ($latLongStatusCode == 200 && $latLongResponse->ok()) {
            $latLongContent = $latLongResponse->json();
            if (count($latLongContent) > 0) {
                $returnArr['err'] = 0;
                $returnArr['message'] = '';
                $returnArr['data'] = array(
                    'lat' => $latLongContent[0]['lat'],
                    'long' => $latLongContent[0]['lon']
                );
            }
        }

        return $returnArr;
    }*/
}

