<?php

namespace Drupal\welcome_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Welcome' block.
 *
 * @Block(
 *   id = "welcome_block",
 *   admin_label = @Translation("Welcome Block"),
 *   category = @Translation("Custom")
 * )
 */
class WelcomeBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $currentUser = \Drupal::currentUser(); 
  
        if ($currentUser->isAuthenticated()) {
            $username = $currentUser->getDisplayName();
            $message = $this->t('Welcome, @username!', ['@username' => $username]);
        } else {
            $message = $this->t('Welcome to our site!');
        }
  
        return [
            '#markup' => $message,
        ];
    }
}
