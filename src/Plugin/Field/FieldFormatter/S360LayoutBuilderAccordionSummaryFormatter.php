<?php

namespace Drupal\s360_layout_builder\Plugin\Field\FieldFormatter;

use Drupal\advanced_header_field\AdvancedHeaderFieldHelper;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Path\PathValidatorInterface;
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
   * The advanced header field helper service.
   *
   * @var \Drupal\advanced_header_field\AdvancedHeaderFieldHelper
   */
  protected $advancedHeaderFieldHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    protected readonly AdvancedHeaderFieldHelper $advanced_header_field_helper,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->advancedHeaderFieldHelper = $advanced_header_field_helper;
  }

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

    $parent_id = $this->advancedHeaderFieldHelper->getHeaderParentId($item);

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
          : $this->advancedHeaderFieldHelper->createAnchorIdFromText($values['title'], $parent_id),
      ],
    ];

    return $element;
  }

}
