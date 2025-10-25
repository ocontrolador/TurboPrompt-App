<?php
// Proxy simples pra chamar API do Grok sem expor key no client

header('Content-Type: application/json');

// Pega dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$prompt = $data['prompt'] ?? '';
$apiKey = $data['apiKey'] ?? '';

if (empty($prompt) || empty($apiKey)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros faltando']);
    exit;
}

// Config da API
$url = 'https://api.x.ai/v1/chat/completions';
$body = json_encode([
    'model' => 'grok-4-fast-reasoning',
    'messages' => [['role' => 'user', 'content' => $prompt]],
    'temperature' => 0.7,
    'max_tokens' => 512
]);

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
        'method' => 'POST',
        'content' => $body,
        'ignore_errors' => true // Pra erro handling
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha na chamada à API']);
    exit;
}

$jsonResponse = json_decode($response, true);
$sugestao = $jsonResponse['choices'][0]['message']['content'] ?? 'Sem resposta';

// Parseia a sugestão em array (assume que Grok retorna texto com 3 versões)
$sugestoes = explode("\n\n", trim($sugestao)); // Ajuste se formato variar

echo json_encode(['sugestoes' => $sugestoes]);
?>
