<?php

declare(strict_types=1);

namespace Drupal\s360_layout_builder\Plugin\StyleOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the class attribute option plugin.
 *
 * @StyleOption(
 *   id = "checkbox",
 *   label = @Translation("Checkbox")
 * )
 */
class Checkbox extends StyleOptionPluginBase {

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->getLabel(),
      '#default_value' => $this->getValue('checkbox') ?? $this->getDefaultValue(),
      '#wrapper_attributes' => [
        'class' => [$this->getConfiguration()['checkbox'] ?? ''],
      ],
      '#description' => $this->getConfiguration('description'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    if ($this->getValue('checkbox')) {
      if ($this->hasConfiguration('class')) {
        $build['#attributes']['class'][] = $this->getConfiguration()['class'];
      }
      elseif ($this->hasConfiguration('attribute')) {
        $build['#attributes']['data-' . $this->getConfiguration()['attribute']] = '';
      }
    }

    return $build;
  }

}
