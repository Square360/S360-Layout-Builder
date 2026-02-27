(function (Drupal, once) {
  'use strict';

  const DOM_ELEMENTS = {};

  // Header field selectors that should be hidden when accordion layout is selected
  const HEADER_FIELD_SELECTORS = [
    '[name*="[header][heading][subtitle]"]',
    '[name*="[header][heading][heading_tag]"]',
    '[name*="[header][header_options][show_in_jump_menu]"]',
    '[class="advanced-header-field__options-group"]',
  ];

  const LAYOUT_SELECTOR = '[name="layout_paragraphs[layout]"]:checked';
  const ACCORDION_LAYOUT_PATTERN = 'accordion';

  Drupal.behaviors.conditionalHeaderFields = {
    attach(context) {
      // Only process form contexts
      if (context.tagName !== 'FORM') {
        return;
      }

      // Find layout radio buttons and initialize behavior
      const layoutRadioButtons = once('conditional-header-fields', LAYOUT_SELECTOR, context);
      if (!layoutRadioButtons.length) {
        return;
      }

      // Cache DOM elements for performance
      this.cacheHeaderFieldElements(context);

      // Set initial state based on current selection
      this.updateHeaderFieldVisibility(layoutRadioButtons[0].value);

      // Listen for layout changes
      this.attachLayoutChangeListener(context);
    },

    /**
     * Cache header field elements to avoid repeated DOM queries
     */
    cacheHeaderFieldElements(context) {
      HEADER_FIELD_SELECTORS.forEach(selector => {
        const element = context.querySelector(selector);

        if (element) {
          const container = selector.includes('[name') ? element.parentElement : element;
          DOM_ELEMENTS[selector] = { container };
        }
      });
    },

    /**
     * Attach event listener for layout changes
     */
    attachLayoutChangeListener(context) {
      const layoutRadios = context.querySelectorAll('[name="layout_paragraphs[layout]"]');

      layoutRadios.forEach(radio => {
        radio.addEventListener('change', (event) => {
          this.updateHeaderFieldVisibility(event.target.value);
        });
      });
    },

    /**
     * Update visibility of header fields based on selected layout
     * @param {string} selectedLayout - The selected layout value
     */
    updateHeaderFieldVisibility(selectedLayout) {
      const isAccordionLayout = selectedLayout.includes(ACCORDION_LAYOUT_PATTERN);

      HEADER_FIELD_SELECTORS.forEach(selector => {
        const domElement = DOM_ELEMENTS[selector];

        if (!domElement?.container) {
          return;
        }

        if (isAccordionLayout) {
          domElement.container.setAttribute('hidden', 'hidden');
        }
        else {
          domElement.container.removeAttribute('hidden');
        }
      });
    }
  };
})(Drupal, once);
