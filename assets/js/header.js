(function () {
  'use strict';

  var header = document.querySelector('.rpt-site-header');
  var desktopSearch = document.querySelector('[data-rpt-desktop-search]');
  var searchOverlay = document.getElementById('rpt-search-overlay');
  var mobileMenu = document.getElementById('rpt-mobile-menu');
  var searchOpenBtn = document.querySelector('[data-rpt-search-open]');
  var menuOpenBtn = document.querySelector('[data-rpt-menu-open]');

  function lockScroll() {
    if (window.rptScrollLock) {
      window.rptScrollLock.lock();
    }
  }

  function unlockScroll() {
    if (window.rptScrollLock) {
      window.rptScrollLock.unlock();
    }
  }

  function setExpanded(trigger, expanded) {
    if (!trigger) {
      return;
    }
    trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  function initStickyShadow() {
    if (!header) {
      return;
    }

    function onScroll() {
      header.classList.toggle('is-scrolled', window.scrollY > 4);
    }

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function initDesktopSearch() {
    if (!desktopSearch) {
      return;
    }

    var input = desktopSearch.querySelector('.rpt-header-search__input');

    if (!input) {
      return;
    }

    desktopSearch.addEventListener('mouseenter', function () {
      desktopSearch.classList.add('is-expanded');
    });

    desktopSearch.addEventListener('mouseleave', function () {
      desktopSearch.classList.remove('is-expanded');
      input.blur();
    });
  }

  function openSearchOverlay() {
    if (!searchOverlay) {
      return;
    }

    searchOverlay.hidden = false;
    searchOverlay.setAttribute('aria-hidden', 'false');
    lockScroll();
    setExpanded(searchOpenBtn, true);

    var input = searchOverlay.querySelector('.rpt-header-search__input');
    if (input) {
      window.setTimeout(function () {
        input.focus();
      }, 50);
    }
  }

  function closeSearchOverlay() {
    if (!searchOverlay) {
      return;
    }

    searchOverlay.hidden = true;
    searchOverlay.setAttribute('aria-hidden', 'true');
    setExpanded(searchOpenBtn, false);
    unlockScroll();
  }

  function openMobileMenu() {
    if (!mobileMenu) {
      return;
    }

    mobileMenu.hidden = false;
    mobileMenu.setAttribute('aria-hidden', 'false');
    lockScroll();
    setExpanded(menuOpenBtn, true);
  }

  function closeMobileMenu() {
    if (!mobileMenu) {
      return;
    }

    mobileMenu.hidden = true;
    mobileMenu.setAttribute('aria-hidden', 'true');
    setExpanded(menuOpenBtn, false);
    unlockScroll();
  }

  function initSearchOverlay() {
    if (!searchOverlay) {
      return;
    }

    if (searchOpenBtn) {
      searchOpenBtn.addEventListener('click', openSearchOverlay);
    }

    searchOverlay.querySelectorAll('[data-rpt-search-close]').forEach(function (el) {
      el.addEventListener('click', closeSearchOverlay);
    });
  }

  function initMobileMenu() {
    if (!mobileMenu) {
      return;
    }

    if (menuOpenBtn) {
      menuOpenBtn.addEventListener('click', openMobileMenu);
    }

    mobileMenu.querySelectorAll('[data-rpt-menu-close]').forEach(function (el) {
      el.addEventListener('click', closeMobileMenu);
    });

    mobileMenu.querySelectorAll('.rpt-mobile-nav__toggle').forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        var sub = toggle.nextElementSibling;

        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');

        if (sub) {
          sub.hidden = expanded;
        }
      });
    });
  }

  function initEscapeKey() {
    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') {
        return;
      }

      if (searchOverlay && !searchOverlay.hidden) {
        closeSearchOverlay();
      }

      if (mobileMenu && !mobileMenu.hidden) {
        closeMobileMenu();
      }
    });
  }

  initStickyShadow();
  initDesktopSearch();
  initSearchOverlay();
  initMobileMenu();
  initEscapeKey();
})();
