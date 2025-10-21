# Fellowship Form Setup Guide

The fellowship application form now uses a third-party service so it can run on static hosting (e.g., GitHub Pages). Follow the steps below to activate email delivery.

## Option 1: Web3Forms (Recommended – already wired up)

### Steps to Activate

1. **Get your access key**
   - Go to https://web3forms.com/
   - Click **Get Started for Free**
   - Sign up with **drhsmann@gmail.com**
   - Confirm the verification email sent by Web3Forms
   - Copy the access key from your Web3Forms dashboard

2. **Update the form**
   - Open `fellowship.html`
   - Locate the hidden field (around line 651):
     ```html
     <input type="hidden" name="access_key" value="YOUR_ACCESS_KEY_HERE">
     ```
   - Replace `YOUR_ACCESS_KEY_HERE` with the access key you copied

3. **Test**
   - Visit https://mannscan.in/fellowship.html
   - Switch to the **Apply Now** tab
   - Submit a test application
   - Approve the first confirmation email from Web3Forms if prompted
   - Check **drhsmann@gmail.com** for the submission details

### Why Web3Forms?
- Free forever with generous limits
- No backend or PHP server required
- Built-in spam protection
- Emails land in **drhsmann@gmail.com**
- Works on any static host (including GitHub Pages)
- GDPR compliant and ad-free

## Option 2: Formspree (Alternative)

If you prefer Formspree:

1. Sign up at https://formspree.io/ using **drhsmann@gmail.com**
2. Create a new form and copy its endpoint URL
3. In `fellowship.html`, change the `<form>` action to the Formspree endpoint

## Current Form Behaviour

- **Success:** Shows an inline success message and resets the form
- **Error:** Shows an inline error with instructions to email manually
- **Validation:** Client-side checks ensure required fields are filled
- **Fallback:** If the access key is missing, the form opens a `mailto:` email so applicants can still send their details

## Email Contents

Each submission includes:
- First and last name
- Email & phone number
- Primary degree & specialization
- Years of experience & current institution
- Preferred program type & start date
- Motivation and expectations answers

## Troubleshooting

- **Form does nothing:** Replace the placeholder access key with your live key
- **Not receiving mail:** Check the spam folder and verify delivery inside the Web3Forms dashboard
- **Still seeing 404/405 errors:** Deploy only the static files; PHP is no longer required
- **Need attachments:** Upgrade to a paid plan or connect a different service that supports file uploads

---

**Last Updated:** October 21, 2025  
**Status:** Ready to activate (just add the Web3Forms access key)
