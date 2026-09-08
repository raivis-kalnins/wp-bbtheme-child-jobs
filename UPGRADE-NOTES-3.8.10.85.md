# Jobs Theme 3.8.10.85 — upgrade notes

## What this build adds

- Green marketplace visual refresh based on the supplied Jobs preview.
- Existing homepage “One marketplace, two clear journeys” section retained.
- Optional LinkedIn OpenID Connect login for candidate identity/email/profile-image prefill.
- Rich candidate/CV builder with profile image, structured experience/education/languages fields and CV upload.
- New managed `/home-2/` high-volume hiring demo with qualifying filters and two deterministic landing-page variants.
- Employer application pipeline with inline status changes.
- Optional CRM/ATS JSON webhook for application creation and status changes; optional HMAC SHA-256 signing.
- Managed language parity for EN, DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS.

## Staging setup

1. Install/replace the child theme and confirm WP BBTheme + WP BBuilder dependencies are active.
2. In **HR Jobs → Setup & Demo**, run **Create / refresh Jobs pages**. This creates/refreshes managed portal pages including `/home-2/` while leaving unmanaged same-slug pages untouched.
3. If Polylang is active, let the managed translation sync complete in wp-admin and verify Home, Jobs, Salary Guide, Home 2 and dashboards in each language.
4. In **HR Jobs → Settings**, optionally enable LinkedIn and add the LinkedIn Client ID/Secret. Copy the displayed Authorized redirect URL into the LinkedIn app exactly.
5. In **HR Jobs → Settings**, optionally add a CRM/ATS webhook URL and signing secret.
6. Test candidate registration, LinkedIn sign-in, CV/profile image upload, job filters, apply flow, employer pipeline status changes and CRM delivery on staging.

## LinkedIn scope note

The implementation uses the normal LinkedIn OpenID Connect scopes `openid profile email`. This is appropriate for sign-in plus basic identity/profile-image data. It does not assume access to a complete LinkedIn employment-history/CV export. The native CV builder and upload flow cover the richer CV data unless separate LinkedIn API product permissions are available.

## A/B QA

- `/home-2/?jobs_variant=a` forces variant A.
- `/home-2/?jobs_variant=b` forces variant B.
- Without an override, the theme assigns a stable A/B variant from the logged-in user ID or visitor request fingerprint.

## CRM webhook payload

Events are sent for `application.created` and `application.status_changed`. The payload contains site, application, job and company data. When a signing secret is configured, the request includes `X-WPBB-Jobs-Signature: sha256=<hmac>`.

## Verification completed before packaging

- PHP syntax lint passed for all PHP files in the child theme.
- Theme version markers updated to 3.8.10.85.
- Active runtime/theme version markers are updated to 3.8.10.85; historical changelog references are retained.

A full visual/browser regression still needs to be run after installing the package on the actual WordPress staging instance because this build environment does not include that site's WordPress database, parent theme runtime or deployment credentials.
