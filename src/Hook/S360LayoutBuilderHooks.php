<?php

declare(strict_types=1);

namespace Drupal\s360_layout_builder\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\s360_layout_builder\S360LayoutBuilderHelper;

/**
 * Provides hook implementations for S360 Layout Builder.
 */
final class S360LayoutBuilderHooks {

  use StringTranslationTrait;

  /**
   * Hook implementations for s360_layout_builder.
   *
   * @param \Drupal\Core\Routing\AdminContext $adminContext
   *   The admin context service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\s360_layout_builder\S360LayoutBuilderHelper $s360LayoutBuilderHelper
   *   The S360 Layout Builder Helper service.
   */
  public function __construct(
    protected readonly AdminContext $adminContext,
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly S360LayoutBuilderHelper $s360LayoutBuilderHelper,
  ) {}

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    $layout_paragraph_bundles = $this->s360LayoutBuilderHelper->getLayoutParagraphBundles();

    $layout_themes = [];

    foreach ($layout_paragraph_bundles as $layout_paragraph_bundle) {
      $layout_themes["paragraph__{$layout_paragraph_bundle}"] = [
        'template' => 'paragraph--layout' ,
        'base hook' => 'paragraph',
      ];

      $layout_themes["paragraph__{$layout_paragraph_bundle}__preview"] = [
        'template' => 'paragraph--layout--preview' ,
        'base hook' => 'paragraph',
      ];
    }

    $layouts = [
      's360_layout_builder_one_column',
      's360_layout_builder_two_column',
      's360_layout_builder_three_column',
      's360_layout_builder_four_column',
      's360_layout_builder_accordion',
      's360_layout_builder_flex',
    ];

    foreach ($layouts as $layout) {
      $layout_themes["{$layout}"] = [
        'template' => str_replace('_', '-', $layout),
        'render element' => 'content',
        'base hook' => 'layout',
      ];

      $layout_themes["{$layout}__admin"] = [
        'template' => str_replace('_', '-', "{$layout}--admin"),
        'render element' => 'content',
        'base hook' => 'layout',
      ];
    }

    return $layout_themes;
  }

  /**
   * Implements hook_page_attachments_alter().
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    if ($this->adminContext->isAdminRoute()) {
      $attachments['#attached']['library'][] = 's360_layout_builder/admin';
    }
  }

  /**
   * Implements hook_preprocess_paragraph().
   */
  #[Hook('preprocess_paragraph')]
  public function preprocessParagraph(array &$variables): void {
    /** @var \Drupal\paragraph\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];

    if (in_array($paragraph->getType(), $this->s360LayoutBuilderHelper->getLayoutParagraphBundles())) {
      $this->s360LayoutBuilderHelper->processLayoutParagraph($variables);
    }
  }

  /**
   * Implements hook_modules_installed().
   */
  #[Hook('modules_installed')]
  public function modulesInstalled(array $modules, bool $is_syncing): void {
    if (in_array('s360_layout_builder', $modules, TRUE)) {
      $config = $this->configFactory->getEditable('layout_paragraphs.settings');

      $config->set('show_paragraph_labels', TRUE)->save();
      $config->set('show_layout_labels', TRUE)->save();
    }
  }

  /**
   * Implements hook_theme_suggestions_layout_alter().
   */
  #[Hook('theme_suggestions_layout_alter')]
  public function themeSuggestionsLayoutAlter(array &$suggestions, array $variables): void {
    $theme = $variables['content']['#theme'];

    if ($this->s360LayoutBuilderHelper->isEditContext()) {
      if (str_contains($theme, 's360_layout_builder')) {
        $suggestions[] = "{$theme}__admin";
      }
    }
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_layout_paragraphs_component_form_alter')]
  public function formLayoutParagraphsComponentFormAlter(&$form, FormStateInterface $form_state, string $form_id): void {
    $form['#attached']['library'][] = 's360_layout_builder/conditional-header-fields';
  }

}
