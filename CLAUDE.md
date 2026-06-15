# Mann Scanning Centre Website

Marketing/informational site for Mann Scanning Centre (diagnostic imaging, Jalandhar, Punjab). Includes services, an ultrasound fellowship program, integrated DICOM web viewing, and an LMP/gestational-age calculator.

## Stack
- **Pure static** — HTML5 + CSS3 (glassmorphism, dark/light theme) + vanilla JS. **No build step**; open any `.html` in a browser.
- Only dev dep is Jest (LMP calculator unit tests).

## Layout
- Pages: `index.html` (home + fellowship), `about.html`, `services.html`, `contact.html`, `fellowship.html`, `lmp-calculator.html`.
- `css/style.css` (theme management), `js/lmp.js` (calculator), `images/`, `tests/lmp.test.js`, `context.json` (project config/docs).

## Commands
- `npm install` then `npm test` (Jest) — covers the LMP calculator.
- Deploy: GitHub Pages (push static files).

## Conventions
- Keep it dependency-free and accessible (semantic markup). Theme is toggled in JS — preserve dark/light parity when editing styles.
