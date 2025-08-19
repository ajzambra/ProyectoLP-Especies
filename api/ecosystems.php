<?php
require_once __DIR__ . '/../app/controllers/EcosystemController.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
  if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id'])) {
    EcosystemController::update((int)$_GET['id']);
  } else {
    EcosystemController::store();
  }
if ($method === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
            EcosystemController::edit((int)$_GET['id']);
        } elseif ($_GET['action'] === 'check' && isset($_GET['id'])) {
            EcosystemController::check((int)$_GET['id']);
        } else {
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Método GET no permitido.']);
        }
    }
}


} else {
  http_response_code(405);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['error' => 'Método no permitido. Usa POST o GET.']);
}

