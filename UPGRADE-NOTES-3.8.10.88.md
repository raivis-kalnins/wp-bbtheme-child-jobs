# Jobs Theme 3.8.10.88 upgrade notes

## What this fixes

- Removes the visible literal `\n` characters above the header.
- Expands the Jobs layout from the old narrow demo width to a 1400px desktop canvas so the header, hero, cards, newsletter and footer align with the supplied marketplace reference.
- Integrates the woman/city hero visual into the right side of Homepage 1 instead of leaving a large unused blue area.
- Makes the Homepage 1 search bar visually match the reference: keyword, location and Search jobs. Full job type/category filters remain available on the Jobs page.
- Adds a separate **Post a job** action and account icon to the header; Career Advice is no longer used as the green CTA.
- Improves category, salary, featured-job, journey and advice card sizing/spacing.
- Repairs footer Newsletter Campaigns alignment and keeps the required privacy-consent checkbox visible below the email field. The redundant parent generic form notice is removed only from this newsletter form.
- Improves guest application/public Jobs form file inputs and legal/privacy notice spacing.
- Keeps hCaptcha modal verification, while production host detection now understands `X-Forwarded-Host` / original-host proxy headers so a live reverse-proxied site does not accidentally use hCaptcha localhost test credentials.
- Hides the duplicate BBuilder cookie banner if the parent theme privacy banner is already present.
- Refines dark mode and overrides inherited BBuilder/Bootstrap blue primary variables with the Jobs green/teal palette.

## Install / refresh

1. Replace the existing child theme with **3.8.10.88**.
2. Clear any page cache, optimisation cache and CDN cache.
3. Hard-refresh the browser so `jobs-v88.css` and `jobs-v88.js` are loaded.
4. Check Homepage 1 at desktop width first. The content should now occupy most of the viewport instead of sitting in a narrow central strip.
5. Check **For Employers → High-volume Hiring Demo**; the submenu added in 3.8.10.87 remains in place.
6. Test light/dark mode, a guest job application, and the footer newsletter form.
7. On a real public hostname, inspect the newsletter hCaptcha modal. It should use the BBuilder production site key; genuine `localhost` continues to use hCaptcha test mode.

## Homepage 2 QA

- `/home-2/?jobs_variant=a` forces variant A.
- `/home-2/?jobs_variant=b` forces variant B.
