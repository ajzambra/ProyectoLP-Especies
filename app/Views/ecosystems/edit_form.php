<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../models/Ecosystem.php';

if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
    exit;
}

$id = (int)$_GET['id'];
$ecosistema = Ecosystem::find($id);

if (!$ecosistema) {
    $_SESSION['errores'] = ["Ecosistema con ID $id no encontrado."];
    header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
    exit;
}

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Editar Ecosistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 20px;
}
.container {
    max-width: 700px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
}
.form-label {
    font-weight: bold;
    color: #34495e;
    margin-top: 15px;
    display: block;
}
.form-control {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}
textarea {
    resize: vertical;
}
.current-img {
    margin-top: 10px;
    border: 1px solid #ccc;
    padding: 5px;
    max-width: 300px;
    display: block;
    border-radius: 5px;
}
.alert {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.alert-success {
    background: #e0f8e9;
    color: #27ae60;
}
.alert-danger {
    background: #ffe6e6;
    color: #c0392b;
}
.btn-primary {
    margin-top: 20px;
    padding: 12px 25px;
    border: none;
    background: #27ae60;
    color: #fff;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
.btn-primary:hover {
    background: #219150;
}
.back-link {
    display: block;
    margin-top: 20px;
    text-align: center;
    text-decoration: none;
    color: #2980b9;
}
.back-link:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
<h1>Editar Ecosistema</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
    <ul>
        <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/api/ecosystems.php?action=update&id=<?= $ecosistema['id'] ?>" enctype="multipart/form-data">
    <div class="mb-3">
    <label class="form-label">Nombre:</label>
    <input type="text" class="form-control" name="nombre" maxlength="80" required value="<?= htmlspecialchars($ecosistema['nombre']) ?>">
    </div>
    
    <div class="mb-3">
    <label class="form-label">Descripción:</label>
    <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($ecosistema['descripcion'] ?? '') ?></textarea>
    </div>
    
    <div class="mb-3">
    <label class="form-label">Clasificación:</label>
    <select class="form-control" name="clasificacion" required>
        <option value="bosque" <?= (isset($ecosistema['clasificacion']) && $ecosistema['clasificacion'] === 'bosque') ? 'selected' : '' ?>>Bosque</option>
        <option value="lago" <?= (isset($ecosistema['clasificacion']) && $ecosistema['clasificacion'] === 'lago') ? 'selected' : '' ?>>Lago</option>
        <option value="playa" <?= (isset($ecosistema['clasificacion']) && $ecosistema['clasificacion'] === 'playa') ? 'selected' : '' ?>>Playa</option>
    </select>
    </div>
    
    <div class="mb-3">
    <label class="form-label">Lugar:</label>
    <input type="text" class="form-control" name="lugar" maxlength="120" placeholder="Provincia / Cantón / Referencia" value="<?= htmlspecialchars($ecosistema['lugar'] ?? '') ?>">
    </div>
    
    <div class="mb-3">
    <label class="form-label">Foto actual:</label>
    <?php if (!empty($ecosistema['imagen_url'])): ?>
        <img src="<?= BASE_URL . '/' . htmlspecialchars($ecosistema['imagen_url']) ?>" alt="Foto ecosistema" class="current-img">
    <?php else: ?>
        <em>No hay imagen</em>
    <?php endif; ?>
    </div>
    
    <div class="mb-3">
    <label class="form-label">Subir nueva foto (jpg/png/webp, máx 2MB):</label>
    <input type="file" class="form-control" name="imagen" accept="image/*">
    </div>
    
    <button type="submit" class="btn btn-primary">Actualizar Ecosistema</button>
</form>

<a class="back-link" href="<?= BASE_URL ?>/app/Views/ecosystems/index.php">← Volver al listado</a>
</div>
</body>
</html>