# GitHub Deployment Commands for Mann Scanning Centre

# Step 1: Initialize Git repository (if not already done)
git init

# Step 2: Add all files to staging
git add .

# Step 3: Create initial commit
git commit -m "Initial commit - Mann Scanning Centre website for mannscan.in"

# Step 4: Set main branch
git branch -M main

# Step 5: Add remote repository
git remote add origin https://github.com/harkaranmann/mannscan.git

# Step 6: Push to GitHub
git push -u origin main

# After pushing to GitHub:
# 1. Go to https://github.com/harkaranmann/mannscan/settings/pages
# 2. Enable GitHub Pages (Settings > Pages)
# 3. Select "main" branch and "/" root
# 4. Set custom domain to "mannscan.in"
# 5. Configure DNS as per DEPLOYMENT_GUIDE.md

# Future updates (after initial setup):
# git add .
# git commit -m "Updated website content"
# git push

echo "Repository: https://github.com/harkaranmann/mannscan"
echo "Remember to:"
echo "1. Configure DNS records in Cloudflare (see below)"
echo "2. Wait 24-48 hours for full DNS propagation"
echo "3. Enable HTTPS in GitHub Pages after DNS is working"
