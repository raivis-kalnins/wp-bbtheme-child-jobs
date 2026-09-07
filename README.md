# WP BBTheme Child Jobs 3.8.10.68

A self-contained **HR / Jobs / recruitment** child theme for the WP BBTheme suite. The recruitment engine is integrated directly into the child theme, so **WP Job Portal is not required**.

## Recommended installation order

1. Install and activate the **WP BBTheme** parent theme.
2. Install and activate **WP BBuilder 5.6.8 or newer**.
3. Install and activate **WP BBTheme Child Jobs 3.8.10.68**.
4. Open **HR Jobs → Setup & Demo** (or **Appearance → HR Jobs Setup**).
5. Click **Create / refresh Jobs pages**.
6. Optionally click **Add / repair HR demo data** to seed employers, vacancies, candidate profiles and example application statuses.
7. Use the parent theme Starter Setup / BBuilder Demo workflow for the rich HR landing pages, menus and sector imagery.
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

The optional demo seeder creates realistic recruitment data without creating fake public user accounts:

- six employer profiles;
- twelve varied vacancies;
- six candidate profiles across multiple disciplines;
- example applications in several workflow states;
- useful categories, job types and UK/remote locations.

Existing matching sample records are not duplicated when the repair action is run again.

## Important portability note

Because the recruitment engine is intentionally integrated into this child theme, switching away from **WP BBTheme Child Jobs** stops registering the Jobs post types and recruitment screens. The underlying WordPress database records are not automatically deleted, but they will not be available through normal admin/front-end screens until this theme (or compatible registration code) is active again.

For a site where recruitment data must remain independent of the active theme, a separate functionality plugin would normally be the more portable architecture. This release follows the requested all-in-one Jobs-theme model.

## Scope

This release implements the core careers / ATS / job-marketplace workflow directly in the theme. It does **not** copy commercial/premium add-ons from WP Job Portal such as paid credit packages, external AI services, premium messaging, PDF generation or payment add-ons.
