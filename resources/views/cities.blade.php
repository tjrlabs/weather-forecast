@extends('layouts.app')

@section('content')

    <div class="float-left w-full bg-white py-8 px-4 rounded-sm">
        <h2 class="float-left w-full text-2xl color-black font-bold">Add new City</h2>
        <div class="float-left w-full mt-4">
            <form method="post" action="{{route('city.store')}}">
                @csrf
                @if(count($errors))
                    <div class="form-group mb-4">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
                <div>
                    <input type="text" id="city" name="city" value="{{old('city')}}"
                           class="bg-gray-100 max-w-2xl border border-gray-600 text-black text-base rounded-md block w-full p-2.5"
                           placeholder="Enter City name" required>
                </div>
                <div class="mt-4">
                    <button type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="float-left w-full bg-white py-8 px-4 rounded-sm mt-8">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" border="1">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    City name
                </th>
                <th scope="col" class="px-6 py-3">
                    {{date('d-M')}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{ date('d-M',strtotime('+1 days')) }}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{date('d-M',strtotime('+2 days'))}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{date('d-M',strtotime('+3 days'))}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{date('d-M',strtotime('+4 days'))}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{date('d-M',strtotime('+5 days'))}}
                </th>
            </tr>
            </thead>
            <tbody>
            @if(count($citiesData) > 0)
                @foreach($citiesData as $data)
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{$data['name']}}
                        </th>
                        @foreach($data['data'] as $weather)
                            <td>
                                @foreach($weather as $weatherData)
                                    <div class="flex gap-x-4 py-4" style="border-bottom:1px solid white">
                                        <div class="font-bold text-sm">{{$weatherData['timeIndex']}}</div>
                                        <div class="text-sm">

                                            <p class="float-left w-full mb-2">
                                                <strong>Temperature</strong>
                                                <span>{{$weatherData['temp']}}</span>
                                            </p>
                                            <p class="float-left w-full mb-2">
                                                <strong>Humidity</strong>
                                                <span>{{$weatherData['humidity']}}</span>
                                            </p>
                                            <p class="float-left w-full mb-2">
                                                <strong>Pressure</strong>
                                                <span>{{$weatherData['pressure']}}</span>
                                            </p>
                                            <p class="float-left w-full mb-2">
                                                <strong>Weather</strong>
                                                <span>{{$weatherData['weather']}}</span>
                                            </p>
                                            <p class="float-left w-full mb-2">
                                                <strong>Wind</strong>
                                                <span>{{$weatherData['wind']}}</span>
                                            </p>
                                            <p class="float-left w-full mb-2">
                                                <strong>Visibility</strong>
                                                <span>{{$weatherData['visibility']}}</span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        @endforeach

                    </tr>
                @endforeach
            @else
                <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                   <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white" colspan="7">No result(s) found</th>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@stop
