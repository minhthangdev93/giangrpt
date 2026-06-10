(function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('[data-rpt-home-carousel]').forEach(function (slider) {
    var viewport = slider.querySelector('[data-rpt-carousel-viewport]');
    var prev = slider.querySelector('[data-rpt-carousel-prev]');
    var next = slider.querySelector('[data-rpt-carousel-next]');
    var slides = slider.querySelectorAll('[data-rpt-carousel-slide]');

    if (!viewport || !slides.length) {
      return;
    }

    function getGap() {
      var track = slider.querySelector('[data-rpt-carousel-track]');

      if (!track) {
        return 22;
      }

      var gap = parseFloat(window.getComputedStyle(track).columnGap || window.getComputedStyle(track).gap);

      return Number.isFinite(gap) ? gap : 22;
    }

    function getScrollStep() {
      var slide = slides[0];

      if (!slide) {
        return viewport.clientWidth;
      }

      return slide.offsetWidth + getGap();
    }

    function scrollByStep(direction) {
      viewport.scrollBy({
        left: direction * getScrollStep(),
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
      });
    }

    function updateArrows() {
      if (!prev || !next) {
        return;
      }

      var maxScroll = viewport.scrollWidth - viewport.clientWidth;
      var tolerance = 4;

      prev.disabled = viewport.scrollLeft <= tolerance;
      next.disabled = viewport.scrollLeft >= maxScroll - tolerance;
    }

    if (prev) {
      prev.addEventListener('click', function () {
        scrollByStep(-1);
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        scrollByStep(1);
      });
    }

    viewport.addEventListener('scroll', updateArrows, { passive: true });
    window.addEventListener('resize', updateArrows);

    updateArrows();
  });
})();
