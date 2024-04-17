<?php

declare(strict_types=1);

namespace Drupal\s360_layout_builder\Plugin\StyleOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the class attribute option plugin.
 *
 * @StyleOption(
 *   id = "data_attribute",
 *   label = @Translation("Data Attribute")
 * )
 */
class DataAttribute extends StyleOptionPluginBase {

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['data_attribute'] = [
      '#type' => 'select',
      '#title' => $this->getLabel(),
      '#default_value' => $this->getValue('data_attribute') ?? $this->getDefaultValue(),
      '#wrapper_attributes' => [
        'class' => [$this->getConfiguration()['data_attribute'] ?? ''],
      ],
      '#description' => $this->getConfiguration('description'),
      '#options' => $this->getConfiguration()['options'],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    $value = $this->getValue('data_attribute') ?? NULL;

    if (!empty($value)) {
      $build['#attributes']['data-' . $this->getConfiguration()['attribute']][] = $value;
    }

    return $build;
  }

}
