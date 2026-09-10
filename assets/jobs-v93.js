(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn, { once: true });
  }

  ready(function () {
    if (!window.wpbbJobsV93 || !window.fetch) return;

    var form = document.querySelector('[data-wpbb-jobs-live-search="1"]');
    var panel = document.querySelector('[data-wpbb-jobs-live-results]');
    if (!form || !panel) return;

    var activeFilter = null;
    var lastParams = '';

    function esc(value) {
      var div = document.createElement('div');
      div.textContent = value == null ? '' : String(value);
      return div.innerHTML;
    }

    function getBaseParams() {
      var params = new URLSearchParams();
      var keyword = form.querySelector('[name="job_keyword"]');
      var location = form.querySelector('[name="job_location"]');
      if (keyword && keyword.value.trim()) params.set('job_keyword', keyword.value.trim());
      if (location && location.value) params.set('job_location', location.value);
      return params;
    }

    function renderLoading() {
      panel.hidden = false;
      panel.classList.add('is-loading');
      panel.innerHTML = '<div class="wpbb-jobs-live-results__status">' + esc(wpbbJobsV93.labels.loading) + '</div>';
    }

    function renderResults(data, params) {
      panel.classList.remove('is-loading');
      panel.hidden = false;
      var items = data && Array.isArray(data.items) ? data.items : [];
      if (!items.length) {
        panel.innerHTML = '<div class="wpbb-jobs-live-results__empty">' + esc(wpbbJobsV93.labels.empty) + '</div>' +
          '<a class="wpbb-jobs-live-results__all" href="' + esc(data && data.viewAll ? data.viewAll : wpbbJobsV93.jobsUrl) + '">' + esc(wpbbJobsV93.labels.viewAll) + ' →</a>';
        return;
      }

      var html = '<div class="wpbb-jobs-live-results__head"><strong>' + esc(wpbbJobsV93.labels.results) + '</strong><span>' + esc(data.total || items.length) + '</span></div>';
      html += '<div class="wpbb-jobs-live-results__grid">';
      items.forEach(function (item) {
        var meta = [];
        if (item.location) meta.push(item.location);
        if (item.type) meta.push(item.type);
        if (item.remote) meta.push('Remote friendly');
        html += '<a class="wpbb-jobs-live-result" href="' + esc(item.url) + '">' +
          '<span class="wpbb-jobs-live-result__title">' + esc(item.title) + '</span>' +
          '<span class="wpbb-jobs-live-result__company">' + esc(item.company) + '</span>' +
          '<span class="wpbb-jobs-live-result__meta">' + esc(meta.join(' · ')) + '</span>' +
          (item.salary ? '<strong class="wpbb-jobs-live-result__salary">' + esc(item.salary) + '</strong>' : '') +
          '</a>';
      });
      html += '</div><a class="wpbb-jobs-live-results__all" href="' + esc(data.viewAll || wpbbJobsV93.jobsUrl) + '">' + esc(wpbbJobsV93.labels.viewAll) + ' →</a>';
      panel.innerHTML = html;
      lastParams = params.toString();
    }

    function runSearch(params) {
      params = params || getBaseParams();
      var body = new URLSearchParams(params.toString());
      body.set('action', 'wpbb_jobs_live_search');
      body.set('nonce', wpbbJobsV93.nonce);
      renderLoading();

      fetch(wpbbJobsV93.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString()
      }).then(function (response) {
        if (!response.ok) throw new Error('Request failed');
        return response.json();
      }).then(function (payload) {
        if (!payload || !payload.success) throw new Error('Invalid response');
        renderResults(payload.data || {}, params);
      }).catch(function () {
        panel.classList.remove('is-loading');
        panel.hidden = false;
        var fallback = new URL(wpbbJobsV93.jobsUrl, window.location.origin);
        params.forEach(function (value, key) { fallback.searchParams.set(key, value); });
        panel.innerHTML = '<div class="wpbb-jobs-live-results__empty">' + esc(wpbbJobsV93.labels.error) + '</div>' +
          '<a class="wpbb-jobs-live-results__all" href="' + esc(fallback.toString()) + '">' + esc(wpbbJobsV93.labels.viewAll) + ' →</a>';
      });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (activeFilter) {
        activeFilter.classList.remove('is-active');
        activeFilter = null;
      }
      runSearch(getBaseParams());
    });

    form.addEventListener('input', function () {
      window.clearTimeout(form._wpbbJobsLiveTimer);
      form._wpbbJobsLiveTimer = window.setTimeout(function () {
        var params = getBaseParams();
        var keyword = params.get('job_keyword') || '';
        var location = params.get('job_location') || '';
        if (keyword.length < 2 && !location) { panel.hidden = true; return; }
        if (params.toString() !== lastParams) runSearch(params);
      }, 350);
    });

    form.addEventListener('change', function () {
      if (!panel.hidden) runSearch(getBaseParams());
    });

    document.querySelectorAll('[data-wpbb-jobs-live-filter="1"]').forEach(function (chip) {
      chip.addEventListener('click', function (event) {
        event.preventDefault();
        var href = new URL(chip.href, window.location.origin);
        var params = getBaseParams();
        ['job_keyword', 'job_type', 'job_category', 'job_remote'].forEach(function (key) { params.delete(key); });
        href.searchParams.forEach(function (value, key) {
          if (['job_keyword', 'job_type', 'job_category', 'job_remote', 'job_location'].indexOf(key) !== -1) params.set(key, value);
        });
        if (activeFilter && activeFilter !== chip) activeFilter.classList.remove('is-active');
        chip.classList.toggle('is-active', activeFilter !== chip);
        activeFilter = chip.classList.contains('is-active') ? chip : null;
        if (!activeFilter) params = getBaseParams();
        runSearch(params);
      });
    });

    document.addEventListener('click', function (event) {
      if (panel.hidden) return;
      if (form.contains(event.target) || panel.contains(event.target) || event.target.closest('[data-wpbb-jobs-live-filter="1"]')) return;
      panel.hidden = true;
    });
  });
})();
