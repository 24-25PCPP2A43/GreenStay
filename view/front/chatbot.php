<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

// Vérification de la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['error' => 'Message vide']);
    exit;
}

// Configuration OpenAI - À METTRE DANS VOTRE CONFIGURATION
$api_key = 'sk-votre-cle-api-securisee'; // NE LAISSEZ PAS LA CLÉ EN DUR DANS LE CODE
$api_url = 'https://api.openai.com/v1/chat/completions';

// Contexte spécialisé pour les réclamations écologiques
$context = "Tu es un assistant expert en réclamations pour un site de location de logements écologiques. 
Voici des informations importantes :
- Les réclamations doivent être traitées sous 48h
- Les clients peuvent joindre des photos/vidéos
- Les problèmes courants concernent l'isolation, le chauffage, les matériaux
- Réponds de manière concise, professionnelle et empathique
- Dirige vers le formulaire si nécessaire
- Pour les questions techniques, propose de contacter le support";

// Préparation des messages pour l'API
$messages = [
    ['role' => 'system', 'content' => $context],
    ['role' => 'user', 'content' => $user_message]
];

try {
    // Appel à l'API OpenAI
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 150
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('Erreur de connexion à OpenAI: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    
    if ($http_code !== 200 || !isset($data['choices'][0]['message']['content'])) {
        throw new Exception('Réponse invalide de l\'API OpenAI');
    }

    // Enregistrement dans la base de données
    try {
        $stmt = $conn->prepare("INSERT INTO chat_log (message, response) VALUES (?, ?)");
        $stmt->execute([$user_message, $data['choices'][0]['message']['content']]);
    } catch (PDOException $e) {
        error_log("Erreur d'enregistrement en base: " . $e->getMessage());
    }

    // Retour de la réponse
    echo json_encode([
        'response' => $data['choices'][0]['message']['content']
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'error' => "Désolé, je n'ai pas pu traiter votre demande.",
        'details' => $e->getMessage() // À retirer en production
    ]);
}
?>