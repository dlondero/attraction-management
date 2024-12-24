<?php

namespace Tests\Integration;

use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    protected WeatherService $weatherService;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.openweather.key', 'test_api_key');

        $this->weatherService = app(WeatherService::class);

        Cache::flush();
    }

    public function test_can_get_current_weather()
    {
        $location = 'London';
        $lat = 51.5073;
        $lon = -0.1276;

        Http::fake([
            'https://api.openweathermap.org/geo/1.0/direct*' => Http::response([
                [
                    'name' => 'London',
                    'lat' => $lat,
                    'lon' => $lon,
                    'country' => 'GB',
                ]
            ], 200),
        ]);

        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather*' => Http::response([
                'main' => ['temp' => 15],
                'weather' => [['description' => 'Cloudy']],
            ], 200),
        ]);

        $weather = $this->weatherService->getCurrentWeather($location);

        $this->assertNotNull($weather);
        $this->assertEquals(15, $weather['main']['temp']);
        $this->assertEquals('Cloudy', $weather['weather'][0]['description']);
        $this->assertEquals('London', $weather['location']['name']);
        $this->assertEquals('GB', $weather['location']['country']);

        $cacheKey = "weather_{$lat}_{$lon}";
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals($weather, Cache::get($cacheKey));
    }

    public function test_cannot_get_current_weather()
    {
        $location = 'NonexistentCity';

        Http::fake([
            'https://api.openweathermap.org/geo/1.0/direct*' => Http::response([], 200),
        ]);

        $weather = $this->weatherService->getCurrentWeather($location);

        $this->assertNull($weather);
    }

    public function test_cached_weather_data()
    {
        $location = 'Paris';
        $lat = 48.8566;
        $lon = 2.3522;
        $cacheKey = "weather_{$lat}_{$lon}";

        $cachedData = [
            'main' => ['temp' => 20],
            'weather' => [['description' => 'Sunny']],
            'location' => [
                'name' => 'Paris',
                'country' => 'FR',
            ],
        ];

        Cache::put($cacheKey, $cachedData, 1800);

        Http::fake([
            'https://api.openweathermap.org/geo/1.0/direct*' => Http::response([
                [
                    'name' => 'Paris',
                    'lat' => $lat,
                    'lon' => $lon,
                    'country' => 'FR',
                ]
            ], 200),
        ]);

        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather*' => Http::response([], 500),
        ]);

        $weather = $this->weatherService->getCurrentWeather($location);

        $this->assertNotNull($weather);
        $this->assertEquals(20, $weather['main']['temp']);
        $this->assertEquals('Sunny', $weather['weather'][0]['description']);
        $this->assertEquals('Paris', $weather['location']['name']);
        $this->assertEquals('FR', $weather['location']['country']);

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'api.openweathermap.org/data/2.5/weather');
        });
    }
}

