(function () {
  'use strict';

  function lockPageScroll() {
    document.documentElement.classList.add('rpt-video-lightbox-open');
    document.body.classList.add('rpt-video-lightbox-open');
  }

  function unlockPageScroll() {
    document.documentElement.classList.remove('rpt-video-lightbox-open');
    document.body.classList.remove('rpt-video-lightbox-open');
  }

  function initVideoLightbox() {
    var lightbox = document.getElementById('rpt-product-video-lightbox');

    if (!lightbox) {
      return;
    }

    // Outside <body> so body scroll-lock (position:fixed + top offset) cannot shift the overlay off-screen.
    if (lightbox.parentElement !== document.documentElement) {
      document.documentElement.appendChild(lightbox);
    }

    var content = lightbox.querySelector('.rpt-video-lightbox__content');
    var closeButtons = lightbox.querySelectorAll('[data-rpt-video-close]');
    var lastTrigger = null;

    function openLightbox(trigger) {
      var type = trigger.getAttribute('data-rpt-video-type');
      var src = trigger.getAttribute('data-rpt-video-src');
      var poster = trigger.getAttribute('data-rpt-video-poster') || '';
      var title = trigger.getAttribute('data-rpt-video-title') || '';

      if (!type || !src || !content) {
        return;
      }

      lastTrigger = trigger;
      content.innerHTML = '';

      if (type === 'iframe') {
        var iframe = document.createElement('iframe');
        iframe.src = src;
        iframe.title = title;
        iframe.setAttribute('allowfullscreen', '');
        iframe.setAttribute(
          'allow',
          'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
        );
        content.appendChild(iframe);
      } else {
        var video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.playsInline = true;
        video.setAttribute('preload', 'metadata');

        if (poster) {
          video.poster = poster;
        }

        content.appendChild(video);

        var playPromise = video.play();

        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.catch(function () {
            /* Autoplay may be blocked until user interacts. */
          });
        }
      }

      lightbox.hidden = false;
      lightbox.removeAttribute('hidden');
      lightbox.setAttribute('aria-hidden', 'false');
      lockPageScroll();

      var closeBtn = lightbox.querySelector('.rpt-video-lightbox__close');

      if (closeBtn) {
        closeBtn.focus();
      }
    }

    function closeLightbox() {
      if (content) {
        content.innerHTML = '';
      }

      lightbox.hidden = true;
      lightbox.setAttribute('hidden', '');
      lightbox.setAttribute('aria-hidden', 'true');
      unlockPageScroll();

      if (lastTrigger) {
        lastTrigger.focus();
        lastTrigger = null;
      }
    }

    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('[data-rpt-video-play]');

      if (!trigger) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      openLightbox(trigger);
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        closeLightbox();
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !lightbox.hidden) {
        closeLightbox();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVideoLightbox);
  } else {
    initVideoLightbox();
  }
})();
