<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/ProyectoLP-Especies/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once __DIR__ . '/../app/controllers/SpeciesController.php';

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isJsonRequest = strpos($contentType, 'application/json') !== false;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit;
}

if ($isJsonRequest) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

$method = $_SERVER['REQUEST_METHOD'];

if ($isJsonRequest) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $controller = new SpeciesController();
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'get':
                        if (isset($_GET['id'])) {
                            $controller->getAPI((int)$_GET['id']);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'ID no proporcionado']);
                        }
                        break;
                    case 'list':
                        $controller->listAPI();
                        break;
                    default:
                        http_response_code(400);
                        echo json_encode(['error' => 'Acción no válida']);
                }
            }
            break;
            
        case 'POST':
            if (isset($input['nombre_comun'])) {
                $controller->createAPI($input);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Datos incompletos']);
            }
            break;
            
        case 'PUT':
            if (isset($input['id_especie'])) {
                $controller->updateAPI((int)$input['id_especie'], $input);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
            
        case 'DELETE':
            if (isset($input['id_especie'])) {
                $controller->deleteAPI((int)$input['id_especie']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
    exit;
}

if (!$isJsonRequest) {
    $controller = new SpeciesController();
    
    if ($method === 'POST') {
        if (isset($_GET['action']) && $_GET['action'] === 'store') {
            $controller->store();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id'])) {
            $controller->update((int)$_GET['id']);
        } else {
            $controller->store();
        }
        exit;
    } elseif ($method === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $controller->edit((int)$_GET['id']);
            exit;
        } elseif (isset($_GET['action']) && $_GET['action'] === 'list') {
            $controller->index();
            exit;
        }
    }
}

http_response_code(405);
echo "Método no permitido";