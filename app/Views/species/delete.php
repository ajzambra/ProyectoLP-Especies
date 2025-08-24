<?php
require_once __DIR__ . '/../../../config/database.php'; 
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../Models/Species.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_especie'])) {
    $db = DB::conn();
    $speciesModel = new Species($db);
    
    try {
        $id = (int)$_POST['id_especie'];
        if ($speciesModel->delete($id)) {
            $_SESSION['success'] = "Especie eliminada correctamente";
        } else {
            $_SESSION['errores'] = ['Error al eliminar la especie'];
        }
    } catch (Exception $e) {
        $_SESSION['errores'] = ['Error: ' . $e->getMessage()];
    }
}

header('Location: ' . BASE_URL . '/app/Views/species/index.php');
exit;