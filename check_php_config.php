<?php
header('Content-Type: application/json');

// Check PHP mail function availability
$mailEnabled = function_exists('mail');

// Check PHP version
$phpVersion = PHP_VERSION;

// Check server information
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$serverName = $_SERVER['SERVER_NAME'] ?? 'Unknown';

// Check for common SMTP configuration
$smtpConfig = [
    'sendmail_path' => ini_get('sendmail_path'),
    'smtp' => ini_get('SMTP'),
    'smtp_port' => ini_get('smtp_port'),
    'sendmail_from' => ini_get('sendmail_from'),
    'sendmail_path' => ini_get('sendmail_path'),
];

// Check file upload configuration
$fileUploadConfig = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
];

// Check if required directories exist and are writable
$directories = [
    'uploads' => is_dir('uploads') && is_writable('uploads'),
    'temp' => is_dir('temp') && is_writable('temp'),
];

// Email test result
$emailTestResult = 'not_tested';

// Try to send a test email if mail function is available
if ($mailEnabled) {
    $testSubject = 'Mann Scanning Centre - Email Test';
    $testMessage = 'This is a test email to verify that the PHP mail function is working correctly.';
    $testHeaders = 'From: test@mannscan.in' . "\r\n" .
                   'Reply-To: test@mannscan.in' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
    
    // Try to send test email
    $emailTestResult = @mail('test@example.com', $testSubject, $testMessage, $testHeaders) ? 'success' : 'failed';
}

// Prepare response
$response = [
    'mail_enabled' => $mailEnabled,
    'php_version' => $phpVersion,
    'server_software' => $serverSoftware,
    'server_name' => $serverName,
    'smtp_config' => $smtpConfig,
    'file_upload_config' => $fileUploadConfig,
    'directories' => $directories,
    'email_test_result' => $emailTestResult,
    'timestamp' => date('Y-m-d H:i:s'),
    'recommendations' => []
];

// Generate recommendations
if (!$mailEnabled) {
    $response['recommendations'][] = 'PHP mail function is not available. You may need to configure SMTP settings or use an alternative email method.';
}

if ($emailTestResult === 'failed') {
    $response['recommendations'][] = 'Email sending failed. Check your SMTP configuration or contact your hosting provider.';
}

if (!$directories['uploads']) {
    $response['recommendations'][] = 'Uploads directory does not exist or is not writable. Please create it with proper permissions.';
}

if (!$directories['temp']) {
    $response['recommendations'][] = 'Temp directory does not exist or is not writable. Please create it with proper permissions.';
}

if (ini_get('file_uploads') == '0') {
    $response['recommendations'][] = 'File uploads are disabled in PHP configuration. Enable file_uploads in php.ini.';
}

if (ini_get('upload_max_filesize') < '10M') {
    $response['recommendations'][] = 'Upload file size limit is too small. Set upload_max_filesize to at least 10M in php.ini.';
}

if (ini_get('post_max_size') < '10M') {
    $response['recommendations'][] = 'POST size limit is too small. Set post_max_size to at least 10M in php.ini.';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>