<?php
/**
 * REST API Endpoint to save customization payload
 */
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $item = json_decode($raw, true);

    if ($item) {
        $customizations = load_json('customizations.json');
        $item['saved_on_server_at'] = date('Y-m-d H:i:s');
        $customizations[] = $item;
        save_json('customizations.json', $customizations);

        echo json_encode([
            'success' => true,
            'message' => 'Customization successfully saved in PHP backend!',
            'itemId' => $item['id'] ?? null,
            'total' => count($customizations)
        ]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON payload']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $customizations = load_json('customizations.json');
    echo json_encode($customizations);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
