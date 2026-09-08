# WP BBTheme Child Jobs 3.8.10.86 — Upgrade Notes

## What changed

3.8.10.86 is the finishing release for the supplied Jobs marketplace direction. It keeps the integrated recruitment engine and the 3.8.10.85 LinkedIn/CV/high-volume work, while finishing the public brand, homepage, footer newsletter and anonymous-form captcha flow.

### Design

- New `assets/brand/jobs-logo.svg` plus 32/192/512 favicon PNGs and Apple touch icon.
- Primary UI now uses green `#078f6a` / mint `#0caf80`; dark navy is retained as heading/ink rather than a button/link colour.
- Homepage hero copy/search/image treatment now follows the supplied preview more closely.
- Category discovery is six cards, salary is a focused calculator section and Featured jobs is a four-card desktop row.
- `One marketplace, two clear journeys` is retained.
- Inner page heroes, dashboards and the high-volume demo share the same softer green/mint system.
- Newsletter remains a green gradient band; the main footer is now white to match the preview.

### Newsletter + consent

The child theme does not replace WP Newsletter Campaigns. It styles the plugin's own frontend form and modal. The plugin's `privacy_checkbox` defaults to enabled; 3.8.10.86 ensures the rendered consent row remains visible and correctly aligned in the footer.

### hCaptcha source and the “testing only” warning

The supplied database contains an enabled WP BBuilder hCaptcha configuration. WP Newsletter Campaigns 2.1.3 normally detects localhost URLs and swaps that configuration for hCaptcha's official test pair. That is why the screenshots show the red test-only warning.

3.8.10.86 adds the plugin's documented local-test-mode filter. When the incoming browser request is on a real host but a cloned WordPress `home`/`siteurl` still says localhost, the theme disables the newsletter test-key substitution so the saved WP BBuilder credentials are used. Genuine localhost requests keep the plugin's safe test mode. The newsletter then uses the same BBuilder keys instead of the test pair. The secret key remains server-side and is never written into this theme package.

**Deployment check:** the hCaptcha site key must allow the actual hostname used by the site (for example the staging/demo hostname). If a cloned database still reports a localhost WordPress URL, correct `home` / `siteurl` as part of deployment as well.

### Anonymous forms protected

When WP BBuilder hCaptcha is enabled, the following Jobs forms now require a modal challenge and server-side verification:

- Candidate/employer account registration.
- Public Jobs sign-in form.
- Guest job applications.
- Homepage 2 high-volume qualifying form for anonymous visitors.
- Newsletter subscription continues to use WP Newsletter Campaigns' own hCaptcha integration, now sourced from BBuilder settings.

Forms already owned by WP BBuilder keep using WP BBuilder's native hCaptcha handling. Logged-in employer/candidate management forms are not given an extra captcha.

## Upgrade

1. Back up the current child theme.
2. Upload/replace with `wp-bbtheme-child-jobs-3.8.10.86.zip` and activate it.
3. Clear page/cache/CDN caches.
4. Load wp-admin once so the 3.8.10.86 managed Jobs page/translation repair marker can refresh theme-managed pages.
5. Verify WP BBuilder → hCaptcha is enabled and has a valid site/secret pair.
6. Confirm the hCaptcha site key allows the current frontend hostname.
7. Test newsletter subscription, anonymous registration, a guest application and Homepage 2 qualification in a private/incognito browser.
8. Check all language switcher variants after the one-time managed translation refresh.

No parent-theme or plugin files need to be modified for this release.
