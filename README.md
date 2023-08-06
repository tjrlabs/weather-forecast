<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Project

This is a simple Weather Forecast Project, which gets weather forecast of the cities of next 5 days.

API Used - [OpenWeatherMap](https://openweathermap.org/api)

## How to install and run the project

1. Clone this repository
2. Run "composer install"
3. Run "npm install" to install required packages
4. Run "npm dev"
5. Copy .env.example to .env file and add your db configurations
6. Run "php artisan migrate"

## Additional functionalities and improvements
1. We can add an API request to get coordinates via geocoding api and store coordinates while storing city. This will reduce stray calls to the forecast api incase user enters any invalid city.
2. A Cache is being used to store the API request data after first call, further improvements include resetting the cache data at the interval of x number of hours  as per the requirements.
3. The forecast data can be structured and displayed in a better way.
