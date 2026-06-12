(function () {
  'use strict';

  var gallery = document.querySelector('[data-rpt-product-gallery]');

  if (!gallery) {
    return;
  }

  var mainImg = gallery.querySelector('[data-rpt-gallery-main]');
  var playBtn = gallery.querySelector('[data-rpt-gallery-play]');
  var thumbs = gallery.querySelectorAll('[data-rpt-gallery-thumb]');
  var prevBtn = gallery.querySelector('[data-rpt-gallery-prev]');
  var nextBtn = gallery.querySelector('[data-rpt-gallery-next]');
  var images = [];

  try {
    images = JSON.parse(gallery.getAttribute('data-rpt-gallery-images') || '[]');
  } catch (error) {
    images = [];
  }

  if (!mainImg || !images.length) {
    return;
  }

  var current = 0;

  function togglePlayButton(index) {
    if (!playBtn) {
      return;
    }

    var slide = images[index] || {};
    var isVideoSlide = slide.type === 'video';

    if (isVideoSlide) {
      playBtn.hidden = false;
    } else {
      playBtn.hidden = true;
    }
  }

  function show(index) {
    if (!images.length) {
      return;
    }

    current = (index + images.length) % images.length;
    mainImg.src = images[current].full;
    mainImg.alt = images[current].alt || '';

    thumbs.forEach(function (thumb) {
      var thumbIndex = parseInt(thumb.getAttribute('data-index'), 10);
      var isActive = thumbIndex === current;

      if (Number.isNaN(thumbIndex)) {
        return;
      }

      thumb.classList.toggle('is-active', isActive);
      thumb.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    togglePlayButton(current);
  }

  thumbs.forEach(function (thumb) {
    thumb.addEventListener('click', function () {
      var thumbIndex = parseInt(thumb.getAttribute('data-index'), 10);

      if (!Number.isNaN(thumbIndex)) {
        show(thumbIndex);
      }
    });
  });

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      show(current - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      show(current + 1);
    });
  }

  togglePlayButton(current);
})();
