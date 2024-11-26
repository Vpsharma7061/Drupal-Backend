<?php

namespace Drupal\ingredient\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure ingredient settings for this site.
 */
class IngredientSettingsForm extends ConfigFormBase {

  public function __construct(ConfigFactoryInterface $configFactory, TypedConfigManagerInterface $typedConfigManager, protected ModuleHandlerInterface $moduleHandler) {
    parent::__construct($configFactory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ingredient_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ingredient.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ingredient.settings');

    $form['ingredient_name_normalize'] = [
      '#type' => 'radios',
      '#title' => $this->t('Ingredient name normalization'),
      '#default_value' => $config->get('ingredient_name_normalize'),
      '#options' => [
        $this->t('Leave as entered'),
        $this->t('Convert to lowercase'),
      ],
      '#description' => $this->t('If enabled, the names of <em>new</em> ingredients will be converted to lowercase when they are entered. The names of registered trademarks, any ingredient name containing the &reg; symbol, will be excluded from normalization.'),
      '#required' => TRUE,
    ];
    if ($this->moduleHandler->moduleExists('language')) {
      $form['default_ingredient_language'] = [
        '#type' => 'details',
        '#title' => $this->t('Ingredients language'),
        '#open' => TRUE,
      ];
      $form['default_ingredient_language']['default_language'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => 'ingredient',
          'bundle' => 'ingredient',
        ],
        '#default_value' => ContentLanguageSettings::loadByEntityTypeBundle('ingredient', 'ingredient'),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ingredient.settings')
      ->set('ingredient_name_normalize', $values['ingredient_name_normalize'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
