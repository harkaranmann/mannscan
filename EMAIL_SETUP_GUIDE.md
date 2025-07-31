# 📧 Email Setup Guide for Mann Scanning Centre Fellowship Program

This guide will help you configure and test the email functionality for fellowship applications.

## 📋 Overview

The fellowship application system uses PHP to process form submissions and send emails to:
- harkaran@msn.com
- harkaran@mannscan.in

## 🔧 Configuration Options

### Option 1: PHP Mail (Simplest)
**Pros:** Easy to set up, no additional libraries required  
**Cons:** Less reliable, may be blocked by spam filters

**Steps:**
1. Open `process_fellowship.php`
2. Ensure `$usePhpMail = true;` is set
3. Check if your hosting provider allows PHP mail function

### Option 2: SMTP (Recommended)
**Pros:** More reliable, better deliverability, less likely to be marked as spam  
**Cons:** Requires additional setup, may need paid service

**Steps:**
1. Install PHPMailer library:
   ```bash
   composer require phpmailer/phpmailer
   ```
2. Update `config_smtp.php` with your SMTP credentials
3. Set `$useSmtp = true;` in the configuration

## 📧 Email Provider Setup

### Gmail SMTP Setup
1. Enable 2-factor authentication in your Gmail account
2. Generate an App Password:
   - Go to Google Account → Security
   - Enable 2FA
   - Generate App Password
   - Use "Mail" as the app name
3. Update `config_smtp.php`:
   ```php
   public static $smtpHost = 'smtp.gmail.com';
   public static $smtpPort = 587;
   public static $smtpUsername = 'your-email@gmail.com';
   public static $smtpPassword = 'your-app-password';
   public static $smtpSecurity = 'tls';
   ```

### Other Email Providers
- **Outlook/Hotmail:** smtp.office365.com, port 587, TLS
- **Yahoo:** smtp.mail.yahoo.com, port 587, TLS
- **Your hosting provider:** Check their documentation for SMTP settings

## 🧪 Testing Email Functionality

### Step 1: Check Server Configuration
1. Upload all files to your web server
2. Access `check_php_config.php` in your browser
3. Review the configuration report

### Step 2: Test Email Sending
1. Access `test_email.php` in your browser
2. Fill out the test form
3. Submit the form to test email delivery

### Step 3: Test Real Application
1. Navigate to `fellowship.html`
2. Fill out the fellowship application form
3. Submit and check if you receive the email

## 📁 Directory Setup

Create these directories with proper permissions:
```
mannscan-main/
├── uploads/        # For file uploads (755 permissions)
├── temp/          # For temporary files (755 permissions)
├── email_log.txt  # For email logging (644 permissions)
```

**Linux/Mac:**
```bash
mkdir uploads temp
chmod 755 uploads temp
touch email_log.txt
chmod 644 email_log.txt
```

**Windows:**
- Create directories through File Explorer
- Ensure IIS user has write permissions

## 🔍 Troubleshooting

### Common Issues

1. **Emails not sending:**
   - Check PHP error logs
   - Verify SMTP credentials
   - Ensure server allows email sending

2. **File uploads not working:**
   - Check directory permissions
   - Verify `upload_max_filesize` and `post_max_size` in php.ini
   - Ensure file types are allowed

3. **Emails going to spam:**
   - Use SMTP instead of PHP mail
   - Include proper SPF/DKIM records
   - Avoid spam trigger words

### Debug Mode

Enable debug mode in `config_smtp.php`:
```php
public static $debug = true;
```

This will create detailed logs in `email_log.txt`.

## 🚀 Deployment Checklist

- [ ] Upload all files to web server
- [ ] Create required directories (uploads, temp)
- [ ] Set proper directory permissions
- [ ] Configure email settings in `config_smtp.php`
- [ ] Test email functionality with `test_email.php`
- [ ] Test real application form
- [ ] Monitor email logs for any issues

## 📞 Support

If you continue to have issues:
1. Check the error logs on your server
2. Review the configuration report from `check_php_config.php`
3. Ensure your hosting provider allows email sending
4. Consider using a transactional email service (SendGrid, Mailgun, etc.)

## 🔒 Security Considerations

- Never commit actual email passwords to version control
- Use environment variables for sensitive configuration
- Regularly monitor email logs for suspicious activity
- Implement CAPTCHA to prevent spam submissions
- Validate all user input to prevent injection attacks

---

**Note:** Email functionality depends on your hosting provider's configuration. Some shared hosting plans may restrict email sending for security reasons. In such cases, consider using a transactional email service like SendGrid or Mailgun.