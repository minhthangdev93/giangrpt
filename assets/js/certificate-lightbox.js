(function () {
  'use strict';

  var lightbox = document.getElementById('rpt-certificate-lightbox');

  if (!lightbox) {
    return;
  }

  var image = lightbox.querySelector('.rpt-certificate-lightbox__image');
  var caption = lightbox.querySelector('.rpt-certificate-lightbox__caption');
  var closeButtons = lightbox.querySelectorAll('[data-rpt-certificate-close]');
  var lastTrigger = null;

  function openLightbox(trigger) {
    var src = trigger.getAttribute('data-rpt-certificate-src');
    var title = trigger.getAttribute('data-rpt-certificate-title') || '';

    if (!src || !image) {
      return;
    }

    lastTrigger = trigger;
    image.src = src;
    image.alt = title;

    if (caption) {
      caption.textContent = title;
      caption.hidden = !title;
    }

    lightbox.hidden = false;
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.classList.add('rpt-certificate-lightbox-open');

    if (window.rptScrollLock) {
      window.rptScrollLock.lock();
    }

    var closeBtn = lightbox.querySelector('.rpt-certificate-lightbox__close');

    if (closeBtn) {
      closeBtn.focus();
    }
  }

  function closeLightbox() {
    if (image) {
      image.removeAttribute('src');
      image.alt = '';
    }

    if (caption) {
      caption.textContent = '';
      caption.hidden = true;
    }

    lightbox.hidden = true;
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('rpt-certificate-lightbox-open');

    if (window.rptScrollLock) {
      window.rptScrollLock.unlock();
    }

    if (lastTrigger) {
      lastTrigger.focus();
      lastTrigger = null;
    }
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-rpt-certificate-open]');

    if (!trigger) {
      return;
    }

    event.preventDefault();
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
})();
