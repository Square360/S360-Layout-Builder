<?php

namespace Drupal\s360_layout_builder\Plugin\Field\FieldFormatter;

use Drupal\advanced_header_field\AdvancedHeaderFieldInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 's360_layout_builder' summary formatter.
 */
#[FieldFormatter(
  id: 's360_layout_builder_accordion_summary',
  label: new TranslatableMarkup('Accordion Summary Markup'),
  field_types: [
    'advanced_header_field',
  ]
)]
class S360LayoutBuilderAccordionSummaryFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewElement($item);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function viewElement(FieldItemInterface $item) {
    $values = $item->getValue();

    if (!$values['title']) {
      return;
    }

    $options = $values['options'];

    $parent_id = AdvancedHeaderFieldInterface::getHeaderParentId($item);

    $element = [
      '#type' => 'html_tag',
      '#tag' => 'summary',
      '#value' => Markup::create('<span>' . $values['title'] . '</span>'),
      '#attributes' => [
        'class' => [
          'layout__summary',
        ],
        'id' => !empty($options['custom_anchor_id'])
          ? $options['custom_anchor_id']
          : AdvancedHeaderFieldInterface::createAnchorIdFromText($values['title'], $parent_id),
      ],
    ];

    return $element;
  }

}
