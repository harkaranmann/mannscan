# Mann Scanning Centre Website

This repository contains the static website for **Mann Scanning Centre**. The site is built entirely with HTML, CSS and vanilla JavaScript. No build tools or server side code are required.

## Setup

1. Clone the repository
   ```bash
   git clone <repo-url>
   cd mannscan
   ```
2. Serve the files locally using any simple HTTP server. For example:
   ```bash
   # with Python
   python3 -m http.server 8000
   ```
   Then open `http://localhost:8000/index.html` in your browser.

## Project Structure

```
mannscan/
├── index.html            # Homepage with interactive animation
├── about.html            # About the facility
├── about-enhanced.html   # Alternative about page
├── services.html         # List of services and equipment
├── contact.html          # Contact information
├── gallery.html          # Image gallery
├── lmp-calculator.html   # Gestational age calculator
├── animation.html        # Experimental background animation
├── animation-3d.html     # 3D animation demo
├── image-processor.html  # Utility for removing image backgrounds
├── css/
│   └── style.css         # Main theme and layout styles
├── style.css             # Additional styles for simple pages
├── images/               # Image assets
└── README.md             # Project documentation
```

## Deployment

Because the site is fully static, it can be hosted on any static hosting platform.

### GitHub Pages

1. Push the project to a GitHub repository.
2. Enable **GitHub Pages** in the repository settings and choose the `main` branch (or a dedicated `/docs` folder) as the source.
3. Your site will be available at `https://<username>.github.io/<repository>/`.

### Other Hosts

You can also deploy the site to services like Netlify, Vercel or any standard web server by uploading the repository contents.

---

Feel free to customize the pages or extend the functionality. For more advanced development consider modularizing the repeated JavaScript and CSS into separate files.
