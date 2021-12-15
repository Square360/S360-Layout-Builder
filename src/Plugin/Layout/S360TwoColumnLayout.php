<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

/**
 * Base two column layout class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class S360TwoColumnLayout extends S360BaseLayout {

  /**
   * {@inheritdoc}
   */
  protected function columnRatioOptions() {
    return [
      '50-50' => '50%/50%',
      '60-40' => '60%/40%',
      '40-60' => '40%/60%',
      '70-30' => '70%/30%',
      '30-70' => '30%/70%',
      '75-25' => '75%/25%',
      '25-75' => '25%/75%',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function gutterWidthOptions() {
    return $this->defaultGutterWidthOptions();
  }

  /**
   * {@inheritdoc}
   */
  protected function columnSeparatorOptions() {
    return $this->defaultColumnSeparatorOptions();
  }

  /**
   * {@inheritdoc}
   */
  protected function backgroundColorOptions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function showBackgroundImageField() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function layoutWidthOptions() {
    return $this->defaultLayoutWidthOptions();
  }

  /**
   * {@inheritdoc}
   */
  protected function marginBottomOptions() {
    return $this->defaultMarginBottomOptions();
  }

}
