<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base layout plugin class.
 *
 * @internal
 *   Plugin classes are internal.
 */
abstract class S360BaseLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();

    if (count($this->columnRatioOptions())) {
      $configuration['column_ratio'] = $this->defaultColumnRatio();
    }

    if (count($this->gutterWidthOptions())) {
      $configuration['gutter_width'] = $this->defaultGutterWidth();
    }

    if (count($this->columnSeparatorOptions())) {
      $configuration['column_separator'] = '';
    }

    if (count($this->backgroundColorOptions())) {
      $configuration['background_color'] = '';
    }

    if ($this->showBackgroundImageField()) {
      $configuration['background_image'] = '';
    }

    if (count($this->layoutWidthOptions())) {
      $configuration['layout_width'] = '';
    }

    if (count($this->marginBottomOptions())) {
      $configuration['margin_bottom'] = '';
    }

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    if (count($this->columnRatioOptions())) {
      $form['column_ratio'] = [
        '#type' => 'select',
        '#title' => $this->t('Column ratio'),
        '#default_value' => $this->configuration['column_ratio'],
        '#options' => $this->columnRatioOptions(),
        '#description' => $this->t('Choose the column ratio for this layout.'),
      ];
    }

    if (count($this->gutterWidthOptions())) {
      $form['gutter_width'] = [
        '#type' => 'select',
        '#title' => $this->t('Gutter width'),
        '#default_value' => $this->configuration['gutter_width'],
        '#options' => $this->gutterWidthOptions(),
        '#empty_option' => $this->t('- None -'),
        '#description' => $this->t('Choose the amount of space between columns.'),
      ];
    }

    if (count($this->columnSeparatorOptions())) {
      $form['column_separator'] = [
        '#type' => 'select',
        '#title' => $this->t('Column separator'),
        '#default_value' => $this->configuration['column_separator'],
        '#options' => $this->columnSeparatorOptions(),
        '#empty_option' => $this->t('- None -'),
        '#description' => $this->t('Choose how the columns should be separated.'),
      ];
    }

    if (count($this->backgroundColorOptions())) {
      $form['background_color'] = [
        '#type' => 'select',
        '#title' => $this->t('Background color'),
        '#default_value' => $this->configuration['background_color'],
        '#options' => $this->backgroundColorOptions(),
        '#empty_option' => $this->t('- None -'),
        '#description' => $this->t('Choose the background for this layout.'),
      ];
    }

    if ($this->showBackgroundImageField()) {
      $form['background_image'] = [
        '#type' => 'media_library',
        '#allowed_bundles' => ['image'],
        '#title' => $this->t('Background image'),
        '#default_value' => $this->configuration['background_image'],
        '#description' => $this->t('Upload or select a background image.'),
      ];
    }

    if (count($this->layoutWidthOptions())) {
      $form['layout_width'] = [
        '#type' => 'select',
        '#title' => $this->t('Layout width'),
        '#default_value' => $this->configuration['layout_width'],
        '#options' => $this->layoutWidthOptions(),
        '#empty_option' => $this->t('Normal'),
        '#description' => $this->t('Choose how wide the layout should be.'),
      ];
    }

    if (count($this->marginBottomOptions())) {
      $form['margin_bottom'] = [
        '#type' => 'select',
        '#title' => $this->t('Margin bottom'),
        '#default_value' => $this->configuration['margin_bottom'],
        '#options' => $this->marginBottomOptions(),
        '#empty_option' => $this->t('- None -'),
        '#description' => $this->t('Choose the amount of space beneath the layout.'),
      ];
    }

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (count($this->columnRatioOptions())) {
      $this->configuration['column_ratio'] = $form_state->getValue('column_ratio');
    }

    if (count($this->gutterWidthOptions())) {
      $this->configuration['gutter_width'] = $form_state->getValue('gutter_width');
    }

    if (count($this->columnSeparatorOptions())) {
      $this->configuration['column_separator'] = $form_state->getValue('column_separator');
    }

    if (count($this->backgroundColorOptions())) {
      $this->configuration['background_color'] = $form_state->getValue('background_color');
    }

    if ($this->showBackgroundImageField()) {
      $this->configuration['background_image'] = $form_state->getValue('background_image');
    }

    if (count($this->layoutWidthOptions())) {
      $this->configuration['layout_width'] = $form_state->getValue('layout_width');
    }

    if (count($this->marginBottomOptions())) {
      $this->configuration['margin_bottom'] = $form_state->getValue('margin_bottom');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    if (count($this->columnRatioOptions())) {
      $build['#attributes']['class'][] = 'layout--' . $this->configuration['column_ratio'];
    }

    if (count($this->gutterWidthOptions())) {
      if ($this->configuration['gutter_width'] !== '') {
        $build['#attributes']['class'][] = 'layout--' . $this->configuration['gutter_width'];
      }
    }

    if (count($this->columnSeparatorOptions())) {
      if ($this->configuration['column_separator'] !== '') {
        $build['#attributes']['class'][] = 'layout--' . $this->configuration['column_separator'];
      }
    }

    if (count($this->backgroundColorOptions())) {
      if ($this->configuration['background_color'] !== '') {
        $build['#attributes']['class'][] = 'layout--background-color';
        $build['#attributes']['class'][] = 'layout--background-color-' . $this->configuration['background_color'];
      }
    }

    if ($this->showBackgroundImageField()) {
      if (!empty($this->configuration['background_image'])) {
        $entity_type_manager = \Drupal::entityTypeManager();

        /** @var \Drupal\media\Entity\Media $media */
        $media = $entity_type_manager->getStorage('media')->load($this->configuration['background_image']);
        $media_image_field = $media->get('field_media_image');

        /** @var \Drupal\file\Entity\File $file */
        $file = $media_image_field->entity;

        $build['#attributes']['style'][] = 'background-image:url(' . file_create_url($file->uri->value) . ')';
        $build['#attributes']['class'][] = 'layout--background-image';
      }
    }

    if (count($this->layoutWidthOptions())) {
      if ($this->configuration['layout_width'] !== '') {
        $build['#attributes']['class'][] = 'layout--' . $this->configuration['layout_width'];
      }
    }

    if (count($this->marginBottomOptions())) {
      if ($this->configuration['margin_bottom'] !== '') {
        $build['#attributes']['class'][] = 'layout--' . $this->configuration['margin_bottom'];
      }
    }

    return $build;
  }

  /**
   * Define the column ratio options for the configuration form.
   *
   * The first option will be used as the default 'column_ratio' configuration
   * value.
   *
   * @return string[]
   *   The column ration options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function columnRatioOptions();

  /**
   * Define the gutter width options for the configuration form.
   *
   * @return string[]
   *   The gutter width options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function gutterWidthOptions();

  /**
   * Define the column separator options for the configuration form.
   *
   * @return string[]
   *   The column separator options array where the keys are strings that will
   *   be added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function columnSeparatorOptions();

  /**
   * Define the background color options for the configuration form.
   *
   * @return string[]
   *   The background color options array where the keys are strings that will
   *   be added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function backgroundColorOptions();

  /**
   * Show or hide the "Background image" field.
   *
   * @return bool
   *   Show the field in the configuration form when TRUE, hide when FALSE.
   */
  abstract protected function showBackgroundImageField();

  /**
   * Define the layout width options for the configuration form.
   *
   * @return string[]
   *   The layout width options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function layoutWidthOptions();

  /**
   * Define the margin bottom options for the configuration form.
   *
   * @return string[]
   *   The margin bottom options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  abstract protected function marginBottomOptions();

  /**
   * Provides a default value for the column ratio options.
   *
   * @return string
   *   A key from the array returned by ::columnRatioOptions().
   */
  protected function defaultColumnRatio() {
    // Return the first available key from the list of options.
    $column_ratio_classes = array_keys($this->columnRatioOptions());
    return array_shift($column_ratio_classes);
  }

  /**
   * Provides a default value for the gutter width options.
   *
   * @return string
   *   A key from the array returned by ::gutterWidthOptions().
   */
  protected function defaultGutterWidth() {
    // Return the first available key from the list of options.
    $gutter_width_classes = array_keys($this->gutterWidthOptions());
    return array_shift($gutter_width_classes);
  }

  /**
   * Provide default gutter width options.
   *
   * @final
   *
   * @return string[]
   *   The gutter width options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  final protected function defaultGutterWidthOptions() {
    return [
      'gutter-sm' => $this->t('Small'),
      'gutter-md' => $this->t('Medium'),
      'gutter-lg' => $this->t('Large'),
    ];
  }

  /**
   * Provide default column separator options.
   *
   * @final
   *
   * @return string[]
   *   The column separator options array where the keys are strings that will
   *   be added to the CSS classes and the values are the human readable labels.
   */
  final protected function defaultColumnSeparatorOptions() {
    return [
      'column-separator-border' => $this->t('Border'),
      'column-separator-divider' => $this->t('Divider'),
    ];
  }

  /**
   * Provide default layout width options.
   *
   * @final
   *
   * @return string[]
   *   The layout width options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  final protected function defaultLayoutWidthOptions() {
    return [
      'edge-to-edge' => $this->t('Edge to Edge'),
      'inset-sm' => $this->t('Inset Small'),
      'inset-md' => $this->t('Inset Medium'),
      'inset-lg' => $this->t('Inset Large'),
    ];
  }

  /**
   * Provide default margin bottom options.
   *
   * @final
   *
   * @return string[]
   *   The margin bottom options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  final protected function defaultMarginBottomOptions() {
    return [
      'margin-bottom-sm' => $this->t('Small'),
      'margin-bottom-md' => $this->t('Medium'),
      'margin-bottom-lg' => $this->t('Large'),
    ];
  }

}
