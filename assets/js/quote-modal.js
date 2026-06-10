(function () {
  'use strict';

  var modal = document.getElementById('rpt-quote-modal');

  if (!modal || typeof rptQuoteModal === 'undefined') {
    return;
  }

  var form = modal.querySelector('[data-rpt-quote-form]');
  var feedback = modal.querySelector('[data-rpt-quote-feedback]');
  var submitBtn = modal.querySelector('[data-rpt-quote-submit]');
  var productSummary = modal.querySelector('[data-rpt-quote-product-summary]');
  var productLink = modal.querySelector('[data-rpt-quote-product-link]');
  var nonceField = modal.querySelector('[data-rpt-quote-nonce]');
  var productIdField = modal.querySelector('[data-rpt-quote-product-id]');
  var productNameField = modal.querySelector('[data-rpt-quote-product-name]');
  var productUrlField = modal.querySelector('[data-rpt-quote-product-url]');
  var closeButtons = modal.querySelectorAll('[data-rpt-quote-close]');
  var lastTrigger = null;
  var isSubmitting = false;

  if (nonceField) {
    nonceField.value = rptQuoteModal.nonce;
  }

  function setFeedback(message, type) {
    if (!feedback) {
      return;
    }

    feedback.textContent = message;
    feedback.hidden = !message;
    feedback.classList.toggle('is-error', type === 'error');
    feedback.classList.toggle('is-success', type === 'success');
  }

  function setProductContext(product) {
    var hasProduct = product && product.name;

    if (productSummary) {
      productSummary.hidden = !hasProduct;
    }

    if (productLink && hasProduct) {
      productLink.textContent = product.name;
      productLink.href = product.url || '#';
    }

    if (productIdField) {
      productIdField.value = product && product.id ? product.id : '';
    }

    if (productNameField) {
      productNameField.value = product && product.name ? product.name : '';
    }

    if (productUrlField) {
      productUrlField.value = product && product.url ? product.url : '';
    }
  }

  function openModal(trigger) {
    var product = {
      id: trigger.getAttribute('data-rpt-quote-product-id') || '',
      name: trigger.getAttribute('data-rpt-quote-product-name') || '',
      url: trigger.getAttribute('data-rpt-quote-product-url') || '',
    };

    lastTrigger = trigger;
    setProductContext(product);
    setFeedback('', '');

    if (form) {
      form.reset();

      if (nonceField) {
        nonceField.value = rptQuoteModal.nonce;
      }

      setProductContext(product);
    }

    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('rpt-quote-modal-open');

    if (window.rptScrollLock) {
      window.rptScrollLock.lock();
    }

    var firstField = modal.querySelector('#rpt-quote-name');

    if (firstField) {
      window.setTimeout(function () {
        firstField.focus();
      }, 50);
    }
  }

  function closeModal() {
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('rpt-quote-modal-open');
    setFeedback('', '');

    if (window.rptScrollLock) {
      window.rptScrollLock.unlock();
    }

    if (lastTrigger) {
      lastTrigger.focus();
      lastTrigger = null;
    }
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-rpt-quote-open]');

    if (!trigger) {
      return;
    }

    event.preventDefault();

    var mobileMenu = document.getElementById('rpt-mobile-menu');

    if (mobileMenu && !mobileMenu.hidden) {
      mobileMenu.hidden = true;
      mobileMenu.setAttribute('aria-hidden', 'true');

      var menuOpenBtn = document.querySelector('[data-rpt-menu-open]');

      if (menuOpenBtn) {
        menuOpenBtn.setAttribute('aria-expanded', 'false');
      }
    }

    openModal(trigger);
  });

  closeButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      closeModal();
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !modal.hidden) {
      closeModal();
    }
  });

  if (!form) {
    return;
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    if (isSubmitting) {
      return;
    }

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    isSubmitting = true;
    setFeedback('', '');

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-busy', 'true');
    }

    var formData = new FormData(form);
    formData.set('action', 'rpt_submit_quote_request');
    formData.set('nonce', rptQuoteModal.nonce);

    fetch(rptQuoteModal.ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (payload) {
        if (!payload || !payload.success) {
          throw new Error(
            payload && payload.data && payload.data.message
              ? payload.data.message
              : rptQuoteModal.errorMessage
          );
        }

        setFeedback(payload.data.message || rptQuoteModal.successMessage, 'success');
        form.reset();
        setProductContext({
          id: productIdField ? productIdField.value : '',
          name: productNameField ? productNameField.value : '',
          url: productUrlField ? productUrlField.value : '',
        });
      })
      .catch(function (error) {
        setFeedback(error.message || rptQuoteModal.errorMessage, 'error');
      })
      .finally(function () {
        isSubmitting = false;

        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.removeAttribute('aria-busy');
        }
      });
  });
})();
