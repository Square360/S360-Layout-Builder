<?php

declare(strict_types=1);

namespace Drupal\s360_layout_builder;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Helper class for s360 layout builder operations.
 */
class S360LayoutBuilderHelper {

  /**
   * Construct an S360LayoutBuilderHelper service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entityTypeBundleInfo
   *   The entity type bundle info service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    protected readonly CacheBackendInterface $cache,
    protected readonly RouteMatchInterface $routeMatch,
    protected LoggerInterface $logger
  ) {}

  /**
   * Checks the route name to see if it's an "admin route".
   *
   * @return bool
   *   Returns true if the current route is found in the array, false otherwise.
   */
  public function isAdminRoute(): bool {
    $admin_paths = [
      'node.add',
      'entity.node.edit_form',
      'entity.group.edit_form',
      'layout_paragraphs',
    ];

    $route_name = $this->routeMatch->getRouteName();

    if (empty($route_name)) {
      return FALSE;
    }

    // Check each pattern and return immediately on first match.
    foreach ($admin_paths as $pattern) {
      if (str_starts_with($route_name, $pattern)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Helper function to get all paragraphs that have layout paragraphs.
   *
   * @return string[]
   *   An array of paragraph bundles.
   */
  public function getLayoutParagraphBundles(): array {
    $cache_key = 's360_layout_builder:layout_paragraphs';

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }

    $paragraph_bundles = $this->entityTypeBundleInfo->getBundleInfo('paragraph');

    // Load all paragraph types at once.
    $bundle_ids = array_keys($paragraph_bundles);

    try {
      $paragraphs_types = $this->entityTypeManager
        ->getStorage('paragraphs_type')
        ->loadMultiple($bundle_ids);
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to load paragraph types: @message', [
        '@message' => $e->getMessage(),
      ]);

      return [];
    }

    $layout_paragraph_bundles = [];

    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraphs_type */
    foreach ($paragraphs_types as $paragraph_bundle => $paragraphs_type) {
      $layout_paragraphs_behavior = $paragraphs_type->getBehaviorPlugin('layout_paragraphs');
      $layout_paragraphs_behavior_config = $layout_paragraphs_behavior->getConfiguration();

      if (!empty($layout_paragraphs_behavior_config['enabled'])) {
        $layout_paragraph_bundles[] = $paragraph_bundle;
      }
    }

    $this->cache->set(
      $cache_key,
      $layout_paragraph_bundles,
      CacheBackendInterface::CACHE_PERMANENT,
      ['paragraph_list']
    );

    return $layout_paragraph_bundles;
  }

  /**
   * Apply classes and additional content for a layout paragraph.
   *
   * @param array $variables
   *   The array of variables passed in from Drupal's preprocess.
   *
   * @see S360LayoutBuilderHooks::preprocessParagraph()
   */
  public function processLayoutParagraph(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];
    $paragraph_is_published = $paragraph->isPublished();

    $layout_paragraphs_layout = $paragraph->getBehaviorSetting('layout_paragraphs', 'layout');

    if (!$paragraph_is_published) {
      $variables['content']['regions']['#attributes']['data-unpublished'] = 'layout';
    }

    $variables['content']['regions']['#attributes']['id'] = 'layout-' . $paragraph->id();
    $variables['content']['regions']['#layout_tag'] = 'div';

    if ($paragraph->hasField('field_header')) {
      $field_header = $paragraph->get('field_header');

      if (!$field_header->isEmpty()) {
        $field_header_value = $field_header->first()->getValue();

        if (isset($field_header_value['title']) && !empty($field_header_value['title'])) {
          $variables['content']['regions']['#attributes']['class'][] = 'layout--has-header';

          if (str_contains($layout_paragraphs_layout, 'accordion')) {
            $variables['content']['regions']['#summary'] = $field_header->view([
              'type' => 's360_layout_builder_accordion_summary',
              'label' => 'hidden',
            ]);
          }
          else {
            $field_header = $field_header->view(['label' => 'hidden']);

            $variables['content']['regions']['#attributes']['aria-labelledby'] = $field_header[0]['#header_id'];
            $variables['content']['regions']['#layout_tag'] = 'section';
            $variables['content']['regions']['#header'] = $field_header;
          }
        }
      }
    }

    if (S360LayoutBuilderHelper::isAdminRoute()) {
      if (!$paragraph_is_published) {
        $variables['attributes']['class'][] = 'paragraph--unpublished';
      }

      if ($paragraph->id()) {
        $variables['content']['layout'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => [
              'field',
              'field--label-inline',
            ],
          ],
          'child' => [
            [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => ucwords(str_replace('_', ' ', str_replace('layout_custom_', '', $layout_paragraphs_layout))),
              '#attributes' => [
                'class' => 'field__label',
              ],
            ],
            [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => "(ID: #{$paragraph->id()})",
              '#attributes' => [
                'class' => 'field__item',
              ],
            ],
          ],
        ];
      }

      $layout_paragraphs_config = $paragraph->getBehaviorSetting('layout_paragraphs', 'config');

      if (isset($layout_paragraphs_config['layout'])) {
        $layout_config = (array) $layout_paragraphs_config['layout'];

        $variables['content']['layout_options'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => 'layout-options',
          ],
          'child' => [
            [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => 'Layout Options',
              '#attributes' => [
                'class' => 'layout-options__label',
              ],
            ],
          ],
        ];

        $variables['content']['layout_options']['layout_options_group'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => 'layout-options__options-group',
          ],
        ];

        foreach ($layout_config as $layout_config_key => $layout_config_value) {
          /*
           * Patterns:
           *  layout_[*]_options
           *  layout_[one|two|three|four]_column_[*]_options
           *  layout_flex_[*]_options
           */
          preg_match('/(?:layout_(?:(?:one|two|three|four)_column_|flex_)?)(.*?)_options/', $layout_config_key, $matches);

          // When there is no layout option match or there is no value for that
          // option, move onto the next value.
          if (!$matches || !$layout_config_value[key($layout_config_value)]) {
            continue;
          }

          $variables['content']['layout_options']['layout_options_group']['field_' . $matches[1]] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
              'class' => 'layout-options__option',
            ],
            'child' => [
              [
                '#type' => 'html_tag',
                '#tag' => 'div',
                '#value' => ucwords(str_replace('_', ' ', $matches[1])),
                '#attributes' => [
                  'class' => 'layout-options__option-label',
                ],
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'div',
                '#value' => is_int($layout_config_value[key($layout_config_value)])
                  ? ($layout_config_value[key($layout_config_value)]
                    ? 'true'
                    : 'false')
                  : $layout_config_value[key($layout_config_value)],
                '#attributes' => [
                  'class' => 'layout-options__option-value',
                ],
              ],
            ],
          ];
        }
      }

      if (isset($layout_paragraphs_config['regions'])) {
        $regions = (array) $layout_paragraphs_config['regions'];

        foreach ($regions as $region_key => $region_configs) {
          // Adds the region options at the top.
          array_unshift($variables['content']['regions'][$region_key], [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
              'class' => 'region-options',
            ],
            'child' => [
              [
                '#type' => 'html_tag',
                '#tag' => 'div',
                '#value' => 'Region Options',
                '#attributes' => [
                  'class' => 'region-options__label',
                ],
              ],
            ],
          ]);

          foreach ($region_configs as $region_config_key => $region_config_value) {
            preg_match('/(?:layout_region_)(.*?)(?=_options)/', $region_config_key, $matches);

            // When there is no layout option match or there is no value for
            // that option, move onto the next value.
            if (!$matches || !$region_config_value[key($region_config_value)]) {
              continue;
            }

            $variables['content']['regions'][$region_key][0]['field_' . $matches[1]] = [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#attributes' => [
                'class' => 'region-options__option',
              ],
              'child' => [
                [
                  '#type' => 'html_tag',
                  '#tag' => 'div',
                  '#value' => ucwords(str_replace('_', ' ', $matches[1])),
                  '#attributes' => [
                    'class' => 'region-options__option-label',
                  ],
                ],
                [
                  '#type' => 'html_tag',
                  '#tag' => 'div',
                  '#value' => is_int($region_config_value[key($region_config_value)])
                    ? ($region_config_value[key($region_config_value)]
                      ? 'true'
                      : 'false')
                    : $region_config_value[key($region_config_value)],
                  '#attributes' => [
                    'class' => 'region-options__option-value',
                  ],
                ],
              ],
            ];
          }
        }
      }
    }
  }

  /**
   * Gets the logger service.
   *
   * @return \Psr\Log\LoggerInterface
   *   The logger service.
   */
  public function logger(): LoggerInterface {
    return $this->logger;
  }

}
