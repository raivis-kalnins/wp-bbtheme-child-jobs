(function () {
  'use strict';

  /*
   * WP BBuilder normally renders its dynamic-form hCaptcha itself. The Jobs
   * footer/newsletter can cause the shared hCaptcha API handle to be loaded in
   * explicit mode, so do a small defensive render pass for contact/dynamic
   * forms that still have no iframe. Site keys continue to come only from
   * BBuilder's rendered data-sitekey attribute/settings.
   */
  function renderBbuilderHcaptchas() {
    if (!window.hcaptcha || typeof window.hcaptcha.render !== 'function') return;
    document.querySelectorAll('.wpbb-dynamic-form[data-captcha-provider="hcaptcha"] .h-captcha[data-sitekey]').forEach(function (widget) {
      if (!widget || widget.querySelector('iframe') || widget.dataset.wpbbJobsV95Rendered === '1') return;
      try {
        window.hcaptcha.render(widget, { sitekey: widget.getAttribute('data-sitekey') || '' });
        widget.dataset.wpbbJobsV95Rendered = '1';
      } catch (e) {
        if (widget.querySelector('iframe')) widget.dataset.wpbbJobsV95Rendered = '1';
      }
    });
  }

  function boot() {
    renderBbuilderHcaptchas();
    [300, 900, 1800, 3200].forEach(function (delay) {
      window.setTimeout(renderBbuilderHcaptchas, delay);
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once: true });
  else boot();
  window.addEventListener('load', renderBbuilderHcaptchas, { once: true });
}());
