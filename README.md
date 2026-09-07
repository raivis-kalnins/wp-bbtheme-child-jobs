# WP BBTheme Child Jobs 3.8.10.72

A self-contained **HR / Jobs / recruitment** child theme for the WP BBTheme suite. The recruitment engine is integrated directly into the child theme, so **WP Job Portal is not required**.

## Recommended installation order

1. Install and activate the **WP BBTheme** parent theme.
2. Install and activate **WP BBuilder 5.6.8 or newer**.
3. Install and activate **WP BBTheme Child Jobs 3.8.10.72**.
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



## 3.8.10.72 update

- Fixes the English Starter Demo overwrite that could replace the rich sector homepage with empty/default BBuilder Swiper, Icon Card, Fun Fact and Catalogue blocks.
- Uses the parent theme’s canonical rich sector serializer for managed English Home, About, Services, For Candidates/Industries and Contact pages. Existing localized demo pages are preserved rather than overwritten by the repair pass.
- Adds dedicated block-theme and PHP fallback templates for individual `/job/.../` and `/employer/.../` URLs, including job metadata, employer details, description, skills and the application form.
- Refreshes the `/job/` rewrite rules once after upgrade so existing demo vacancy links resolve correctly.
- Adds shared spacing, container, card-height, media and responsive alignment fixes used across the 15-theme suite.
- Keeps the rich integrated demo marketplace: employers, 20 jobs, candidate profiles and example application workflow states remain seeded by Starter Setup.

## 3.8.10.70 update

- Refreshes the theme screenshot so the HR / Jobs preview matches the shared WP Base sector-card style used by the other child themes in **Appearance → Themes**.
