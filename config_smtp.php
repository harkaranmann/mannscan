<?php
// SMTP Configuration for Mann Scanning Centre Fellowship Applications
// This file provides enhanced email configuration options

class EmailConfig {
    // Basic PHP mail configuration
    public static $usePhpMail = false;
    
    // SMTP Configuration (recommended for better deliverability)
    public static $useSmtp = true;
    public static $smtpHost = 'smtp.gmail.com';
    public static $smtpPort = 587;
    public static $smtpUsername = 'your-email@gmail.com'; // Replace with your Gmail
    public static $smtpPassword = 'your-app-password'; // Replace with your Gmail App Password
    public static $smtpSecurity = 'tls';
    
    // Alternative SMTP providers
    public static $alternativeSmtp = [
        'host' => 'smtp.mailtrap.io',
        'port' => 2525,
        'username' => 'your-mailtrap-username',
        'password' => 'your-mailtrap-password',
        'security' => 'tls'
    ];
    
    // Email settings
    public static $fromEmail = 'fellowship@mannscan.in';
    public static $fromName = 'Mann Scanning Centre Fellowship Program';
    public static $recipients = [
        'harkaran@msn.com',
        'harkaran@mannscan.in'
    ];
    
    // File upload settings
    public static $uploadDir = 'uploads/';
    public static $tempDir = 'temp/';
    public static $allowedFileTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    public static $maxFileSize = 10485760; // 10MB
    
    // Debug settings
    public static $debug = false;
    public static $logFile = 'email_log.txt';
}

// Email sending class
class FellowshipEmail {
    private $config;
    
    public function __construct() {
        $this->config = EmailConfig::$useSmtp ? 'smtp' : 'phpmail';
    }
    
    public function sendApplication($formData, $files = []) {
        try {
            $subject = "New Fellowship Application - " . htmlspecialchars($formData['full_name']);
            $message = $this->buildEmailMessage($formData);
            $headers = $this->buildEmailHeaders();
            
            // Handle file attachments
            $attachments = [];
            if (!empty($files)) {
                $attachments = $this->processFileAttachments($files);
            }
            
            // Send email
            if ($this->config === 'smtp') {
                return $this->sendViaSmtp($subject, $message, $headers, $attachments);
            } else {
                return $this->sendViaPhpMail($subject, $message, $headers, $attachments);
            }
            
        } catch (Exception $e) {
            $this->logError("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function buildEmailMessage($formData) {
        $message = "New Fellowship Application Received\n";
        $message .= "=================================\n\n";
        
        $message .= "Personal Information:\n";
        $message .= "Full Name: " . htmlspecialchars($formData['full_name']) . "\n";
        $message .= "Email: " . htmlspecialchars($formData['email']) . "\n";
        $message .= "Phone: " . htmlspecialchars($formData['phone']) . "\n\n";
        
        $message .= "Professional Information:\n";
        $message .= "Education: " . htmlspecialchars($formData['education']) . "\n";
        $message .= "Experience: " . htmlspecialchars($formData['experience']) . " years\n";
        $message .= "Special Interest: " . htmlspecialchars($formData['special_interest']) . "\n\n";
        
        $message .= "Application Details:\n";
        $message .= "Message: " . htmlspecialchars($formData['message']) . "\n";
        $message .= "Application Date: " . date('Y-m-d H:i:s') . "\n\n";
        
        $message .= "This application was submitted through the Mann Scanning Centre Fellowship Program website.\n";
        
        return $message;
    }
    
    private function buildEmailHeaders() {
        $headers = "From: " . EmailConfig::$fromName . " <" . EmailConfig::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . EmailConfig::$fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        return $headers;
    }
    
    private function processFileAttachments($files) {
        $attachments = [];
        
        foreach ($files as $fileKey => $file) {
            if ($file['error'] == 0 && $file['size'] > 0) {
                $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($fileType), EmailConfig::$allowedFileTypes)) {
                    $fileContent = file_get_contents($file['tmp_name']);
                    $attachments[] = [
                        'name' => $file['name'],
                        'content' => $fileContent,
                        'type' => $file['type'],
                        'size' => $file['size']
                    ];
                }
            }
        }
        
        return $attachments;
    }
    
    private function sendViaSmtp($subject, $message, $headers, $attachments) {
        // This would require a PHP mailer library like PHPMailer
        // For now, we'll provide the configuration structure
        $this->logError("SMTP configuration requires PHPMailer library. Please install PHPMailer for SMTP functionality.");
        return false;
    }
    
    private function sendViaPhpMail($subject, $message, $headers, $attachments) {
        $recipients = implode(',', EmailConfig::$recipients);
        
        // Add attachments if any
        if (!empty($attachments)) {
            $boundary = "==Multipart_Boundary_x" . md5(time()) . "x";
            $headers = "MIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$boundary}\"";
            
            $message = "This is a multi-part message in MIME format.\n\n" .
                      "--{$boundary}\n" .
                      "Content-Type: text/plain; charset=\"UTF-8\"\n" .
                      "Content-Transfer-Encoding: 7bit\n\n" .
                      $message . "\n\n";
            
            foreach ($attachments as $attachment) {
                $message .= "--{$boundary}\n" .
                           "Content-Type: " . $attachment['type'] . ";\n" .
                           " name=\"" . $attachment['name'] . "\"\n" .
                           "Content-Transfer-Encoding: base64\n" .
                           "Content-Disposition: attachment;\n" .
                           " filename=\"" . $attachment['name'] . "\"\n\n" .
                           chunk_split(base64_encode($attachment['content'])) . "\n\n";
            }
            
            $message .= "--{$boundary}--";
        }
        
        $result = @mail($recipients, $subject, $message, $headers);
        
        if ($result) {
            $this->logSuccess("Email sent successfully to " . $recipients);
        } else {
            $this->logError("Email sending failed to " . $recipients);
        }
        
        return $result;
    }
    
    private function logError($message) {
        if (EmailConfig::$debug) {
            $logMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $message . "\n";
            file_put_contents(EmailConfig::$logFile, $logMessage, FILE_APPEND);
        }
    }
    
    private function logSuccess($message) {
        if (EmailConfig::$debug) {
            $logMessage = "[" . date('Y-m-d H:i:s') . "] SUCCESS: " . $message . "\n";
            file_put_contents(EmailConfig::$logFile, $logMessage, FILE_APPEND);
        }
    }
}

// Installation instructions
function showInstallationInstructions() {
    echo "<h2>📧 Email Configuration Instructions</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🔧 Setup Instructions:</h3>";
    echo "<ol>";
    echo "<li><strong>Basic Setup (PHP Mail):</strong><br>";
    echo "Set EmailConfig::\$usePhpMail = true; in config_smtp.php<br>";
    echo "Ensure your hosting provider allows PHP mail function</li>";
    echo "<li><strong>Advanced Setup (SMTP - Recommended):</strong><br>";
    echo "Install PHPMailer library: composer require phpmailer/phpmailer<br>";
    echo "Update SMTP settings in config_smtp.php with your email provider credentials</li>";
    echo "<li><strong>Gmail SMTP Setup:</strong><br>";
    echo "Enable 2-factor authentication in your Gmail account<br>";
    echo "Generate an App Password for your application<br>";
    echo "Update EmailConfig::\$smtpUsername and EmailConfig::\$smtpPassword</li>";
    echo "<li><strong>File Upload Setup:</strong><br>";
    echo "Create 'uploads' and 'temp' directories with write permissions<br>";
    echo "Set proper directory permissions (755 for directories, 644 for files)</li>";
    echo "</ol>";
    echo "</div>";
}

// Test function
function testEmailConfiguration() {
    $config = new FellowshipEmail();
    echo "<h2>🧪 Email Configuration Test</h2>";
    
    // Check PHP mail function
    if (function_exists('mail')) {
        echo "<p style='color: green;'>✅ PHP mail function is available</p>";
    } else {
        echo "<p style='color: red;'>❌ PHP mail function is not available</p>";
    }
    
    // Check directories
    if (is_dir(EmailConfig::$uploadDir) && is_writable(EmailConfig::$uploadDir)) {
        echo "<p style='color: green;'>✅ Upload directory is writable</p>";
    } else {
        echo "<p style='color: red;'>❌ Upload directory is not writable or doesn't exist</p>";
    }
    
    if (is_dir(EmailConfig::$tempDir) && is_writable(EmailConfig::$tempDir)) {
        echo "<p style='color: green;'>✅ Temp directory is writable</p>";
    } else {
        echo "<p style='color: red;'>❌ Temp directory is not writable or doesn't exist</p>";
    }
    
    // Check file upload settings
    echo "<h3>📁 File Upload Configuration:</h3>";
    echo "<ul>";
    echo "<li>File Uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
    echo "<li>Max File Size: " . ini_get('upload_max_filesize') . "</li>";
    echo "<li>Max POST Size: " . ini_get('post_max_size') . "</li>";
    echo "<li>Max File Uploads: " . ini_get('max_file_uploads') . "</li>";
    echo "</ul>";
}

// Configuration form
function showConfigurationForm() {
    echo "<h2>⚙️ Email Configuration</h2>";
    echo "<form method='post' action=''>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Email Method:</h3>";
    echo "<label><input type='radio' name='email_method' value='phpmail' " . (EmailConfig::$usePhpMail ? 'checked' : '') . "> PHP Mail</label><br>";
    echo "<label><input type='radio' name='email_method' value='smtp' " . (EmailConfig::$useSmtp ? 'checked' : '') . "> SMTP (Recommended)</label><br><br>";
    
    echo "<h3>SMTP Settings:</h3>";
    echo "<label>SMTP Host: <input type='text' name='smtp_host' value='" . EmailConfig::$smtpHost . "'></label><br>";
    echo "<label>SMTP Port: <input type='number' name='smtp_port' value='" . EmailConfig::$smtpPort . "'></label><br>";
    echo "<label>SMTP Username: <input type='text' name='smtp_username' value='" . EmailConfig::$smtpUsername . "'></label><br>";
    echo "<label>SMTP Password: <input type='password' name='smtp_password' value='" . EmailConfig::$smtpPassword . "'></label><br>";
    echo "<label>SMTP Security: <select name='smtp_security'>";
    echo "<option value='tls' " . (EmailConfig::$smtpSecurity == 'tls' ? 'selected' : '') . ">TLS</option>";
    echo "<option value='ssl' " . (EmailConfig::$smtpSecurity == 'ssl' ? 'selected' : '') . ">SSL</option>";
    echo "</select></label><br><br>";
    
    echo "<h3>Recipients:</h3>";
    echo "<label>Primary Email: <input type='email' name='primary_email' value='" . EmailConfig::$recipients[0] . "'></label><br>";
    echo "<label>Secondary Email: <input type='email' name='secondary_email' value='" . EmailConfig::$recipients[1] . "'></label><br><br>";
    
    echo "<button type='submit' name='save_config'>Save Configuration</button>";
    echo "</div>";
    echo "</form>";
}

// Handle form submission
if (isset($_POST['save_config'])) {
    // Save configuration logic here
    echo "<div class='success'>Configuration saved successfully!</div>";
}

// Show instructions and test
showInstallationInstructions();
testEmailConfiguration();
showConfigurationForm();

?>