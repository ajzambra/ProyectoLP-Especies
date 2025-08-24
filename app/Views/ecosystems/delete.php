<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/app.php';  
require_once __DIR__ . '/../../Models/Ecosystem.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = (int)$_POST['id'];
        
        $db = DB::conn();
        $query = "DELETE FROM ecosistemas WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Ecosistema eliminado correctamente";
        } else {
            $_SESSION['errores'] = ['El ecosistema no existe o ya fue eliminado'];
        }
        
    } catch (Throwable $e) {
        $_SESSION['errores'] = ['Error al eliminar: ' . $e->getMessage()];
    }
}

header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
exit;