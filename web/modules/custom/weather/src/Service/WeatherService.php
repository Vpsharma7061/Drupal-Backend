<?php

namespace Drupal\weather\Service;

use GuzzleHttp\ClientInterface;

/**
 * Weather service to fetch data from WeatherAPI.
 */
class WeatherService {

  protected $httpClient;

  public function __construct(ClientInterface $httpClient) {
    $this->httpClient = $httpClient;
  }

  public function getWeatherData($city) {
    $apiKey = \Drupal::config('weather.settings')->get('api_key');
    $url = "https://api.weatherapi.com/v1/current.json?key={$apiKey}&q={$city}&aqi=no";

    try {
      $response = $this->httpClient->get($url);
      $data = json_decode($response->getBody(), TRUE);

      if (isset($data['current'])) {
        return [
          'temp' => $data['current']['temp_c'], // Celsius temperature
          'condition' => $data['current']['condition']['text'], // Condition description
        ];
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('weather')->error('Error fetching weather data: @message', ['@message' => $e->getMessage()]);
    }

    return ['temp' => 'N/A', 'condition' => 'N/A'];
  }
}
