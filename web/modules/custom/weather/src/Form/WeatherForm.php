<?php

namespace Drupal\weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class WeatherForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['weather.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('weather.settings');

    $form['default_city'] = [
      '#type' => 'select',
      '#title' => $this->t('Default City'),
      '#description' => $this->t('Select the default city for the weather block.'),
      '#options' => $this->getIndianCities(),
      '#default_value' => $config->get('default_city') ?? 'Delhi',
    ];

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Weather API Key'),
      '#description' => $this->t('Enter your weather API key.'),
      '#default_value' => $config->get('api_key') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('weather.settings')
      ->set('default_city', $form_state->getValue('default_city'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Returns a list of popular Indian cities.
   */
  private function getIndianCities() {
    return [
      'Delhi' => 'Delhi',
      'Mumbai' => 'Mumbai',
      'Bangalore' => 'Bangalore',
      'Chennai' => 'Chennai',
      'Kolkata' => 'Kolkata',
      'Hyderabad' => 'Hyderabad',
      'Pune' => 'Pune',
      'Ahmedabad' => 'Ahmedabad',
      'Jaipur' => 'Jaipur',
      'Surat' => 'Surat',
    ];
  }
}
