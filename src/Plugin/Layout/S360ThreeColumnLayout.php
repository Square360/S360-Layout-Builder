<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

/**
 * Base three column layout class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class S360ThreeColumnLayout extends S360BaseLayout {

  /**
   * {@inheritdoc}
   */
  protected function getColumnWidthOptions() {
    return [
      '33-33-33' => '33%/33%/33%',
      '25-50-25' => '25%/50%/25%',
      '50-25-25' => '50%/25%/25%',
      '25-25-50' => '25%/25%/50%',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultColumnWidth() {
    return '33-33-33';
  }

}
