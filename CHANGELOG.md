# Changelog

## 3.8.10.80

- Rebuilds every Starter Setup-managed Jobs homepage translation from the same search-led BBuilder structure as English.
- Adds complete demo-facing translation coverage for the homepage, job categories/types, sample vacancy titles and descriptions, employer cards, salary planner/calculator, candidate/employer routes, career advice, navigation and footer labels across DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS.
- Localises sample Jobs CPT titles/excerpts at render time without duplicating or rewriting user-created recruitment data.
- Fixes translated homepage image URLs by serialising each managed language page in its target language instead of copying stale absolute demo markup.
- Standardises hero, metrics, categories, employers, salary tools, vacancies, candidate/employer routes and career-advice grids so long translated strings keep the same alignment and responsive rhythm as English.
- Fixes oversized/empty salary-calculator space and normalises route/advice image aspect ratios in translated pages.
- Finalises the frontend-password settings fix for ACF Theme Settings: checkbox/password inputs are now created after ACF renders the General tab, so Enable/Disable and Change Password are usable and save through AJAX without modifying the parent theme.

## 3.8.10.79

- Fixes Frontend Password Protection controls inside Settings → Theme Settings → General.
- Removes the invalid nested admin form; Enable/Disable and Change Password now save via the main Theme Settings form or the dedicated Save protection settings button.
- Keeps protection state/password suite-wide when switching maintained child themes.
- Parent theme is unchanged.

## 3.8.10.78

- Added the finished search-led Jobs homepage presentation layer for the hero, category discovery, employers, salary planner, latest jobs, candidate/employer routes and career-advice cards.
- Re-synchronises every Starter Setup-managed Polylang demo page to the same 3.8.10.78 homepage structure instead of updating English only.
- Added Jobs-sector starter translations for German, Spanish, French, Polish, Russian, Latvian, Lithuanian, Estonian, Danish, Swedish, Norwegian Bokmal, Finnish and Icelandic.
- Localises managed internal Jobs links after translation so language variants stay inside their own locale.
- Keeps canonical English URLs while demo content is generated in wp-admin, preventing admin language state from leaking translated links into the English source page.
- Added matching Gutenberg editor preview styles for the new homepage discovery and salary blocks.

## 3.8.10.76

- Rebuilt the Jobs homepage around job search as the primary action.
- Added job-category discovery, current-opportunity metrics and richer employer discovery.
- Added a Salary Guide page with live demo-vacancy salary benchmarks.
- Added an integrated illustrative UK take-home pay calculator for the 2026/27 tax year.
- Added salary/category/career-advice Gutenberg blocks and matching shortcodes.
- Added Salary Guide to the Jobs navigation and recruitment mega menu.
- Bumped the managed-demo repair marker so existing managed Jobs homepages refresh once after upgrade.

## 3.8.10.73

- Removed the redundant candidate/employer pathway Icon Card section from the Jobs homepage.
- Forced one managed-demo homepage refresh so existing placeholder cards are removed after upgrade.
- Kept all candidate/employer routes available through navigation, recruitment sections and dashboards.

## 3.8.10.72

- Repaired the managed English demo from the parent canonical rich sector content instead of the old compact fallback serializer.
- Preserved existing localized demo pages during the child-level repair pass.
- Added deterministic single Job and Employer templates for block and classic template resolution.
- Added one-time rewrite refresh for `/job/` routes after upgrade.
- Added suite-wide layout rhythm and WooCommerce control compatibility rules.

## 3.8.10.70

- Replaces the distinct TalentBridge-style theme screenshot with the shared WP Base sector preview-card layout so the Jobs theme visually matches the rest of the suite in wp-admin.

3.8.10.68
- Removed the WP Job Portal dependency completely.
- Added an integrated WordPress-native recruitment engine to the HR / Jobs child theme.
- Added Jobs, Companies, Candidate Profiles and Applications content types plus job categories, job types, locations and skills.
- Added Candidate and Employer WordPress roles with front-end registration and role-specific dashboards.
- Added employer company creation, job posting/editing and application-review workflows.
- Added candidate profile/CV management, job applications, application tracking and withdrawal.
- Added configurable guest quick apply, job/company approval, salary currency and recruitment notification email.
- Added dynamic Gutenberg blocks for job search, job listings, companies and candidate profiles.
- Rebuilt Jobs patterns and managed pages so they contain no WP Job Portal blocks or shortcodes.
- Added realistic optional HR demo companies, vacancies, candidate profiles and application states.
- Added JobPosting structured data, application/status email notifications and WordPress Privacy Tools integration.
- Added authenticated CV download routes and randomized uploaded CV filenames.
- Kept the corrected full-width section → Bootstrap container → BBuilder Row/Column layout used across the 3.8.10.67 sector suite.

## 3.8.10.67
- Restored full-width demo section shells with an inner Bootstrap container.
- Removed duplicate section margins/padding and aligned cards, media rows, stats and BBuilder catalogue grids.
- Added responsive layout fixes shared by all sector demos.
- Fixed one-column demo card rows (notably Contact) so cards use full available column width.

## 3.8.10.66
- Added the initial HR / Jobs sector child theme and recruitment-specific demo presentation.
