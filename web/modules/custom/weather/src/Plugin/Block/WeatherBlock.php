<?php

namespace Drupal\weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to display weather information.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("Weather Block")
 * )
 */
class WeatherBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('weather.settings');
    $default_city = $config->get('default_city') ?? 'Delhi';
    $api_key = $config->get('api_key');

    if (empty($api_key)) {
      return [
        '#markup' => $this->t('API key is missing. Please configure the Weather module.'),
      ];
    }

    $weather_data = $this->getWeatherData($default_city, $api_key);

    return [
      '#theme' => 'item_list',
      '#items' => [
        $this->t('City: @city', ['@city' => $default_city]),
        $this->t('Temperature: @temp', ['@temp' => $weather_data['temp'] . '°C']),
        $this->t('Condition: @condition', ['@condition' => $weather_data['condition']]),
      ],
      '#cache' => [
        'contexts' => ['url.path'],
        'tags' => ['weather_data'],
      ],
    ];
  }

  /**
   * Fetch weather data from the WeatherAPI.
   */
  private function getWeatherData($city, $api_key) {
    $url = "https://api.weatherapi.com/v1/current.json?key=$api_key&q=Delhi,IN&aqi=no";


    try {
      $response = \Drupal::httpClient()->get($url);
      $data = json_decode($response->getBody(), TRUE);

      if (isset($data['current'])) {
        return [
          'temp' => $data['current']['temp_c'],
          'condition' => $data['current']['condition']['text'],
        ];
      }
      return [
        'temp' => 'N/A',
        'condition' => 'N/A',
      ];
    }
    catch (\Exception $e) {
      \Drupal::logger('weather')->error('Weather API request failed: @message', ['@message' => $e->getMessage()]);
      return [
        'temp' => 'N/A',
        'condition' => 'N/A',
      ];
    }
  }
}
