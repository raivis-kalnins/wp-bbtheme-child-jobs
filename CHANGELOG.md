# Changelog

## 3.8.10.88
- Fixes the literal `\\n` text appearing before the site header by correcting the Jobs favicon `<head>` output.
- Widens header, homepage sections, newsletter and footer to a consistent 1400px desktop Jobs canvas, removing the excessive empty gutters visible on the live site.
- Rebuilds the Homepage 1 hero composition so the supplied woman/city image is integrated into the right half of the hero with a soft blend instead of rendering as a narrow boxed column.
- Simplifies the Homepage 1 hero search presentation to keyword + location + action while keeping full job-type/category filters on the Jobs results page.
- Adds a dedicated **Post a job** header CTA and account action; Career Advice returns to a normal navigation item.
- Enlarges category, salary, vacancy, journey and advice layouts to match the supplied marketplace proportions more closely.
- Cleans guest application/public form file inputs, legal notice alignment and hCaptcha spacing.
- Fixes the footer Newsletter Campaigns layout: one email row, visible consent checkbox, no parent generic form-notice collision, and responsive stacking on mobile.
- Hardens Newsletter Campaigns hCaptcha test-mode detection for reverse proxies by checking forwarded/original host headers before allowing localhost test credentials.
- Hides the duplicate simple BBuilder cookie banner when the parent theme privacy banner is already present, and removes remaining inherited blue primary controls from Jobs UI.
- Refines Jobs dark mode around deep teal surfaces and mint/green actions.

## 3.8.10.87
- Finishes Homepage 1 against the supplied Jobs marketplace reference: integrated full-height hero visual, tighter search bar, metric strip, six-card category row, lighter salary band, compact featured jobs and advice cards, white footer and green newsletter band.
- Removes the extra homepage employer showcase and generic CTA so the page rhythm follows the supplied reference while retaining **One marketplace, two clear journeys**.
- Adds **High-volume Hiring Demo** / Homepage 2 as a real submenu under **For Employers** and inside the employer mega menu, so the previously-created managed page is discoverable from the public header.
- Replaces remaining inherited royal-blue dark-mode surfaces with a coordinated deep-teal / mint / navy Jobs palette across the header, portals, dashboards, forms, Homepage 2, newsletter and footer.
- Uses a cropped hero visual from the user-supplied design reference as the Homepage 1 demo hero asset.

## 3.8.10.86

- Finishes the public Jobs visual system against the supplied green/teal marketplace preview: hero, category row, salary block, featured jobs, inner-page surfaces, buttons and navigation.
- Adds a new Jobs wordmark and matching favicon / Apple touch icon assets without changing the parent theme.
- Removes inherited blue primary controls throughout the child theme while preserving LinkedIn's official sign-in blue as the intentional exception.
- Reworks the footer into a green newsletter band followed by a white Jobs footer and adds dedicated WP Newsletter Campaigns form styling.
- Keeps the newsletter GDPR/privacy consent checkbox visible and aligned across desktop/mobile layouts.
- Uses WP BBuilder's saved hCaptcha configuration as the shared source for newsletter and Jobs public forms; no captcha secret is stored in the child theme.
- Prevents WP Newsletter Campaigns from substituting its localhost hCaptcha test credentials when a complete BBuilder hCaptcha configuration is available.
- Adds theme-styled modal hCaptcha plus server-side verification to anonymous account registration, guest job applications and the Homepage 2 high-volume qualifying form.
- Adds 3.8.10.86 homepage, newsletter and captcha translations across DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS and bumps the managed-page repair marker so translations refresh once.
- Preserves **One marketplace, two clear journeys**, LinkedIn/CV tools, high-volume A/B demo, applicant pipeline and CRM webhook features from 3.8.10.85.

## 3.8.10.85

- Rebuilt the Jobs presentation layer toward the supplied green marketplace preview while keeping the existing recruitment engine and managed multilingual pages.
- Keeps the homepage **One marketplace, two clear journeys** candidate/employer section and moves the homepage rhythm to search → categories → salary → featured jobs → journeys → companies → advice.
- Adds optional **LinkedIn OpenID Connect** sign-in with name, email and profile-photo prefill; credentials remain server-side in HR Jobs Settings.
- Expands the candidate profile into a structured **CV builder** with profile image, current role, website, LinkedIn URL, languages, summary, work experience, education and original CV upload. DOCX uploads can best-effort seed editable CV text when the server has ZipArchive.
- Adds multilingual **Homepage 2 / High-volume Hiring Demo** with a large qualifying CTA, role/location/type filtering and stable A/B landing-page variants (`?jobs_variant=a` / `?jobs_variant=b`).
- Adds an employer **application pipeline** with stage cards and inline status changes for New, Reviewing, Shortlisted, Interview, Offer made and Hired.
- Adds an optional **CRM webhook** for new applications and status changes, with an optional HMAC SHA-256 signature.
- Extends the managed Polylang page set and Jobs translation dictionary for the new high-volume journey across EN, DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS.
- Completes the earlier multilingual navigation, Contact, result-spacing and dark-mode polish.
- Parent theme remains unchanged.

## 3.8.10.84

- Jobs-only multilingual polish: rebuilt managed translated pages from the current local theme assets so hero and journey images no longer retain stale/broken URLs.
- Simplified the desktop header to six primary destinations; About/Contact move to the utility/footer menus and Salary Guide sits under For Candidates.
- Added a consistent separator/rhythm between the Jobs filters and the first vacancy row.
- Rebuilt Contact as four matching contact-method cards plus a clean recruitment enquiry form.
- Added a complete Jobs dark-mode layer for header, footer/newsletter, cards, forms, search, salary tools and contact surfaces.
- Parent theme remains unchanged.

## 3.8.10.83

- Refined the Jobs homepage into a calmer, more business-focused recruitment design across every supported language.
- Reduced oversized hero/section typography and softened borders, shadows and background treatments.
- Rebalanced the hero image/search layout, metrics, categories, companies, salary planner, vacancy cards, candidate/employer routes and career-advice cards.
- Kept multilingual layout parity: EN, DE, ES, FR, PL, RU, LV, LT, ET, DA, SV, NB, FI and IS use the same component geometry and responsive rules.
- Preserves the 3.8.10.82 menu repair, 3.8.10.80 password controls and all integrated recruitment functionality.

## 3.8.10.82

- Jobs-only managed navigation repair. Removes duplicate/triplicate managed Header, Utility and Footer items left by earlier demo refreshes.
- Rebuilds all Polylang Jobs menus from the cleaned English managed menu so Latvian, Russian and the remaining translated menus stay in parity.
- Adds a frontend de-duplication guard for managed Jobs menus so stale duplicate menu rows can never render three times.
- Tightens the desktop Jobs header spacing for longer translated navigation labels while preserving the normal mobile drawer breakpoint.
- Parent theme remains unchanged.

## 3.8.10.81
- Keeps Theme Settings saves and WordPress theme update/upload requests responsive by pausing legacy child-owned demo/media migration callbacks for those interactive requests.
- Keeps the working 3.8.10.80 Frontend Password Protection controls unchanged.
- Parent theme is unchanged.

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
