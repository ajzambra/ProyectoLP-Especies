<?php
require_once __DIR__ . '/../app/controllers/EcosystemController.php';
require_once __DIR__ . '/../app/models/Ecosystem.php';

$method = $_SERVER['REQUEST_METHOD'];
$json = $_GET['json'] ?? 0; // si viene ?json=1 devuelve JSON

function jsonError($msg, $code=400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error'=>$msg]);
    exit;
}

if($method === 'POST'){

    if(isset($_GET['action']) && $_GET['action']==='update' && isset($_GET['id'])){
        $id = (int)$_GET['id'];
        if($json){
            EcosystemController::updateAPI($id, $_POST);
        } else {
            // HTML form: hacer redirección
            EcosystemController::update($id); // esta función hace header('Location: show-ecosystems')
        }
        exit;
    }


    if(isset($_GET['action']) && $_GET['action']==='delete' && isset($_POST['id'])){
        $id = (int)$_POST['id'];
        if($json){
            EcosystemController::deleteAPI($id);
        } else {
            EcosystemController::delete($id); // función que hace header('Location: show-ecosystems')
        }
        exit;
    }


    EcosystemController::store();
    exit;
}

if($method === 'GET'){
    if($json){
        header('Content-Type: application/json; charset=utf-8');
        $filters = [
            'clasificacion'=>$_GET['clasificacion']??'',
            'nombre'=>$_GET['nombre']??'',
            'lugar'=>$_GET['lugar']??''
        ];
        $ecosistemas = Ecosystem::getFiltered($filters)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ecosistemas);
        exit;
    }

    if(isset($_GET['action']) && $_GET['action']==='edit' && isset($_GET['id'])){
        $id = (int)$_GET['id'];
        EcosystemController::edit($id); // carga edit.php
        exit;
    }

    EcosystemController::index(); // lista ecosistemas
    exit;
}

jsonError("Método no permitido",405);
