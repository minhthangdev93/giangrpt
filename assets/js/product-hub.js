(function () {
  'use strict';

  var hub = document.querySelector('.rpt-product-hub, .rpt-product-category-page');

  if (!hub) {
    return;
  }

  function initAccordion() {
    var accordion = hub.querySelector('[data-rpt-hub-accordion]');
    var trigger = hub.querySelector('[data-rpt-hub-accordion-trigger]');

    if (!accordion || !trigger) {
      return;
    }

    trigger.addEventListener('click', function () {
      var expanded = accordion.classList.toggle('is-open');
      trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    });
  }

  initAccordion();
})();
