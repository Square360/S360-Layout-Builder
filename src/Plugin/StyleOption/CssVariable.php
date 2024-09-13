<?php

declare(strict_types=1);

namespace Drupal\s360_layout_builder\Plugin\StyleOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the class attribute option plugin.
 *
 * @StyleOption(
 *   id = "css_variable",
 *   label = @Translation("Css Variable")
 * )
 */
class CssVariable extends StyleOptionPluginBase {

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['css_variable'] = [
      '#type' => 'textfield',
      '#title' => $this->getLabel(),
      '#default_value' => $this->getValue('css_variable') ?? $this->getDefaultValue(),
      '#wrapper_attributes' => [
        'class' => [$this->getConfiguration()['css_variable'] ?? ''],
      ],
      '#description' => $this->getConfiguration('description'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    $value = $this->getValue('css_variable') ?? NULL;

    if (!empty($value)) {
      $build['#attributes']['style'][] = '--' . $this->getConfiguration()['variable'] . ': ' . $value . ';';
    }

    return $build;
  }

}
