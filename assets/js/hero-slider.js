(function () {
  'use strict';

  var slider = document.querySelector('[data-rpt-hero-slider]');

  if (!slider || slider.getAttribute('data-rpt-hero-initialized') === '1') {
    return;
  }

  slider.setAttribute('data-rpt-hero-initialized', '1');

  var slides = slider.querySelectorAll('[data-rpt-hero-slide]');
  var dots = slider.querySelectorAll('[data-rpt-hero-dot]');
  var prev = slider.querySelector('[data-rpt-hero-prev]');
  var next = slider.querySelector('[data-rpt-hero-next]');

  if (slides.length < 2) {
    return;
  }

  var current = 0;
  var timer = null;
  var pausedByHover = false;
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

  function stopAutoplay() {
    if (timer !== null) {
      window.clearInterval(timer);
      timer = null;
    }
  }

  function startAutoplay() {
    stopAutoplay();

    if (pausedByHover || document.hidden) {
      return;
    }

    timer = window.setInterval(nextSlide, autoplayMs);
  }

  if (prev) {
    prev.addEventListener('click', function (event) {
      event.preventDefault();
      prevSlide();
      startAutoplay();
    });
  }

  if (next) {
    next.addEventListener('click', function (event) {
      event.preventDefault();
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

  slider.addEventListener('mouseenter', function () {
    pausedByHover = true;
    stopAutoplay();
  });

  slider.addEventListener('mouseleave', function () {
    pausedByHover = false;
    startAutoplay();
  });

  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      stopAutoplay();
      return;
    }

    startAutoplay();
  });

  startAutoplay();
})();
