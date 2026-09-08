(function () {
  'use strict';

  var cfg = window.wpbbJobsV88 || {};

  function isLocalHost(host) {
    host = String(host || '').toLowerCase().replace(/:\d+$/, '');
    return host === 'localhost' || /\.localhost$/.test(host) || host === '::1' || /^127\./.test(host);
  }

  function addHeaderActions() {
    var actions = document.querySelector('.wp-theme-header-actions');
    if (!actions) return;
    var search = actions.querySelector('.wp-theme-search-toggle');

    if (cfg.postUrl && !actions.querySelector('.wpbb-jobs-header-post')) {
      var post = document.createElement('a');
      post.className = 'wpbb-jobs-header-post';
      post.href = cfg.postUrl;
      post.textContent = cfg.postLabel || 'Post a job';
      if (search) actions.insertBefore(post, search); else actions.insertBefore(post, actions.firstChild);
    }

    if (cfg.accountUrl && !actions.querySelector('.wpbb-jobs-header-account')) {
      var account = document.createElement('a');
      account.className = 'wpbb-jobs-header-account';
      account.href = cfg.accountUrl;
      account.setAttribute('aria-label', cfg.accountLabel || 'Account');
      account.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M5.5 20c.6-4 3-6 6.5-6s5.9 2 6.5 6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>';
      var themeToggle = actions.querySelector('.wp-theme-theme-toggle');
      if (themeToggle) actions.insertBefore(account, themeToggle); else actions.appendChild(account);
    }
  }

  function childTextMatchesNotice(node) {
    if (!node || node.nodeType !== 1) return false;
    var text = (node.textContent || '').replace(/\s+/g, ' ').trim();
    if (!text) return false;
    var configured = window.WPThemeEssentials && window.WPThemeEssentials.labels && window.WPThemeEssentials.labels.formNotice;
    if (configured && text.indexOf(String(configured).replace(/\s+/g, ' ').trim()) === 0) return true;
    return /^By submitting this form,/i.test(text);
  }

  function normaliseForms() {
    document.querySelectorAll('form.wp-newslatter-campaigns-form').forEach(function (form) {
      Array.prototype.slice.call(form.children).forEach(function (child) {
        if (childTextMatchesNotice(child)) child.classList.add('wpbb-jobs-newsletter-redundant-notice');
      });
    });

    document.querySelectorAll('form.wpbb-jobs-form').forEach(function (form) {
      var button = form.querySelector('button[type="submit"],input[type="submit"]');
      Array.prototype.slice.call(form.children).forEach(function (child) {
        if (!childTextMatchesNotice(child)) return;
        child.classList.add('wpbb-jobs-legal-notice');
        if (button && child.nextElementSibling !== button) form.insertBefore(child, button);
      });
    });
  }

  function repairNewsletterCaptchaSiteKey() {
    if (!cfg.hcaptchaSiteKey || isLocalHost(window.location.hostname)) return;
    document.querySelectorAll('form.wp-newslatter-campaigns-form [data-wpnc-hcaptcha][data-sitekey]').forEach(function (host) {
      var current = host.getAttribute('data-sitekey') || '';
      if (/^10000000-ffff-ffff-ffff-000000000001$/i.test(current)) {
        host.setAttribute('data-sitekey', cfg.hcaptchaSiteKey);
        var form = host.closest('form');
        if (form) {
          delete form.dataset.wpncHcaptchaRendered;
          delete form.dataset.wpncHcaptchaWidget;
        }
      }
    });
  }

  function removeDuplicateCookieBanner() {
    if (!document.querySelector('.wp-theme-cookie-banner')) return;
    document.querySelectorAll('.wpbb-cookie-consent').forEach(function (banner) {
      banner.hidden = true;
      banner.setAttribute('aria-hidden', 'true');
    });
  }

  function runRepairs() {
    addHeaderActions();
    normaliseForms();
    repairNewsletterCaptchaSiteKey();
    removeDuplicateCookieBanner();
  }

  function init() {
    runRepairs();
    [40, 160, 600, 1400].forEach(function (delay) { window.setTimeout(runRepairs, delay); });
    var observer = new MutationObserver(function () { window.setTimeout(runRepairs, 0); });
    observer.observe(document.body, { childList: true, subtree: true });
    window.setTimeout(function () { observer.disconnect(); }, 5000);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
}());
