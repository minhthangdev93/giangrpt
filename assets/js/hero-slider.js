(function () {
  'use strict';

  var slider = document.querySelector('[data-rpt-hero-slider]');

  if (!slider) {
    return;
  }

  var slides = slider.querySelectorAll('[data-rpt-hero-slide]');
  var dots = slider.querySelectorAll('[data-rpt-hero-dot]');
  var prev = slider.querySelector('[data-rpt-hero-prev]');
  var next = slider.querySelector('[data-rpt-hero-next]');
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!slides.length) {
    return;
  }

  var current = 0;
  var timer;
  var autoplayMs = parseInt(slider.getAttribute('data-rpt-hero-autoplay'), 10);

  if (!Number.isFinite(autoplayMs)) {
    autoplayMs = 4000;
  }

  autoplayMs = Math.min(5000, Math.max(3000, autoplayMs));

  function showSlide(index) {
    current = (index + slides.length) % slides.length;

    slides.forEach(function (slide, i) {
      slide.classList.toggle('is-active', i === current);
    });

    dots.forEach(function (dot, i) {
      var isActive = i === current;
      dot.classList.toggle('is-active', isActive);

      if (isActive) {
        dot.setAttribute('aria-current', 'true');
      } else {
        dot.removeAttribute('aria-current');
      }
    });
  }

  function nextSlide() {
    showSlide(current + 1);
  }

  function prevSlide() {
    showSlide(current - 1);
  }

  function startAutoplay() {
    if (prefersReducedMotion || slides.length < 2) {
      return;
    }

    window.clearInterval(timer);
    timer = window.setInterval(nextSlide, autoplayMs);
  }

  function stopAutoplay() {
    window.clearInterval(timer);
  }

  if (prev) {
    prev.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      prevSlide();
      startAutoplay();
    });
  }

  if (next) {
    next.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      nextSlide();
      startAutoplay();
    });
  }

  dots.forEach(function (dot) {
    dot.addEventListener('click', function () {
      var index = parseInt(dot.getAttribute('data-rpt-hero-dot'), 10);

      if (Number.isFinite(index)) {
        showSlide(index);
        startAutoplay();
      }
    });
  });

  if (!prefersReducedMotion) {
    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', startAutoplay);
    startAutoplay();
  }
})();
