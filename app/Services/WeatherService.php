<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $geoUrl;
    protected int $weatherCacheTtl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->baseUrl = config('services.openweather.url');
        $this->geoUrl = config('services.openweather.geo_url');
        $this->weatherCacheTtl = config('services.openweather.weather_cache_ttl');
    }

    public function getCurrentWeather(string $location): ?array
    {
        $coordinates = $this->getCoordinates($location);

        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_{$coordinates['lat']}_{$coordinates['lon']}";

        return Cache::remember($cacheKey, $this->weatherCacheTtl, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/weather", [
                'lat' => $coordinates['lat'],
                'lon' => $coordinates['lon'],
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $data['location'] = [
                    'name' => $coordinates['name'],
                    'country' => $coordinates['country'],
                    'state' => $coordinates['state'],
                ];
                return $data;
            }

            return null;
        });
    }

    protected function getCoordinates(string $location): ?array
    {
        $cacheKey = "geocode_{$location}";

        return Cache::remember($cacheKey, 86400, function () use ($location) {
            $response = Http::get("{$this->geoUrl}/direct", [
                'q' => $location,
                'limit' => 1,
                'appid' => $this->apiKey
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return [
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                    'name' => $data['name'],
                    'country' => $data['country'] ?? null,
                    'state' => $data['state'] ?? null,
                ];
            }

            return null;
        });
    }
}
