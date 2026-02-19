<?php
// Fonction pour envoyer un message à Telegram
function sendTelegramMessage($message) {
    // Chemin du fichier de configuration Telegram
    $telegramConfigFile = 'telegram_config.json';
    
    if (!file_exists($telegramConfigFile)) {
        return false;
    }
    
    $telegramConfig = json_decode(file_get_contents($telegramConfigFile), true);
    $botToken = $telegramConfig['bot_token'] ?? '';
    $chatId = $telegramConfig['chat_id'] ?? '';
    
    if (empty($botToken) || empty($chatId)) {
        return false;
    }
    
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
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
 
    
    return $info['http_code'] == 200;
}

// Récupérer les données JSON envoyées
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Si les données sont envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        $result = sendTelegramMessage($message);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    } elseif ($data && isset($data['message'])) {
        $message = $data['message'];
        $result = sendTelegramMessage($message);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }
}

// Si aucune donnée valide n'est reçue
header('Content-Type: application/json');
echo json_encode(['success' => false, 'error' => 'Aucun message fourni']);