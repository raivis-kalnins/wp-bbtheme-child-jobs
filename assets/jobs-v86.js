(function () {
  'use strict';

  var cfg = window.wpbbJobsV86 || {};
  var openForm = null;

  function replaceBranding() {
    if (!cfg.brandLogo) return;
    document.querySelectorAll('.wp-theme-site-logo-link, .wp-theme-footer-brand').forEach(function (link) {
      if (!link || link.dataset.wpbbJobsBrandReady === '1') return;
      var img = document.createElement('img');
      img.className = 'wpbb-jobs-brand-logo';
      img.src = cfg.brandLogo;
      img.alt = 'Jobs';
      img.decoding = 'async';
      link.innerHTML = '';
      link.appendChild(img);
      link.setAttribute('aria-label', 'Jobs');
      link.dataset.wpbbJobsBrandReady = '1';
    });
  }

  function formHost(form) {
    return form ? form.querySelector('[data-wpbb-jobs-hcaptcha][data-sitekey]') : null;
  }

  function tokenInput(form) {
    return form ? form.querySelector('[name="wpbb_jobs_hcaptcha_response"]') : null;
  }

  function widgetId(form) {
    var value = form && form.dataset.wpbbJobsCaptchaWidget;
    return value === undefined || value === '' ? null : value;
  }

  function setToken(form, token) {
    var input = tokenInput(form);
    if (input) input.value = token || '';
  }

  function captchaReady() {
    return !!(window.hcaptcha && typeof window.hcaptcha.render === 'function');
  }

  function modalFor(form) {
    if (form.wpbbJobsCaptchaModal && document.body.contains(form.wpbbJobsCaptchaModal)) {
      return form.wpbbJobsCaptchaModal;
    }

    var host = formHost(form);
    if (!host) return null;

    var modal = document.createElement('div');
    modal.className = 'wpbb-jobs-captcha-modal';
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML =
      '<div class="wpbb-jobs-captcha-modal__dialog" role="dialog" aria-modal="true">' +
        '<button type="button" class="wpbb-jobs-captcha-modal__close" aria-label="' + escapeHtml(cfg.captchaClose || 'Close verification') + '">×</button>' +
        '<span class="wp-theme-sector-eyebrow">' + escapeHtml(cfg.captchaEyebrow || 'Security check') + '</span>' +
        '<h2>' + escapeHtml(cfg.captchaTitle || 'Verify you are human') + '</h2>' +
        '<p>' + escapeHtml(cfg.captchaText || 'Complete the hCaptcha check to continue.') + '</p>' +
        '<div class="wpbb-jobs-captcha-modal__widget"></div>' +
        '<div class="wpbb-jobs-captcha-modal__status" role="status" aria-live="polite"></div>' +
      '</div>';
    modal.querySelector('.wpbb-jobs-captcha-modal__widget').appendChild(host);
    document.body.appendChild(modal);
    form.wpbbJobsCaptchaModal = modal;

    modal.querySelector('.wpbb-jobs-captcha-modal__close').addEventListener('click', function () {
      closeModal(form, true);
    });
    modal.addEventListener('click', function (event) {
      if (event.target === modal) closeModal(form, true);
    });
    modal.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        event.preventDefault();
        closeModal(form, true);
      }
    });
    return modal;
  }

  function escapeHtml(value) {
    var div = document.createElement('div');
    div.textContent = value || '';
    return div.innerHTML;
  }

  function status(form, text) {
    var modal = form && form.wpbbJobsCaptchaModal;
    var node = modal && modal.querySelector('.wpbb-jobs-captcha-modal__status');
    if (node) node.textContent = text || '';
  }

  function openModal(form) {
    var modal = modalFor(form);
    if (!modal) return false;
    if (openForm && openForm !== form) closeModal(openForm, false);
    openForm = form;
    form.wpbbJobsPreviousFocus = document.activeElement;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('wpbb-jobs-captcha-open');
    document.body.classList.add('wpbb-jobs-captcha-open');
    window.requestAnimationFrame(function () {
      var close = modal.querySelector('.wpbb-jobs-captcha-modal__close');
      if (close) close.focus();
    });
    return true;
  }

  function reset(form) {
    var id = widgetId(form);
    if (window.hcaptcha && id !== null && typeof window.hcaptcha.reset === 'function') {
      try { window.hcaptcha.reset(id); } catch (e) {}
    }
    setToken(form, '');
  }

  function closeModal(form, cancelled) {
    if (!form || !form.wpbbJobsCaptchaModal) return;
    var modal = form.wpbbJobsCaptchaModal;
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('wpbb-jobs-captcha-open');
    if (document.body) document.body.classList.remove('wpbb-jobs-captcha-open');
    if (openForm === form) openForm = null;
    if (cancelled) reset(form);
    var previous = form.wpbbJobsPreviousFocus;
    if (previous && typeof previous.focus === 'function') {
      try { previous.focus({ preventScroll: true }); } catch (e) { try { previous.focus(); } catch (ignore) {} }
    }
    form.wpbbJobsPreviousFocus = null;
  }

  function render(form) {
    if (!captchaReady()) return false;
    if (form.dataset.wpbbJobsCaptchaRendered === '1' && widgetId(form) !== null) return true;
    var host = formHost(form);
    if (!host) return false;

    try {
      var id = window.hcaptcha.render(host, {
        sitekey: host.getAttribute('data-sitekey') || '',
        size: window.innerWidth < 360 ? 'compact' : 'normal',
        theme: document.documentElement.classList.contains('is-dark-theme') ? 'dark' : 'light',
        callback: function (token) {
          setToken(form, token || '');
          status(form, '');
          closeModal(form, false);
          form.dataset.wpbbJobsCaptchaVerified = '1';
          window.HTMLFormElement.prototype.submit.call(form);
        },
        'error-callback': function () {
          setToken(form, '');
          status(form, cfg.captchaError || 'Please complete the hCaptcha verification.');
        },
        'expired-callback': function () {
          reset(form);
          status(form, cfg.captchaError || 'Please complete the hCaptcha verification.');
        }
      });
      form.dataset.wpbbJobsCaptchaWidget = String(id);
      form.dataset.wpbbJobsCaptchaRendered = '1';
      return true;
    } catch (e) {
      return false;
    }
  }

  function waitForCaptcha(form, attempt) {
    attempt = attempt || 0;
    if (!openModal(form)) return;
    if (render(form)) {
      status(form, '');
      return;
    }
    if (attempt >= 40) {
      status(form, cfg.captchaError || 'Please complete the hCaptcha verification.');
      return;
    }
    status(form, cfg.captchaWait || 'Loading verification…');
    window.setTimeout(function () { waitForCaptcha(form, attempt + 1); }, 250);
  }

  function onSubmit(event) {
    var form = event.target;
    if (!cfg.captcha || !form || !form.matches('form[data-wpbb-jobs-public-form="1"]')) return;
    if (form.dataset.wpbbJobsCaptchaVerified === '1' || (tokenInput(form) && tokenInput(form).value)) return;
    event.preventDefault();
    event.stopPropagation();
    waitForCaptcha(form, 0);
  }

  function init() {
    replaceBranding();
    document.querySelectorAll('form[data-wpbb-jobs-public-form="1"]').forEach(function (form) {
      modalFor(form);
    });
  }

  document.addEventListener('submit', onSubmit, true);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
}());
