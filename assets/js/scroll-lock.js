(function (window) {
  'use strict';

  var lockCount = 0;
  var scrollY = 0;

  function applyLock() {
    scrollY = window.scrollY || window.pageYOffset || 0;
    document.documentElement.classList.add('rpt-scroll-lock');
    document.body.classList.add('rpt-scroll-lock');
    document.body.style.top = '-' + scrollY + 'px';
  }

  function removeLock() {
    document.documentElement.classList.remove('rpt-scroll-lock');
    document.body.classList.remove('rpt-scroll-lock');
    document.body.style.top = '';
    window.scrollTo(0, scrollY);
  }

  window.rptScrollLock = {
    lock: function () {
      if (lockCount === 0) {
        applyLock();
      }

      lockCount += 1;
    },
    unlock: function () {
      if (lockCount <= 0) {
        return;
      }

      lockCount -= 1;

      if (lockCount === 0) {
        removeLock();
      }
    },
  };
})(window);
