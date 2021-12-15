<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

/**
 * Base accordion layout class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class S360AccordionLayout extends S360BaseLayout {

  /**
   * {@inheritdoc}
   */
  protected function columnRatioOptions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function gutterWidthOptions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function columnSeparatorOptions() {
    return [];
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
