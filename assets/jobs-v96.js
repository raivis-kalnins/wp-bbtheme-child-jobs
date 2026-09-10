(function () {
  'use strict';

  function q(sel, root) { return (root || document).querySelector(sel); }
  function qa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

  /* Repair old saved contact select markup/content without changing the page/map. */
  function normaliseContactSelect() {
    qa('.wpbb-jobs-contact-form select[name="topic"], .wp-theme-contact-form select[name="topic"]').forEach(function (select) {
      select.classList.add('wpbb-jobs-topic-select');
      if (select.options && select.options.length > 1) return;
      var raw = (select.textContent || '').replace(/\s+/g, ' ').trim();
      if (!raw || raw.length < 20) return;
      var labels = ['Choose a topic', 'Candidate support', 'Employer support', 'Account support', 'General enquiry'];
      select.innerHTML = '';
      labels.forEach(function (label, index) {
        var option = document.createElement('option');
        option.value = index === 0 ? '' : label;
        option.textContent = label;
        select.appendChild(option);
      });
    });
  }

  /* Parent-shell fallback: only intervenes when a menu click did not change state. */
  function mobileMenuFallback() {
    var button = q('.wp-theme-menu-toggle');
    var nav = q('[data-primary-navigation], #wp-theme-mobile-navigation');
    if (!button || !nav || button.dataset.wpbbJobsV96Bound === '1') return;
    button.dataset.wpbbJobsV96Bound = '1';

    button.addEventListener('click', function () {
      var before = document.body.classList.contains('wp-theme-menu-open');
      window.setTimeout(function () {
        var after = document.body.classList.contains('wp-theme-menu-open');
        if (after === before) {
          after = !before;
          document.body.classList.toggle('wp-theme-menu-open', after);
          button.setAttribute('aria-expanded', after ? 'true' : 'false');
          nav.setAttribute('aria-hidden', after ? 'false' : 'true');
        }
      }, 30);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape' || !document.body.classList.contains('wp-theme-menu-open')) return;
      document.body.classList.remove('wp-theme-menu-open');
      button.setAttribute('aria-expanded', 'false');
      nav.setAttribute('aria-hidden', 'true');
    });
  }

  /* Improve resilience of the v86 modal if hCaptcha loads late via another plugin. */
  function hcaptchaModalRecovery() {
    qa('.wpbb-jobs-captcha-modal').forEach(function (modal) {
      var dialog = q('.wpbb-jobs-captcha-modal__dialog', modal);
      var host = q('[data-wpbb-jobs-hcaptcha]', modal);
      var status = q('.wpbb-jobs-captcha-modal__status', modal);
      if (!dialog || !host || dialog.dataset.wpbbJobsV96Recovery === '1') return;
      dialog.dataset.wpbbJobsV96Recovery = '1';

      var retry = document.createElement('button');
      retry.type = 'button';
      retry.className = 'wpbb-jobs-captcha-modal__retry';
      retry.textContent = 'Retry verification';
      retry.hidden = true;
      if (status) status.parentNode.insertBefore(retry, status.nextSibling);

      function hasWidget() { return !!q('iframe', host); }
      function render() {
        if (hasWidget()) { retry.hidden = true; if (status) status.textContent = ''; return true; }
        if (!window.hcaptcha || typeof window.hcaptcha.render !== 'function') return false;
        try {
          host.innerHTML = '';
          var form = modal.wpbbJobsForm || host.closest('form');
          var sitekey = host.getAttribute('data-sitekey') || '';
          var id = window.hcaptcha.render(host, {
            sitekey: sitekey,
            size: window.innerWidth < 360 ? 'compact' : 'normal',
            theme: document.documentElement.classList.contains('is-dark-theme') ? 'dark' : 'light',
            callback: function (token) {
              var owner = form || document.querySelector('form[data-wpbb-jobs-public-form="1"] [data-wpbb-jobs-hcaptcha="' + sitekey + '"]');
              if (owner && owner.tagName !== 'FORM') owner = owner.closest('form');
              if (owner) {
                var input = q('[name="wpbb_jobs_hcaptcha_response"]', owner);
                if (input) input.value = token || '';
                owner.dataset.wpbbJobsCaptchaVerified = '1';
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
                document.documentElement.classList.remove('wpbb-jobs-captcha-open');
                document.body.classList.remove('wpbb-jobs-captcha-open');
                if (typeof owner.requestSubmit === 'function') {
                  owner.dataset.wpbbJobsCaptchaBypass = '1';
                  owner.requestSubmit();
                } else {
                  window.HTMLFormElement.prototype.submit.call(owner);
                }
              }
            }
          });
          var ownerForm = host.closest('form');
          if (ownerForm) ownerForm.dataset.wpbbJobsCaptchaWidget = String(id);
          window.setTimeout(function () {
            retry.hidden = hasWidget();
            if (!hasWidget() && status) status.textContent = 'Verification did not load. Please retry.';
          }, 1400);
          return true;
        } catch (e) {
          retry.hidden = false;
          if (status) status.textContent = 'Verification did not load. Please retry.';
          return false;
        }
      }

      retry.addEventListener('click', function () {
        retry.hidden = true;
        if (status) status.textContent = 'Loading verification…';
        render();
      });

      var observer = new MutationObserver(function () {
        if (!modal.hidden && !hasWidget()) {
          var tries = 0;
          (function wait() {
            if (render() || tries++ > 30) {
              if (!hasWidget() && tries > 30) retry.hidden = false;
              return;
            }
            window.setTimeout(wait, 200);
          }());
        }
      });
      observer.observe(modal, { attributes: true, attributeFilter: ['hidden', 'aria-hidden'] });
    });
  }

  function boot() {
    normaliseContactSelect();
    mobileMenuFallback();
    hcaptchaModalRecovery();
    window.setTimeout(hcaptchaModalRecovery, 700);
    window.setTimeout(hcaptchaModalRecovery, 1800);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once: true });
  else boot();
}());
