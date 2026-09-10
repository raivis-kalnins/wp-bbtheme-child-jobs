(function () {
  'use strict';
  var cfg = window.wpbbJobsV89 || {};

  function replaceInstallIcons() {
    if (!cfg.icon32) return;
    document.querySelectorAll('.wp-theme-footer-install img').forEach(function (img) {
      if (img.dataset.wpbbJobsIcon === '1') return;
      img.src = cfg.icon32;
      img.dataset.wpbbJobsIcon = '1';
    });
    var dialogIcon = document.querySelector('.wp-theme-install-dialog__icon');
    if (dialogIcon && cfg.icon192 && dialogIcon.dataset.wpbbJobsIcon !== '1') {
      dialogIcon.src = cfg.icon192;
      dialogIcon.dataset.wpbbJobsIcon = '1';
    }
  }

  function makeSearchIconsReadable() {
    document.querySelectorAll('#wp-theme-main button svg').forEach(function (svg) {
      var button = svg.closest('button');
      if (!button) return;
      var bg = window.getComputedStyle(button).backgroundColor;
      if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') svg.style.color = 'currentColor';
    });
  }

  function init() {
    replaceInstallIcons();
    makeSearchIconsReadable();
    [100, 500, 1200].forEach(function (delay) { window.setTimeout(function () { replaceInstallIcons(); makeSearchIconsReadable(); }, delay); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, {once:true}); else init();
}());
