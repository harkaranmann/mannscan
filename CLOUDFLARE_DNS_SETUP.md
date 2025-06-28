# Cloudflare DNS Configuration for mannscan.in

## 🌐 GitHub Repository
- **Repository**: https://github.com/harkaranmann/mannscan
- **GitHub Pages URL**: https://harkaranmann.github.io/mannscan
- **Custom Domain**: https://mannscan.in

## ⚙️ Cloudflare DNS Records Configuration

### Step 1: Access Cloudflare Dashboard
1. Log into your Cloudflare account
2. Select your domain: `mannscan.in`
3. Go to **DNS** > **Records**

### Step 2: Delete Existing Records (if any)
Remove any existing A, AAAA, or CNAME records for:
- `@` (root domain)
- `www`

### Step 3: Add GitHub Pages A Records

Add these **4 A Records** for the root domain (`@`):

```
Type: A
Name: @
IPv4: 185.199.108.153
Proxy status: 🟠 Proxied
TTL: Auto

Type: A
Name: @
IPv4: 185.199.109.153
Proxy status: 🟠 Proxied
TTL: Auto

Type: A
Name: @
IPv4: 185.199.110.153
Proxy status: 🟠 Proxied
TTL: Auto

Type: A
Name: @
IPv4: 185.199.111.153
Proxy status: 🟠 Proxied
TTL: Auto
```

### Step 4: Add WWW CNAME Record (Optional)

```
Type: CNAME
Name: www
Target: harkaranmann.github.io
Proxy status: 🟠 Proxied
TTL: Auto
```

### Step 5: Configure GitHub Pages

1. Go to: https://github.com/harkaranmann/mannscan/settings/pages
2. Under **Source**: Select "Deploy from a branch"
3. Choose **main** branch and **/ (root)** folder
4. Under **Custom domain**: Enter `mannscan.in`
5. Click **Save**
6. ✅ **DO NOT** check "Enforce HTTPS" yet (wait for DNS propagation)

## 🔧 Cloudflare Optimization Settings

### SSL/TLS Settings
1. Go to **SSL/TLS** > **Overview**
2. Set encryption mode to: **Full** (not Full Strict initially)
3. Enable **Always Use HTTPS**
4. Enable **Automatic HTTPS Rewrites**

### Speed Settings
1. Go to **Speed** > **Optimization**
2. Enable **Auto Minify**: ✅ HTML, ✅ CSS, ✅ JS
3. Enable **Brotli** compression
4. Enable **Early Hints**

### Page Rules (Optional)
Create these rules in **Rules** > **Page Rules**:

**Rule 1: Redirect www to non-www**
```
URL: www.mannscan.in/*
Settings: Forwarding URL (301 - Permanent Redirect)
Destination: https://mannscan.in/$1
```

**Rule 2: Cache optimization**
```
URL: mannscan.in/*
Settings:
- Browser Cache TTL: 4 hours
- Cache Level: Standard
- Always Use HTTPS: On
```

## 📋 Verification Checklist

### Immediate Actions:
- [ ] Add all 4 A records to Cloudflare
- [ ] Add CNAME record for www (optional)
- [ ] Configure GitHub Pages with custom domain
- [ ] Set SSL/TLS mode to "Full"

### After 2-6 Hours:
- [ ] Test `https://mannscan.in` (should load your site)
- [ ] Check SSL certificate is working
- [ ] Test www redirect (if configured)

### After 24-48 Hours:
- [ ] Enable "Enforce HTTPS" in GitHub Pages
- [ ] Change SSL/TLS mode to "Full (strict)" in Cloudflare
- [ ] Submit sitemap to Google Search Console

## 🔍 Testing Commands

After DNS propagation, test these:

```bash
# Check DNS resolution
nslookup mannscan.in

# Check A records
dig A mannscan.in

# Check CNAME
dig CNAME www.mannscan.in

# Test website
curl -I https://mannscan.in
```

## 🚨 Troubleshooting

### "Page not found" (404 error)
- Check GitHub Pages is enabled
- Verify CNAME file contains `mannscan.in`
- Ensure repository is public
- Wait for DNS propagation (up to 48 hours)

### SSL Certificate Issues
- Wait 24-48 hours after DNS changes
- Try disabling/re-enabling "Enforce HTTPS" in GitHub
- Check Cloudflare SSL/TLS mode is "Full"

### Cloudflare 1XXX Errors
- Verify A records point to correct GitHub IPs
- Check proxy status is enabled (🟠 orange cloud)
- Ensure GitHub Pages custom domain is set correctly

## 📱 Mobile & Performance Testing

After deployment, test:
- Mobile responsiveness
- Page load speed
- Fellowship application links
- DICOM viewer links
- LMP calculator functionality

## 🎯 Expected Timeline

- **DNS changes**: Immediate in Cloudflare
- **GitHub Pages recognition**: 10-30 minutes
- **Initial site loading**: 2-6 hours
- **Full SSL activation**: 24-48 hours
- **Complete optimization**: 48-72 hours

## 📞 Your Live Website URLs

After deployment:
- **Primary**: https://mannscan.in
- **GitHub Direct**: https://harkaranmann.github.io/mannscan
- **Fellowship Page**: https://mannscan.in/#fellowship
- **Sitemap**: https://mannscan.in/sitemap.xml

---

**Next Step**: Configure the DNS records above in Cloudflare, then push your code to GitHub!
