# GitHub Copilot Instructions — Mannscan Website

## Project Overview
- Static site (HTML/CSS/JS) for Mann Scanning Centre.
- No build step; open HTML directly in browser.
- Key pages: `index.html`, `about.html`, `services.html`, `contact.html`, `fellowship.html`, `lmp-calculator.html`.

## Tech Stack
- HTML5, CSS3, vanilla JavaScript only.
- No frameworks, no bundlers.

## Conventions
- Keep accessibility in mind: semantic HTML, `aria-*` where needed.
- Maintain consistent typography and spacing across pages.
- Reuse styles in `css/style.css`; avoid inline styles.
- JS belongs in `js/` files; keep it minimal.

## Testing
- Unit tests exist for LMP calculator under `tests/` (if present). Do not delete or move source tests.
- Manually verify responsive layout on desktop/tablet/mobile widths.

## Deployment Notes
- GitHub Pages and custom domain (`mannscan.in`) are supported.
- `CNAME` and `sitemap.xml` must remain in root if present.

## Do NOT
- Introduce new dependencies without explicit approval.
- Break existing navigation or SEO metadata.
