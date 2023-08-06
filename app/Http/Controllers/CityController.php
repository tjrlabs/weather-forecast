<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Classes\WeatherApi;

class CityController extends Controller
{
    public function index()
    {

        $cities = City::all()->sortByDesc('created_at');

        $citiesData = array();

        foreach ($cities as $city) {
            $weatherDataRequest = Request::create('/api/get-forecast/' . $city->id, 'POST');
            $weatherResponse = Route::dispatch($weatherDataRequest);
            $weatherResponse = json_decode($weatherResponse->getContent(), true);
            $citiesData[] = array(
                'name' => $city->city_name,
                'data' => $weatherResponse['data']
            );
        }

        return view('cities')->with('citiesData', $citiesData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|unique:cities,city_name|max:255'
        ]);

        $cityName = $request->city;
        $weatherApi = new WeatherApi();

        try {
            /*$latLongResponse = $weatherApi->getCityCoordinatesFromWeatherAPI($cityName);
            if ($latLongResponse['err'] == 1) {
                return back()->withErrors([$latLongResponse['message']])->withInput();
            }

            list($lat, $long) = $latLongResponse['data'];*/

            $responseData = $weatherApi->getCityWeatherForecastData($cityName);


            if($responseData['err'] == 1){
                return back()->withErrors([$responseData['message']])->withInput();
            }

            $data = $responseData['data'];

            $cityObj = City::create([
                'city_name' => $cityName,
                'lat' => $data['city']['coord']['lat'] ?? 0,
                'lng' => $data['city']['coord']['lon'] ?? 0,
            ]);

            Cache::set($cityName, $data);

            return redirect('/')->with('message', 'Saved Successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors(['Something went wrong. Please try again later.'])->withInput();
        }
    }

    public function fetchForecast(Request $request, $cityId)
    {
        $city = City::findOrFail($cityId);
        $weatherApi = new WeatherApi();
        try {
            $cachedData = Cache::get($city->city_name);
            if ($cachedData) {
                $data = $cachedData;
            } else {
                $responseData = $weatherApi->getCityWeatherForecastData($city->city_name);

                /*$responseData = $this->getCityWeatherForecastFromAPI($city);
                if ($responseData['cod'] != 200) {
                    return response()->json([
                        'error' => 1,
                        'data' => array(),
                        'message' => 'Something went wrong. Please try again later.'
                    ], 400);
                }
                $data = $this->parseWeatherResponse($responseData['list']);*/

                if($responseData['err'] == 1){
                    return response()->json($responseData,400);
                }
                $data = $responseData['data'];
                Cache::set($city->city_name, $data);
            }

            return response()->json([
                'error' => 0,
                'data' => $data,
                'message' => ''
            ], 200);
        } catch (\Throwable $e) {
            echo $e->getMessage().' '.$e->getLine();
        }
    }

    /*private function parseWeatherResponse($response): array
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

    private function getCityWeatherForecastFromAPI($city)
    { // Facing issues with GuzzleHTTP in localhost, so using CURL

        $url = env('OW_FORECAST_URL') . 'lat=' . $city->lat . '&lon=' . $city->lng . '&units=metric&appid=' . env("OW_KEY");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }


    private function getCityLatLongFromWeatherAPI($city)
    {
        $url = env('OW_CITY_URL') . '?q=' . urlencode($city) . '&appid=' . env("OW_KEY");
        return Http::acceptJson()->get($url);
    }*/
}
