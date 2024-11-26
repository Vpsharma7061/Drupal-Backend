<?php
namespace Drupal\weather\Controller;

use Symfony\Component\HttpFoundation\Response;

class WeatherDebugController {
  public function test() {
    if (class_exists('\Drupal\weather\Form\WeatherSettingsForm')) {
      return new Response('Class exists!');
    }
    return new Response('Class not found.');
  }
}
