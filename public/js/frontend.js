/**
 * Design Cart Woo Timelines
 * Author: Paweł Nosko — https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart — https://www.designcart.pl/
 */
(function () {
  'use strict';

  function reveal(root) {
    var branches = root.querySelectorAll('.dcwt__branch');
    if (!branches.length) {
      return;
    }
    if (!('IntersectionObserver' in window)) {
      branches.forEach(function (el) {
        el.classList.add('is-in');
      });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    branches.forEach(function (el) {
      io.observe(el);
    });
  }

  function init() {
    document.querySelectorAll('[data-dcwt]').forEach(reveal);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
