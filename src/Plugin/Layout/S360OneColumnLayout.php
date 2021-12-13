<?php

namespace Drupal\s360_layout_builder\Plugin\Layout;

/**
 * Base one column layout class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class S360OneColumnLayout extends S360BaseLayout {

  /**
   * {@inheritdoc}
   */
  protected function showBorderedOption() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function showEdgeToEdgeOption() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function showInsetOption() {
    return TRUE;
  }

}
