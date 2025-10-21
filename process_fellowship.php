<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Enable verbose errors during setup (disable on production hosting)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$siteName = 'Mann Scanning Centre';
$siteEmail = 'fellowship@mannscan.in';
$siteUrl = 'https://mannscan.in';
$adminRecipients = ['drhsmann@gmail.com'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requiredFields = [
            'firstName', 'lastName', 'email', 'phone',
            'degree', 'specialization', 'experience',
            'programType', 'motivation', 'expectations', 'terms'
        ];

        $missing = [];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception('Please complete all required fields.');
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }

        $phone = preg_replace('/[^\d+]/', '', trim($_POST['phone']));
        if (!preg_match('/^\+?\d{10,15}$/', $phone)) {
            throw new Exception('Please enter a valid phone number (10-15 digits).');
        }

        $experience = (int)$_POST['experience'];
        if ($experience < 0 || $experience > 50) {
            throw new Exception('Please enter a valid number of years of experience.');
        }

        if (!isset($_POST['terms'])) {
            throw new Exception('Please agree to the terms and conditions.');
        }

        $formData = [
            'firstName' => sanitizeText($_POST['firstName']),
            'lastName' => sanitizeText($_POST['lastName']),
            'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'phone' => $phone,
            'degree' => sanitizeText($_POST['degree']),
            'specialization' => sanitizeText($_POST['specialization']),
            'experience' => $experience,
            'institution' => sanitizeText($_POST['institution'] ?? ''),
            'programType' => sanitizeText($_POST['programType']),
            'startDate' => sanitizeText($_POST['startDate'] ?? ''),
            'motivation' => sanitizeText($_POST['motivation']),
            'expectations' => sanitizeText($_POST['expectations']),
            'applicationDate' => date('Y-m-d H:i:s')
        ];

        $applicationId = buildApplicationId();

        $adminSubject = "Fellowship Application - {$formData['firstName']} {$formData['lastName']} ($applicationId)";
        $adminMessage = buildAdminEmailMessage($formData, $applicationId, $siteName);
        $adminHeaders = buildEmailHeaders($siteEmail, $siteName, $formData['email']);

        $adminSent = true;
        foreach ($adminRecipients as $recipient) {
            if (!mail($recipient, $adminSubject, $adminMessage, $adminHeaders)) {
                $adminSent = false;
            }
        }

        if (!$adminSent) {
            throw new Exception('There was an issue sending your application. Please email your details to drhsmann@gmail.com.');
        }

        $applicantSubject = "Thank you for your fellowship application ($applicationId)";
        $applicantMessage = buildApplicantEmailMessage($formData, $applicationId, $siteName, $siteUrl);
        $applicantHeaders = buildEmailHeaders($siteEmail, $siteName);
        $applicantSent = mail($formData['email'], $applicantSubject, $applicantMessage, $applicantHeaders);

        $response = [
            'success' => true,
            'message' => 'Thank you for your application! Our team will contact you soon.',
            'applicationId' => $applicationId,
            'confirmationSent' => $applicantSent
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method.'
    ];
}

echo json_encode($response);

function sanitizeText($value) {
    return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
}

function buildApplicationId() {
    try {
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    } catch (Exception $e) {
        $random = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));
    }
    return 'FEL-' . date('Ymd') . '-' . $random;
}

function buildEmailHeaders($fromEmail, $fromName, $replyTo = null) {
    $headers = "From: $fromName <$fromEmail>\r\n";
    if (!empty($replyTo)) {
        $headers .= "Reply-To: $replyTo\r\n";
    }
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return $headers;
}

function buildAdminEmailMessage($data, $applicationId, $siteName) {
    $message = "New Fellowship Application Received\n";
    $message .= "==================================\n\n";
    $message .= "Application ID: $applicationId\n";
    $message .= "Date: {$data['applicationDate']}\n\n";
    $message .= "Applicant Information:\n";
    $message .= "----------------------\n";
    $message .= "Name: {$data['firstName']} {$data['lastName']}\n";
    $message .= "Email: {$data['email']}\n";
    $message .= "Phone: {$data['phone']}\n";
    $message .= "Degree: {$data['degree']}\n";
    $message .= "Specialization: {$data['specialization']}\n";
    $message .= "Years of Experience: {$data['experience']}\n";
    $message .= "Current Institution: " . ($data['institution'] ?: 'Not provided') . "\n\n";
    $message .= "Program Preferences:\n";
    $message .= "--------------------\n";
    $message .= "Program Type: {$data['programType']}\n";
    $message .= "Preferred Start Date: " . ($data['startDate'] ?: 'Not provided') . "\n\n";
    $message .= "Applicant Responses:\n";
    $message .= "--------------------\n";
    $message .= "Motivation:\n{$data['motivation']}\n\n";
    $message .= "Expectations:\n{$data['expectations']}\n\n";
    $message .= "This application was submitted via the $siteName website.\n";
    $message .= "If any detail looks incomplete, please follow up with the applicant directly.\n";
    return $message;
}

function buildApplicantEmailMessage($data, $applicationId, $siteName, $siteUrl) {
    $message = "Thank You for Your Fellowship Application\n";
    $message .= "========================================\n\n";
    $message .= "Dear {$data['firstName']},\n\n";
    $message .= "Thank you for applying to the Ultrasound Fellowship Program at $siteName.\n";
    $message .= "We have received your application and assigned it the ID $applicationId.\n\n";
    $message .= "Application Summary:\n";
    $message .= "--------------------\n";
    $message .= "Program Type: {$data['programType']}\n";
    $message .= "Specialization: {$data['specialization']}\n";
    $message .= "Experience: {$data['experience']} years\n";
    $message .= "Preference: " . ($data['startDate'] ?: 'Not provided') . "\n\n";
    $message .= "Our team will review your submission and respond within the next few business days.\n";
    $message .= "If you need to share supporting documents, please reply to this email or write to drhsmann@gmail.com.\n\n";
    $message .= "Website: $siteUrl\n";
    $message .= "Contact: drhsmann@gmail.com | +91-181-4695500\n\n";
    $message .= "Warm regards,\n";
    $message .= "The Fellowship Team\n";
    $message .= "$siteName\n";
    return $message;
}
?>
