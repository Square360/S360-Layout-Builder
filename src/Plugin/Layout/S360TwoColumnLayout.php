<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

/**
 * Base two column layout class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class S360TwoColumnLayout extends S360BaseLayout {

  protected function showGutterWidthOption() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function columnWidthOptions() {
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
  protected function setDefaultColumnWidth() {
    return '50-50';
  }

}
