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
class S360BaseLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();

    if (count($this->columnWidthOptions())) {
      $configuration['column_width'] = $this->setDefaultColumnWidth();
    }

    if (count($this->backgroundColorOptions())) {
      $configuration['background_color'] = '';
    }

    if ($this->showBackgroundImageField()) {
      $configuration['background_image'] = '';
    }

    if ($this->showBorderedOption()) {
      $configuration['bordered'] = FALSE;
    }

    if ($this->showEdgeToEdgeOption()) {
      $configuration['edge_to_edge'] = FALSE;
    }

    if ($this->showInsetOption()) {
      $configuration['inset'] = FALSE;
    }

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    if (count($this->columnWidthOptions())) {
      $form['column_width'] = [
        '#type' => 'select',
        '#title' => $this->t('Column width'),
        '#default_value' => $this->configuration['column_width'],
        '#options' => $this->columnWidthOptions(),
        '#description' => $this->t('Choose the column widths for this layout.'),
      ];
    }

    if ($this->showGutterWidthOption()) {
      $form['gutter_width'] = [
        '#type' => 'select',
        '#title' => $this->t('Gutter width'),
        '#default_value' => $this->configuration['gutter_width'],
        '#options' => $this->gutterWidthOptions(),
        '#empty_option' => t('- Select -'),
        '#description' => $this->t('Choose the amount of space between columns.'),
      ];
    }

    if (count($this->backgroundColorOptions())) {
      $form['background_color'] = [
        '#type' => 'select',
        '#title' => $this->t('Background color'),
        '#default_value' => $this->configuration['background_color'],
        '#options' => $this->backgroundColorOptions(),
        '#description' => $this->t('Choose the background for this layout.'),
      ];
    }

    if ($this->showBackgroundImageField()) {
      $form['background_image'] = [
        '#type' => 'media_library',
        '#allowed_bundles' => ['image'],
        '#title' => t('Background image'),
        '#default_value' => $this->configuration['background_image'],
        '#description' => t('Upload or select a background image.'),
      ];
    }

    if ($this->showBorderedOption()) {
      $form['bordered'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Border Children'),
        '#default_value' => $this->configuration['bordered'],
        '#description' => $this->t('When checked all children will have a border.'),
      ];
    }

    if ($this->showEdgeToEdgeOption()) {
      $form['edge_to_edge'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Edge to Edge'),
        '#default_value' => $this->configuration['edge_to_edge'],
        '#description' => $this->t('When checked this layout will span the entire width of the page.'),
      ];
    }

    if ($this->showInsetOption()) {
      $form['inset'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Inset'),
        '#default_value' => $this->configuration['inset'],
        '#description' => $this->t('When checked this layout will be inset.'),
      ];
    }

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (count($this->columnWidthOptions())) {
      $this->configuration['column_width'] = $form_state->getValue('column_width');
    }

    if (count($this->backgroundColorOptions())) {
      $this->configuration['background_color'] = $form_state->getValue('background_color');
    }

    if ($this->showBackgroundImageField()) {
      $this->configuration['background_image'] = $form_state->getValue('background_image');
    }

    if ($this->showBorderedOption()) {
      $this->configuration['bordered'] = $form_state->getValue('bordered');
    }

    if ($this->showEdgeToEdgeOption()) {
      $this->configuration['edge_to_edge'] = $form_state->getValue('edge_to_edge');
    }

    if ($this->showInsetOption()) {
      $this->configuration['inset'] = $form_state->getValue('inset');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    if (count($this->columnWidthOptions())) {
      $build['#attributes']['class'][] = 'layout--' . $this->configuration['column_width'];
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

    if ($this->showBorderedOption()) {
      if ($this->configuration['bordered'] === 1) {
        $build['#attributes']['class'][] = 'layout--bordered';
      }
    }

    if ($this->showEdgeToEdgeOption()) {
      if ($this->configuration['edge_to_edge'] === 1) {
        $build['#attributes']['class'][] = 'layout--edge-to-edge';
      }
    }

    if ($this->showInsetOption()) {
      if ($this->configuration['inset'] === 1) {
        $build['#attributes']['class'][] = 'layout--inset';
      }
    }

    return $build;
  }

  /**
   * Gets the width options for the configuration form.
   *
   * The first option will be used as the default 'column_width' configuration
   * value.
   *
   * @return string[]
   *   The width options array where the keys are strings that will be added to
   *   the CSS classes and the values are the human readable labels.
   */
  protected function columnWidthOptions() {
    return [];
  }

  /**
   * Provides a default value for the width options.
   *
   * @return string
   *   A key from the array returned by ::getColumnWidthOptions().
   */
  protected function setDefaultColumnWidth() {
    // Return the first available key from the list of options.
    $width_classes = array_keys($this->columnWidthOptions());
    return array_shift($width_classes);
  }

  /**
   * Create an array of gutter width options for the configuration form.
   *
   * @return string[]
   *   The width options array where the keys are strings that will be added to
   *   the CSS classes and the values are the human readable labels.
   */
  protected function gutterWidthOptions() {
    return [
      'sm' => 'Small',
      'md' => 'Medium',
      'lg' => 'Large',
    ];
  }

  protected function showGutterWidthOption() {
    return FALSE;
  }

  /**
   * Get the background color options for the configuration form.
   *
   * The first option will be used as the default 'background_color'
   * configuration value.
   *
   * @return string[]
   *   The background color options array where the keys are strings that will
   *   be added to the CSS classes and the values are the human readable labels.
   */
  protected function backgroundColorOptions() {
    return [];
  }

  /**
   * Sets whether or not the background image upload shows in the configuration.
   *
   * @return bool
   *   Show the option in the configuration form when TRUE, hide when FALSE.
   */
  protected function showBackgroundImageField() {
    return FALSE;
  }

  /**
   * Sets whether or not the bordered checkbox shows in the configuration.
   *
   * @return bool
   *   Show the option in the configuration form when TRUE, hide when FALSE.
   */
  protected function showBorderedOption() {
    return FALSE;
  }

  /**
   * Sets whether or not the edge to edge checkbox shows in the configuration.
   *
   * @return bool
   *   Show the option in the configuration form when TRUE, hide when FALSE.
   */
  protected function showEdgeToEdgeOption() {
    return FALSE;
  }

  /**
   * Sets whether or not the inset checkbox shows in the configuration.
   *
   * @return bool
   *   Show the option in the configuration form when TRUE, hide when FALSE.
   */
  protected function showInsetOption() {
    return FALSE;
  }

}
