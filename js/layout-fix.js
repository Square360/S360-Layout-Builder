(function (Drupal, once) {
    Drupal.behaviors.layoutFix = {
      attach(context) {
        if (context.tagName !== 'FORM') return;

        // Function once returns an array, so even though there could only be
        // checked radio it still needs to be plural.
        const LAYOUT_RADIO_BUTTONS = once('layout-fix', '[name="layout_paragraphs[layout]"]:checked', context);
        if (!LAYOUT_RADIO_BUTTONS) return;

        const DOM_SELECTORS_MAP = {
          '[name*="[header][heading][title]"]': '!required',
          '[name*="[header][heading][subtitle]"]': '!hidden',
          '[name*="[header][heading][heading_tag]"]': '!hidden',
          '[name*="[header][header_options][show_in_jump_menu]"]': '!hidden',
          '[class="advanced-header-field__options-group"]': '!hidden',
        };

        //
        const NEGATED_ATTRIBUTES_MAP = {
          '!required': 'required',
          '!hidden': 'hidden'
        }

        // Cache DOM elements.
        const DOM_ELEMENTS = {};
        Object.keys(DOM_SELECTORS_MAP).forEach(domSelector => {
          const element = context.querySelector(domSelector);

          if (element) {
            const input = domSelector.includes('[name') ? element : null;
            const container = domSelector.includes('[name') ? element.parentElement : element;
            const label = container && input ? container.querySelector('label') : null;

            DOM_ELEMENTS[domSelector] = { input, container, label };
          }
        });

        const updateDomElements = (selectedLayout) => {
          const IS_ACCORDION_LAYOUT = selectedLayout.includes('accordion');

          Object.keys(DOM_SELECTORS_MAP).forEach(domSelector => {
            const domElements = DOM_ELEMENTS[domSelector];
            const { input, container, label } = domElements;
            let attribute = IS_ACCORDION_LAYOUT
              ? NEGATED_ATTRIBUTES_MAP[DOM_SELECTORS_MAP[domSelector]]
              : DOM_SELECTORS_MAP[domSelector];

            if (attribute === '!required') {
              input?.removeAttribute('required');
              input?.removeAttribute('aria-required');
              label?.classList.remove('js-form-required', 'form-required');
            }
            else if (attribute === '!hidden') {
              container?.removeAttribute('hidden');
            }

            //
            if (IS_ACCORDION_LAYOUT) {
              if (attribute === 'required') {
                input?.setAttribute('required', 'required');
                input?.setAttribute('aria-required', true);
                label?.classList.add('js-form-required', 'form-required');
              }
              else if (attribute === 'hidden') {
                container?.setAttribute('hidden', true);
              }
            }
          });
        };

        updateDomElements(LAYOUT_RADIO_BUTTONS[0].value);
      }
    }

})(Drupal, once);
