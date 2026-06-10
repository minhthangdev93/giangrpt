(function () {
  'use strict';

  var sections = document.querySelectorAll('[data-rpt-about-tabs]');

  if (!sections.length) {
    return;
  }

  sections.forEach(function (section) {
    var buttons = section.querySelectorAll('[data-rpt-tab-button]');
    var panels = section.querySelectorAll('[data-rpt-tab-panel]');
    var nav = section.querySelector('.rpt-tabs-nav');
    var content = section.querySelector('.rpt-tabs-content');

    if (!buttons.length || !panels.length) {
      return;
    }

    function getStickyOffset() {
      var offset = parseInt(
        getComputedStyle(section).getPropertyValue('--rpt-about-tabs-sticky-top'),
        10
      );

      return Number.isFinite(offset) && offset > 0 ? offset : 72;
    }

    function getTabsNavHeight() {
      return nav && nav.offsetHeight ? nav.offsetHeight : 0;
    }

    /**
     * Scroll so content starts below header + sticky tab bar.
     * Do not use sticky nav rect — it reports the stuck viewport position.
     */
    function getTabScrollTop() {
      var anchor = content || section;
      var stickyTop = getStickyOffset();
      var navHeight = getTabsNavHeight();
      var scrollY = window.pageYOffset || document.documentElement.scrollTop;
      var top = anchor.getBoundingClientRect().top + scrollY - stickyTop - navHeight;

      return Math.max(0, Math.round(top));
    }

    function scrollToTabStart() {
      window.scrollTo({
        top: getTabScrollTop(),
        behavior: 'auto',
      });
    }

    function activateTab(index) {
      buttons.forEach(function (button) {
        var isActive = button.getAttribute('data-tab-index') === String(index);
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      panels.forEach(function (panel) {
        var isActive = panel.getAttribute('data-tab-index') === String(index);
        panel.classList.toggle('is-active', isActive);

        if (isActive) {
          panel.removeAttribute('hidden');
        } else {
          panel.setAttribute('hidden', 'hidden');
        }
      });
    }

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        var index = button.getAttribute('data-tab-index');

        if (button.classList.contains('is-active')) {
          return;
        }

        activateTab(index);
        scrollToTabStart();

        requestAnimationFrame(function () {
          scrollToTabStart();
        });
      });
    });
  });
})();
