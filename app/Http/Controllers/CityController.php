<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

class CityController extends Controller
{
    public function index(){
        $cities = City::all()->sortByDesc('created_at');
        $cityArr = array();

        foreach($cities as $city){
            $weatherDataRequest = Request::create(route('forecast.fetch.api',$city->id),'POST');
            $weatherResponse = Route::dispatch($weatherDataRequest);
            dd($weatherResponse);
        }

        dd($cityArr);

        return view('cities');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'city' => 'required|unique:cities,city_name|max:255'
        ]);

        $cityName = $request->city;
        try {
            $latLongResponse = $this->getCityLatLongFromWeatherAPI($cityName);

            $latLongStatusCode = $latLongResponse->getStatusCode();

            if ($latLongStatusCode == 200 && $latLongResponse->ok()) {
                $latLongContent = $latLongResponse->json();
                if (count($latLongContent) <= 0) {
                    return back()->withErrors(['Invalid City Name'])->withInput();
                }

                $lat = $latLongContent[0]['lat'];
                $long = $latLongContent[0]['lon'];

                $cityObj = City::create([
                    'city_name' => $cityName,
                    'lat' => $lat,
                    'lng' => $long
                ]);

            } else {
                return back()->withErrors(['Invalid City Name'])->withInput();
            }
        }
        catch (\Throwable $e){
            return back()->withErrors(['Something went wrong. Please try again later.'])->withInput();
        }

        return redirect('/')->with('message','Saved Successfully!');
    }

    public function fetchForecast(Request $request, $cityId){
        $city = City::findOrFail($cityId);
        $responseData = $this->getCityWeatherForecastFromAPI($city);

        var_dump($responseData->json());
    }

    private function getCityWeatherForecastFromAPI($city) : \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = env('OW_FORECAST_URL').'lat='.$city->lat.'&lon='.$city->lng.'&appid='.env("OW_KEY");
        $response = Http::acceptJson()->get($url);

        dd($response);
    }


    private function getCityLatLongFromWeatherAPI($city) : \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = env('OW_CITY_URL').'?q='.urlencode($city).'&appid='.env("OW_KEY");
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get($url);
    }
}
