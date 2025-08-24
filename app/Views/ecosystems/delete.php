<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../models/Ecosystem.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = (int)$_POST['id'];
        Ecosystem::delete($id);
        $_SESSION['success'] = "Ecosistema eliminado correctamente";
    } catch (Throwable $e) {
        $_SESSION['errores'] = ['Error al eliminar: ' . $e->getMessage()];
    }
}

header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
exit;