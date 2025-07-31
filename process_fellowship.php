<?php
// Set headers for CORS and content type
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$adminEmail1 = 'harkaran@msn.com';
$adminEmail2 = 'harkaran@mannscan.in';
$siteName = 'Mann Scanning Centre';
$siteUrl = 'https://mannscan.in';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $requiredFields = [
            'firstName', 'lastName', 'email', 'phone', 'degree', 
            'specialization', 'experience', 'programType', 'motivation', 'expectations', 'terms'
        ];
        
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = $field . ' is required';
            }
        }
        
        if (!empty($errors)) {
            throw new Exception('Please fill in all required fields: ' . implode(', ', $errors));
        }
        
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address');
        }
        
        // Validate phone number (basic validation)
        if (!preg_match('/^[0-9]{10,15}$/', $_POST['phone'])) {
            throw new Exception('Please enter a valid phone number (10-15 digits)');
        }
        
        // Validate experience
        $experience = (int)$_POST['experience'];
        if ($experience < 0 || $experience > 50) {
            throw new Exception('Please enter a valid number of years of experience');
        }
        
        // Handle file uploads
        $cvFile = null;
        $certificateFiles = [];
        
        // Process CV upload
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cvFile = $_FILES['cv'];
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (!in_array($cvFile['type'], $allowedTypes)) {
                throw new Exception('CV must be a PDF, DOC, or DOCX file');
            }
            
            if ($cvFile['size'] > 5 * 1024 * 1024) { // 5MB limit
                throw new Exception('CV file size must be less than 5MB');
            }
        }
        
        // Process certificate uploads
        if (isset($_FILES['certificates']['name']) && is_array($_FILES['certificates']['name'])) {
            $fileCount = count($_FILES['certificates']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['certificates']['error'][$i] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    
                    if (in_array($_FILES['certificates']['type'][$i], $allowedTypes) && 
                        $_FILES['certificates']['size'][$i] <= 5 * 1024 * 1024) {
                        $certificateFiles[] = [
                            'name' => $_FILES['certificates']['name'][$i],
                            'type' => $_FILES['certificates']['type'][$i],
                            'tmp_name' => $_FILES['certificates']['tmp_name'][$i],
                            'size' => $_FILES['certificates']['size'][$i]
                        ];
                    }
                }
            }
        }
        
        // Sanitize input data
        $formData = [
            'firstName' => htmlspecialchars(trim($_POST['firstName']), ENT_QUOTES, 'UTF-8'),
            'lastName' => htmlspecialchars(trim($_POST['lastName']), ENT_QUOTES, 'UTF-8'),
            'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'phone' => preg_replace('/[^\d]/', '', trim($_POST['phone'])),
            'degree' => htmlspecialchars(trim($_POST['degree']), ENT_QUOTES, 'UTF-8'),
            'specialization' => htmlspecialchars(trim($_POST['specialization']), ENT_QUOTES, 'UTF-8'),
            'experience' => (int)$_POST['experience'],
            'institution' => htmlspecialchars(trim($_POST['institution']), ENT_QUOTES, 'UTF-8'),
            'programType' => htmlspecialchars(trim($_POST['programType']), ENT_QUOTES, 'UTF-8'),
            'startDate' => htmlspecialchars(trim($_POST['startDate']), ENT_QUOTES, 'UTF-8'),
            'motivation' => htmlspecialchars(trim($_POST['motivation']), ENT_QUOTES, 'UTF-8'),
            'expectations' => htmlspecialchars(trim($_POST['expectations']), ENT_QUOTES, 'UTF-8'),
            'applicationDate' => date('Y-m-d H:i:s')
        ];
        
        // Generate application ID
        $applicationId = 'FELLOWSHIP-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Send email to administrators
        $adminSubject = "New Fellowship Application - $applicationId";
        $adminMessage = buildAdminEmailMessage($formData, $applicationId, $siteName);
        
        // Send to both admin emails
        $adminHeaders = buildEmailHeaders($formData['email'], "Fellowship Application <$siteEmail>");
        
        $adminSent1 = sendEmailWithAttachments($adminEmail1, $adminSubject, $adminMessage, $adminHeaders, $cvFile, $certificateFiles);
        $adminSent2 = sendEmailWithAttachments($adminEmail2, $adminSubject, $adminMessage, $adminHeaders, $cvFile, $certificateFiles);
        
        // Send confirmation email to applicant
        $applicantSubject = "Thank You for Your Fellowship Application - $applicationId";
        $applicantMessage = buildApplicantEmailMessage($formData, $applicationId, $siteName, $siteUrl);
        
        $applicantHeaders = buildEmailHeaders($siteEmail, "$siteName <$siteEmail>");
        $applicantSent = sendEmailWithAttachments($formData['email'], $applicantSubject, $applicantMessage, $applicantHeaders);
        
        // Check if emails were sent successfully
        if ($adminSent1 && $adminSent2 && $applicantSent) {
            // Success response
            $response = [
                'success' => true,
                'message' => 'Thank you for your application! We have received your submission and will contact you soon.',
                'applicationId' => $applicationId
            ];
        } else {
            throw new Exception('There was an error sending your application. Please try again later.');
        }
        
    } catch (Exception $e) {
        // Error response
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
} else {
    // Invalid request method
    $response = [
        'success' => false,
        'message' => 'Invalid request method'
    ];
}

// Return JSON response
echo json_encode($response);

// Helper functions
function buildEmailHeaders($fromEmail, $fromName) {
    return "From: " . $fromName . "\r\n" .
           "Reply-To: " . $fromEmail . "\r\n" .
           "X-Mailer: PHP/" . phpversion() . "\r\n" .
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/plain; charset=UTF-8\r\n";
}

function buildAdminEmailMessage($data, $applicationId, $siteName) {
    $message = "New Fellowship Application Received\n";
    $message .= "==================================\n\n";
    
    $message .= "Application ID: $applicationId\n";
    $message .= "Date: " . $data['applicationDate'] . "\n\n";
    
    $message .= "Applicant Information:\n";
    $message .= "----------------------\n";
    $message .= "Name: " . $data['firstName'] . ' ' . $data['lastName'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . $data['phone'] . "\n";
    $message .= "Degree: " . $data['degree'] . "\n";
    $message .= "Specialization: " . $data['specialization'] . "\n";
    $message .= "Years of Experience: " . $data['experience'] . "\n";
    $message .= "Current Institution: " . ($data['institution'] ?: 'Not specified') . "\n\n";
    
    $message .= "Program Details:\n";
    $message .= "----------------\n";
    $message .= "Program Type: " . $data['programType'] . "\n";
    $message .= "Preferred Start Date: " . ($data['startDate'] ?: 'Not specified') . "\n\n";
    
    $message .= "Application Content:\n";
    $message .= "--------------------\n";
    $message .= "Motivation:\n" . $data['motivation'] . "\n\n";
    $message .= "Expectations:\n" . $data['expectations'] . "\n\n";
    
    $message .= "This application was submitted through the $siteName website.\n";
    $message .= "Please review the application and contact the applicant for further details.\n";
    
    return $message;
}

function buildApplicantEmailMessage($data, $applicationId, $siteName, $siteUrl) {
    $message = "Thank You for Your Fellowship Application\n";
    $message .= "========================================\n\n";
    
    $message .= "Dear " . $data['firstName'] . ",\n\n";
    
    $message .= "Thank you for your interest in the Ultrasound Fellowship Program at $siteName. ";
    $message .= "We have received your application (ID: $applicationId) and appreciate your interest in our program.\n\n";
    
    $message .= "Application Details:\n";
    $message .= "--------------------\n";
    $message .= "Application ID: $applicationId\n";
    $message .= "Program Type: " . $data['programType'] . "\n";
    $message .= "Specialization: " . $data['specialization'] . "\n";
    $message .= "Application Date: " . $data['applicationDate'] . "\n\n";
    
    $message .= "Next Steps:\n";
    $message .= "-----------\n";
    $message .= "1. Our admissions committee will review your application within 1-2 business days.\n";
    $message .= "2. If your application meets our requirements, we will contact you for an interview.\n";
    $message .= "3. The interview may be conducted in person or via video call.\n";
    $message .= "4. Selected candidates will receive an offer letter with program details.\n\n";
    
    $message .= "Contact Information:\n";
    $message .= "--------------------\n";
    $message .= "Email: drhsmann@gmail.com\n";
    $message .= "Phone: +91-181-4695500\n";
    $message .= "Website: $siteUrl\n\n";
    
    $message .= "We appreciate your interest in our fellowship program and will be in touch soon.\n\n";
    
    $message .= "Best regards,\n";
    $message .= "The Fellowship Team\n";
    $siteName . "\n";
    
    return $message;
}

function sendEmailWithAttachments($to, $subject, $message, $headers, $cvFile = null, $certificateFiles = []) {
    global $siteEmail;
    
    // If there are attachments, create MIME message
    if ($cvFile || !empty($certificateFiles)) {
        // Create boundary
        $boundary = "==Multipart_Boundary_x" . md5(time()) . "x";
        
        // Headers for multipart message
        $multipartHeaders = $headers . 
            "MIME-Version: 1.0\r\n" .
            "Content-Type: multipart/mixed;\r\n" .
            " boundary=\"$boundary\"";
        
        // Message body
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $message . "\r\n";
        
        // Attach CV file
        if ($cvFile) {
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: " . $cvFile['type'] . "; name=\"" . $cvFile['name'] . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . $cvFile['name'] . "\"\r\n\r\n";
            $body .= chunk_split(base64_encode(file_get_contents($cvFile['tmp_name']))) . "\r\n";
        }
        
        // Attach certificate files
        foreach ($certificateFiles as $certFile) {
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: " . $certFile['type'] . "; name=\"" . $certFile['name'] . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . $certFile['name'] . "\"\r\n\r\n";
            $body .= chunk_split(base64_encode(file_get_contents($certFile['tmp_name']))) . "\r\n";
        }
        
        $body .= "--$boundary--\r\n";
        
        return mail($to, $subject, $body, $multipartHeaders);
    } else {
        // Simple email without attachments
        return mail($to, $subject, $message, $headers);
    }
}
?>