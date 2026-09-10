# WP BBTheme Child Jobs 3.8.10.96

Full-grid Jobs/search and form-security finish. The managed Jobs page now uses a wide four-column desktop vacancy grid and refreshes managed multilingual copies once after upgrade. Jobs submission forms use the hCaptcha configuration stored in WP BBuilder; the Contact page continues to use WP BBuilder's native Dynamic Form hCaptcha, with a small compatibility renderer for shared/explicit hCaptcha script loading. See `UPGRADE-NOTES-3.8.10.96.md`.

# WP BBTheme Child Jobs 3.8.10.94

Emergency boot hotfix for 3.8.10.93. This package corrects the mismatched v93 include filename and loads the release module defensively, restoring WordPress immediately after replacing the broken theme files. All 3.8.10.93 Jobs/search/Home 2 functionality is preserved. See `UPGRADE-NOTES-3.8.10.94.md`.

# WP BBTheme Child Jobs 3.8.10.93

Live homepage/search correction for the Jobs marketplace. 3.8.10.93 adds AJAX-first homepage vacancy search, AJAX popular-filter chips, a repaired full-background hero/metric strip, stronger category cards, a sharper candidate journey image, a narrower newsletter field and an expanded repeated-role Home 2 demo with additional sample vacancies. Existing unmanaged content is not overwritten. See `UPGRADE-NOTES-3.8.10.93.md`.

# WP BBTheme Child Jobs 3.8.10.89

Marketplace-style recruitment child theme for WP BBTheme. This release expects WP BBuilder 5.6.9+ for the fluid-container hero background image controls. See `UPGRADE-NOTES-3.8.10.89.md`.

# WP BBTheme Child Jobs 3.8.10.88

## 3.8.10.88 live-layout correction

This pass fixes the issues visible in the live-page screenshot rather than adding another cosmetic layer. The public header, homepage sections, newsletter and footer now use a wider 1400px desktop canvas instead of the older narrow demo width. The Homepage 1 hero uses the supplied woman/city artwork as an integrated right-hand visual with a soft blend into the copy area, while the hero search is simplified visually to keyword + location + green action like the reference.

The header now has a dedicated **Post a job** CTA and account icon, so **Career advice** is no longer incorrectly styled as the green last-menu button. The child theme also fixes the literal `\n` characters that were being printed before the header by the previous favicon hook.

Public Jobs forms get cleaner file inputs and a dedicated legal-notice row. The footer subscription form is normalised to one email row plus the required consent checkbox underneath, and the redundant parent-theme generic form notice is suppressed only inside the newsletter form. When both parent and BBuilder cookie banners are present, the simpler duplicate BBuilder banner is hidden.

For hCaptcha, production-host detection now also checks forwarded/original host headers, which prevents a reverse-proxied live domain from inheriting localhost test credentials. Genuine localhost still uses hCaptcha's official test mode.

Dark mode is rebalanced around deep teal surfaces, mint links and green actions, and inherited BBuilder/Bootstrap primary variables are overridden so royal-blue controls do not leak back into the Jobs theme.


## 3.8.10.86 final visual + public-form security pass

This release finishes the visual system around the supplied Jobs marketplace preview: a new Jobs logo/favicon set, green/teal primary UI instead of inherited blue controls, a stronger search-led homepage hero, six-card category row, simplified salary calculator section, four-card featured-jobs row, matching inner-page surfaces and a white footer under the green newsletter band. The homepage still includes **“One marketplace, two clear journeys”** and Homepage 2 remains the high-volume recruitment demo.

The footer subscription form is now explicitly styled for **WP Newsletter Campaigns**, including its required privacy-consent checkbox and hCaptcha modal. The theme uses the hCaptcha configuration already stored by **WP BBuilder** as the single captcha source. For cloned databases, it disables the newsletter plugin's automatic public test-key substitution when the incoming browser request is on a real hostname but WordPress still reports a localhost URL, avoiding the red “testing only” widget seen in the supplied screenshots. Genuine localhost requests retain the plugin's normal test mode. No hCaptcha secret is copied into theme files.

Anonymous Jobs account registration, the public Jobs sign-in form, guest job applications and the Homepage 2 qualifying form now open the same theme-styled hCaptcha modal and perform server-side verification before processing. Native WP BBuilder forms continue to use WP BBuilder's own captcha integration.

The new homepage/captcha/footer-facing strings are included in the existing 14-language demo system (EN plus DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS).

## 3.8.10.85 marketplace + high-volume hiring upgrade

This package continues the archived Jobs Theme upgrade as a self-contained child-theme release. It keeps the existing WordPress-native recruitment engine, rebuilds the public Jobs experience toward the supplied green marketplace preview and preserves the homepage **“One marketplace, two clear journeys”** candidate/employer section.

The standard homepage remains the broader jobs marketplace. A second managed page, **Homepage 2 / High-volume Hiring Demo**, is designed for repeat recruitment: a strong qualifying introduction and CTA, job filters, stable A/B landing-page variants, an employer pipeline and optional CRM webhook delivery. Both managed journeys participate in the theme's multilingual Polylang synchronisation.

Supported demo languages remain **EN, DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS**. The new high-volume page and core new UI labels include starter translations for the same language set. User-created content is not overwritten.

### LinkedIn and CV profile flow

Optional LinkedIn sign-in uses **OpenID Connect**. It can match/create a candidate account from the LinkedIn subject/email and prefill the display name and profile image returned by LinkedIn. The normal OpenID Connect profile does **not** provide a complete LinkedIn employment-history/CV export, so the theme combines LinkedIn identity/photo prefill with its own structured CV builder and uploaded CV document. Full LinkedIn work-history import would require separate LinkedIn API product access/permissions.

The CV builder now includes profile image, headline, phone, availability, current role/employer, category, location, skills, website, LinkedIn profile URL, languages, summary, work experience, education and a PDF/DOC/DOCX upload. DOCX files can best-effort seed the editable profile text when PHP `ZipArchive` is available.

### High-volume hiring and CRM

`[wpbb_jobs_high_volume_home]` powers Homepage 2. Visitors are deterministically assigned to variant A or B; QA can force a variant with `?jobs_variant=a` or `?jobs_variant=b`. The qualifying form routes into the existing Jobs results using the existing keyword, location and job-type filters.

The employer dashboard now includes a visual application pipeline. An optional CRM webhook can receive JSON when an application is created or its status changes. If a webhook signing secret is configured, the theme adds an `X-WPBB-Jobs-Signature` HMAC SHA-256 signature.

## Recommended installation order

1. Install and activate the **WP BBTheme** parent theme.
2. Install and activate **WP BBuilder 5.6.8 or newer**.
3. Install and activate **WP BBTheme Child Jobs 3.8.10.88**.
4. Open **HR Jobs → Setup & Demo** (or **Appearance → HR Jobs Setup**).
5. Run the parent **Starter Setup / Import active child-theme demo**. The Jobs theme now automatically seeds the recruitment marketplace as part of the same import.
6. Use **HR Jobs → Setup & Demo → Add / repair HR demo data** only when you want to repair/reseed the integrated recruitment records without rebuilding the whole site demo.
7. If WooCommerce or WP Theme Woo Support is active, confirm the activation notice to disable that store stack for this non-commerce HR sector; no store data is deleted.
8. Review **HR Jobs → Settings** and test the candidate and employer journeys on staging before launch.

## Integrated recruitment functionality

The theme registers and manages its own WordPress-native recruitment content:

- **Jobs** with salary range, currency, company, location, category, job type, remote/hybrid flag, application deadline, featured state and open/closed state.
- **Companies / employers** with profile content, hiring contact details, website and location.
- **Candidate profiles / CVs** with headline, skills, discipline, location, availability, profile summary and document upload.
- **Applications** linked to the job, employer/company, candidate profile and CV, with status workflow: New, Reviewing, Shortlisted, Interview, Offer made, Hired, Not selected and Withdrawn.
- **Candidate accounts** and a candidate dashboard for profile management, application tracking and withdrawal.
- **Employer accounts** and an employer dashboard for company profiles, job posting/editing, candidate discovery and application-status management.
- **Guest quick apply** can be enabled or disabled from HR Jobs Settings.
- **Approval controls** for employer-submitted jobs and company profiles.
- **Email notifications** for new applications, pending jobs and application-status changes.
- **JobPosting schema.org JSON-LD** on individual job pages.
- **WordPress Privacy Tools integration** for export/erase handling of candidate/application data.
- **Authenticated CV download links** in theme screens; uploaded CV filenames are randomized rather than displayed as predictable Media Library URLs.

## Gutenberg and BBuilder

The Jobs theme includes dynamic Gutenberg blocks that can be placed inside normal BBuilder Bootstrap sections:

- **HR Jobs Search** (`wpbb-jobs/search`)
- **HR Jobs Listings** (`wpbb-jobs/list`)
- **HR Hiring Companies** (`wpbb-jobs/companies`)
- **HR Candidate Profiles** (`wpbb-jobs/resumes`)
- **HR Job Categories** (`wpbb-jobs/categories`)
- **HR Salary Guide** (`wpbb-jobs/salary-guide`)
- **HR Salary Calculator** (`wpbb-jobs/salary-calculator`)
- **HR Career Advice** (`wpbb-jobs/career-advice`)

The supplied HR patterns use the same corrected sector layout structure as the other 3.8.10.67+ child themes:

**full-width section → Bootstrap `.container` → BBuilder Row → Columns**

This keeps section backgrounds full-width while headings, forms, cards and media stay aligned to the shared Bootstrap grid.

## Theme-managed Jobs pages

The setup screen can safely create or refresh these pages:

- `/jobs/`
- `/hiring-companies/`
- `/find-candidates/`
- `/candidate-dashboard/`
- `/employer-dashboard/`
- `/post-a-job/`
- `/create-resume/`
- `/login-register/`
- `/salary-guide/` — salary benchmarks and take-home pay calculator
- `/home-2/` — high-volume hiring demo with qualifying filters and A/B variants

Only pages carrying the Jobs theme ownership marker are refreshed. An existing same-slug page created independently is left untouched.

## Shortcodes

The integrated engine also exposes shortcodes for practical page composition:

- `[wpbb_jobs_search]`
- `[wpbb_jobs_list]`
- `[wpbb_jobs_companies]`
- `[wpbb_jobs_resumes]`
- `[wpbb_candidate_dashboard]`
- `[wpbb_employer_dashboard]`
- `[wpbb_post_job]`
- `[wpbb_resume_form]`
- `[wpbb_jobs_login_register]`
- `[wpbb_job_categories]`
- `[wpbb_jobs_home_metrics]`
- `[wpbb_salary_guide]`
- `[wpbb_salary_calculator]`
- `[wpbb_jobs_career_advice]`
- `[wpbb_jobs_high_volume_home]`

## Admin structure

The WordPress dashboard gets a dedicated **HR Jobs** menu with:

- Dashboard / recruitment counts and review queue
- Jobs
- Companies
- Candidate Profiles
- Applications
- Job Categories
- Job Types
- Locations / Skills through their native taxonomy screens
- Setup & Demo
- Settings

The engine uses ordinary WordPress posts, users, taxonomies and metadata instead of a private third-party database layer.

## Demo data

The Starter Setup import now creates a rich recruitment marketplace automatically without creating fake public login accounts:

- **10 employer profiles** with locations, hiring contacts and company copy;
- **20 varied vacancies** spanning technology, design, operations, finance, marketing, HR, customer success, compliance and project management;
- featured, remote/hybrid, graduate, contract, part-time and permanent examples with salary ranges and deadlines;
- **10 candidate profiles** across multiple disciplines with skills and availability;
- **8+ example applications** spread across New, Reviewing, Shortlisted, Interview, Offer, Hired, Rejected and Withdrawn states;
- job categories, job types, UK/remote locations and a useful skills taxonomy;
- the HR sector’s career/hiring advice posts and bundled imagery through the normal parent demo importer.

The repair action is idempotent: matching sample records are updated/repaired rather than duplicated.

## Important portability note

Because the recruitment engine is intentionally integrated into this child theme, switching away from **WP BBTheme Child Jobs** stops registering the Jobs post types and recruitment screens. The underlying WordPress database records are not automatically deleted, but they will not be available through normal admin/front-end screens until this theme (or compatible registration code) is active again.

For a site where recruitment data must remain independent of the active theme, a separate functionality plugin would normally be the more portable architecture. This release follows the requested all-in-one Jobs-theme model.

## Scope

This release implements the core careers / ATS / job-marketplace workflow directly in the theme. It does **not** copy commercial/premium add-ons from WP Job Portal such as paid credit packages, external AI services, premium messaging, PDF generation or payment add-ons.




## 3.8.10.73 update

- Removes the redundant **Choose your route / Start with the tools that match your role** two-card homepage section.
- Candidate and employer routes remain available through the navigation, recruitment tools, candidate routes and dedicated dashboards.
- Bumps the managed-demo repair marker so existing Jobs demo homepages are rebuilt once and the old placeholder Icon Cards disappear automatically.
- No other sector theme is changed in this Jobs-only patch.

## 3.8.10.72 update

- Fixes the English Starter Demo overwrite that could replace the rich sector homepage with empty/default BBuilder Swiper, Icon Card, Fun Fact and Catalogue blocks.
- Uses the parent theme’s canonical rich sector serializer for managed English Home, About, Services, For Candidates/Industries and Contact pages. Existing localized demo pages are preserved rather than overwritten by the repair pass.
- Adds dedicated block-theme and PHP fallback templates for individual `/job/.../` and `/employer/.../` URLs, including job metadata, employer details, description, skills and the application form.
- Refreshes the `/job/` rewrite rules once after upgrade so existing demo vacancy links resolve correctly.
- Adds shared spacing, container, card-height, media and responsive alignment fixes used across the 15-theme suite.
- Keeps the rich integrated demo marketplace: employers, 20 jobs, candidate profiles and example application workflow states remain seeded by Starter Setup.

## 3.8.10.70 update

- Refreshes the theme screenshot so the HR / Jobs preview matches the shared WP Base sector-card style used by the other child themes in **Appearance → Themes**.
## Frontend password protection

The Jobs child theme uses the same suite-wide frontend preview protection as the other maintained WP Base child themes. Configure it under **Settings → Theme Settings → General**. Protection can be enabled or disabled, and the initial demo password is `wp@demo`. The password hash and enabled state are shared across maintained child themes.



**3.8.10.80 admin fix:** the General-tab checkbox and password input are injected after ACF renders the settings panel, avoiding ACF Message-field sanitisation. The dedicated **Save protection settings** button can now actually disable the gate or change its password. The parent theme is not modified.


## Visual direction

Version 3.8.10.85 refines the multilingual Jobs demo toward a calmer business-recruitment presentation: search-first hero, softer typography, subtle borders, lighter shadows, clearer information hierarchy and consistent geometry across all supported languages.
