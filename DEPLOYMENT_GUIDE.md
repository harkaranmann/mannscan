# GitHub Pages Deployment with Custom Domain (mannscan.in)

## 🚀 Complete Deployment Guide

### Step 1: Prepare GitHub Repository

1. **Create GitHub Repository**
   - Go to https://github.com and create a new repository
   - Name it `mannscan-website` (or any name you prefer)
   - Make it **Public** (required for free GitHub Pages)
   - Don't initialize with README (you already have files)

2. **Upload Your Website Files**
   ```bash
   # In your project directory (mannscan-main)
   git init
   git add .
   git commit -m "Initial commit - Mann Scanning Centre website"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/mannscan-website.git
   git push -u origin main
   ```

### Step 2: Configure GitHub Pages

1. **Enable GitHub Pages**
   - Go to your repository on GitHub
   - Click **Settings** tab
   - Scroll down to **Pages** section
   - Under **Source**, select "Deploy from a branch"
   - Choose **main** branch and **/ (root)** folder
   - Click **Save**

2. **Configure Custom Domain**
   - In the **Pages** section, under **Custom domain**
   - Enter: `mannscan.in`
   - Click **Save**
   - Check **Enforce HTTPS** (after DNS is configured)

### Step 3: Create CNAME File

Create a CNAME file in your repository root:

```bash
# Content of CNAME file (no extension)
mannscan.in
```

### Step 4: Configure DNS with Hostinger

**A. For Root Domain (mannscan.in):**

Add these **A Records**:
```
Type: A
Name: @
Value: 185.199.108.153
TTL: 300

Type: A
Name: @
Value: 185.199.109.153
TTL: 300

Type: A
Name: @
Value: 185.199.110.153
TTL: 300

Type: A
Name: @
Value: 185.199.111.153
TTL: 300
```

**B. For WWW Subdomain (optional):**

Add **CNAME Record**:
```
Type: CNAME
Name: www
Value: YOUR_USERNAME.github.io
TTL: 300
```

### Step 5: Configure Cloudflare

1. **Add Domain to Cloudflare**
   - Log into Cloudflare dashboard
   - Click "Add Site"
   - Enter `mannscan.in`
   - Choose Free plan

2. **Update Nameservers at Hostinger**
   - Go to Hostinger domain management
   - Change nameservers to Cloudflare's provided nameservers
   - Example: `alex.ns.cloudflare.com` and `maya.ns.cloudflare.com`

3. **Configure DNS in Cloudflare**
   
   **A Records for GitHub Pages:**
   ```
   Type: A
   Name: @
   IPv4: 185.199.108.153
   Proxy: Orange Cloud (Proxied)
   
   Type: A
   Name: @
   IPv4: 185.199.109.153
   Proxy: Orange Cloud (Proxied)
   
   Type: A
   Name: @
   IPv4: 185.199.110.153
   Proxy: Orange Cloud (Proxied)
   
   Type: A
   Name: @
   IPv4: 185.199.111.153
   Proxy: Orange Cloud (Proxied)
   ```

   **CNAME for WWW:**
   ```
   Type: CNAME
   Name: www
   Target: YOUR_USERNAME.github.io
   Proxy: Orange Cloud (Proxied)
   ```

4. **Configure Page Rules (Optional)**
   ```
   Rule 1: www.mannscan.in/*
   Action: Forwarding URL (301 - Permanent Redirect)
   Destination: https://mannscan.in/$1
   
   Rule 2: mannscan.in/*
   Actions: 
   - Always Use HTTPS: On
   - Browser Cache TTL: 4 hours
   - Cache Level: Standard
   ```

### Step 6: SSL/TLS Configuration

1. **In Cloudflare SSL/TLS Tab:**
   - Set encryption mode to **"Full"** or **"Full (strict)"**
   - Enable **"Always Use HTTPS"**
   - Enable **"Automatic HTTPS Rewrites"**

2. **In GitHub Pages:**
   - After DNS propagation (24-48 hours), check **"Enforce HTTPS"**

### Step 7: Performance Optimization

**Cloudflare Settings:**

1. **Speed Tab:**
   - Enable **Auto Minify** (HTML, CSS, JS)
   - Enable **Brotli compression**
   - Enable **Rocket Loader** (optional)

2. **Caching Tab:**
   - Set **Browser Cache TTL** to 4 hours
   - Configure **Page Rules** for static assets

### Step 8: Verification & Testing

1. **DNS Propagation Check**
   ```bash
   # Check if DNS is working
   nslookup mannscan.in
   dig mannscan.in
   ```

2. **Test Your Website**
   - Visit `https://mannscan.in`
   - Test all pages and functionality
   - Verify SSL certificate is working
   - Check mobile responsiveness

### Step 9: SEO & Analytics Setup

1. **Google Search Console**
   - Add property for `https://mannscan.in`
   - Submit sitemap: `https://mannscan.in/sitemap.xml`

2. **Google Analytics 4**
   - Replace `G-XXXXXXXXXX` in the setup guide with your actual tracking ID
   - Add to all HTML pages

### Expected Timeline

- **GitHub Pages deployment**: 5-10 minutes
- **DNS propagation**: 2-24 hours
- **SSL certificate activation**: 24-48 hours after DNS
- **Full optimization**: 48-72 hours

### Troubleshooting

**Common Issues:**

1. **"Page not found" error**
   - Check repository is public
   - Verify files are in root directory
   - Check GitHub Pages is enabled

2. **Custom domain not working**
   - Verify CNAME file exists and contains `mannscan.in`
   - Check DNS records are correct
   - Wait for DNS propagation

3. **SSL certificate issues**
   - Ensure DNS is properly configured
   - Wait 24-48 hours after DNS changes
   - Try disabling and re-enabling "Enforce HTTPS"

4. **Cloudflare errors**
   - Check nameservers are updated at Hostinger
   - Verify DNS records in Cloudflare
   - Ensure proxy status is correct (orange cloud)

### Post-Deployment Checklist

- [ ] Website loads at `https://mannscan.in`
- [ ] All pages are accessible
- [ ] SSL certificate is active
- [ ] Mobile version works correctly
- [ ] Contact forms work (if applicable)
- [ ] Fellowship application links work
- [ ] DICOM viewer links function
- [ ] LMP calculator works
- [ ] SEO meta tags are correct
- [ ] Google Analytics is tracking
- [ ] Sitemap is accessible

### Ongoing Maintenance

1. **Regular Updates**
   - Push changes to GitHub repository
   - GitHub Pages will auto-deploy

2. **Monitoring**
   - Use Google Analytics for traffic
   - Monitor Google Search Console for SEO
   - Check Cloudflare analytics for performance

3. **Backups**
   - GitHub repository serves as backup
   - Download site files periodically

## 🎯 Your Website URLs

After deployment:
- **Primary**: https://mannscan.in
- **GitHub**: https://YOUR_USERNAME.github.io/mannscan-website
- **Sitemap**: https://mannscan.in/sitemap.xml
- **Fellowship**: https://mannscan.in/fellowship.html

Replace `YOUR_USERNAME` with your actual GitHub username throughout this guide.
