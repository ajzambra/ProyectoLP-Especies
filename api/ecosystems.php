<?php
require_once __DIR__ . '/../app/controllers/EcosystemController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  EcosystemController::store();
} else {
  http_response_code(405);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['error' => 'Método no permitido. Usa POST.']);
}
