<?php
require_once __DIR__ . '/../app/controllers/EcosystemController.php';
require_once __DIR__ . '/../app/models/Ecosystem.php';

$method = $_SERVER['REQUEST_METHOD'];
$json = isset($_GET['json']) && $_GET['json'] == 1;

function jsonError($msg, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $msg]);
    exit;
}

if ($method === 'POST') {
    if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id'])) {
        EcosystemController::update((int)$_GET['id']);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_POST['id'])) {
        EcosystemController::delete((int)$_POST['id']);
    } else {
        EcosystemController::store();
    }
} elseif ($method === 'GET') {
    if ($json) {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [
            'clasificacion' => $_GET['clasificacion'] ?? '',
            'nombre' => $_GET['nombre'] ?? '',
            'lugar' => $_GET['lugar'] ?? ''
        ];

        $ecosistemas = Ecosystem::getFiltered($filters)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ecosistemas);

    } else {
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            EcosystemController::edit((int)$_GET['id']);
        } else {
            EcosystemController::index();   
        }
    }
} else {
    jsonError('Método no permitido. Usa POST o GET.', 405);
}