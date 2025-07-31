<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Mann Scanning Centre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .test-form {
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Functionality Test</h1>
        
        <div class="info">
            <h3>🔍 Testing Email Configuration</h3>
            <p>This page helps you test if the fellowship application email functionality is working properly. Fill out the form below and submit to test email delivery.</p>
        </div>

        <div class="test-form">
            <form method="post" action="process_fellowship.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="testName">Full Name:</label>
                    <input type="text" id="testName" name="full_name" value="Test Applicant" required>
                </div>
                
                <div class="form-group">
                    <label for="testEmail">Email:</label>
                    <input type="email" id="testEmail" name="email" value="test@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="testPhone">Phone:</label>
                    <input type="tel" id="testPhone" name="phone" value="1234567890" required>
                </div>
                
                <div class="form-group">
                    <label for="testEducation">Education:</label>
                    <select id="testEducation" name="education" required>
                        <option value="MBBS">MBBS</option>
                        <option value="MD Radiology">MD Radiology</option>
                        <option value="MS Radiology">MS Radiology</option>
                        <option value="DNB Radiology">DNB Radiology</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="testExperience">Years of Experience:</label>
                    <input type="number" id="testExperience" name="experience" value="2" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="testMessage">Test Message:</label>
                    <textarea id="testMessage" name="message" rows="4" required>This is a test submission to verify email functionality.</textarea>
                </div>
                
                <div class="form-group">
                    <label for="testCV">Upload Test CV (Optional):</label>
                    <input type="file" id="testCV" name="cv" accept=".pdf,.doc,.docx">
                </div>
                
                <div class="form-group">
                    <label for="testCertificate">Upload Test Certificate (Optional):</label>
                    <input type="file" id="testCertificate" name="certificate" accept=".pdf,.doc,.docx">
                </div>
                
                <button type="submit">🚀 Test Email Submission</button>
            </form>
        </div>

        <div id="result" class="result"></div>
    </div>

    <script>
        // Show result message
        document.querySelector('form').addEventListener('submit', function(e) {
            const result = document.getElementById('result');
            result.style.display = 'block';
            result.className = 'info';
            result.innerHTML = '⏳ Processing your test submission... Please wait.';
        });

        // Check PHP mail function availability
        window.addEventListener('load', function() {
            fetch('check_php_config.php')
                .then(response => response.json())
                .then(data => {
                    const result = document.getElementById('result');
                    if (data.mail_enabled) {
                        result.style.display = 'block';
                        result.className = 'success';
                        result.innerHTML = '✅ PHP mail function is available and configured.';
                    } else {
                        result.style.display = 'block';
                        result.className = 'error';
                        result.innerHTML = '❌ PHP mail function is not available or not configured. You may need to configure SMTP settings in your PHP configuration.';
                    }
                })
                .catch(error => {
                    console.log('Could not check PHP configuration:', error);
                });
        });
    </script>
</body>
</html>