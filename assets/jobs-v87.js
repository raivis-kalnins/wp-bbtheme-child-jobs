(function () {
  'use strict';

  function isDark() {
    var root = document.documentElement;
    return root.classList.contains('is-dark-theme') || root.getAttribute('data-theme') === 'dark';
  }

  function syncBrandLogo() {
    document.querySelectorAll('img.wpbb-jobs-brand-logo').forEach(function (img) {
      var src = img.getAttribute('src') || '';
      if (!src) return;
      if (!img.dataset.jobsLightLogo) {
        img.dataset.jobsLightLogo = src.replace(/jobs-logo-dark\.svg(?:\?.*)?$/, 'jobs-logo.svg');
        img.dataset.jobsDarkLogo = img.dataset.jobsLightLogo.replace(/jobs-logo\.svg(?:\?.*)?$/, 'jobs-logo-dark.svg');
      }
      var next = isDark() ? img.dataset.jobsDarkLogo : img.dataset.jobsLightLogo;
      if (next && img.getAttribute('src') !== next) img.setAttribute('src', next);
    });
  }

  function init() {
    syncBrandLogo();
    var observer = new MutationObserver(syncBrandLogo);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
    window.setTimeout(syncBrandLogo, 0);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
}());
