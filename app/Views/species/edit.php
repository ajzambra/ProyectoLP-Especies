<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../Models/Species.php';
require_once __DIR__ . '/../../Models/Ecosystem.php';


if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . '/app/Views/species/index.php');
    exit;
}

$id = (int)$_GET['id'];
$db = DB::conn();
$speciesModel = new Species($db);
$species = $speciesModel->find($id);

if (!$species) {
    $_SESSION['errores'] = ["Especie con ID $id no encontrada."];
    header('Location: ' . BASE_URL . '/app/Views/species/index.php');
    exit;
}

$ecosystemModel = new Ecosystem($db);
$stmt = $ecosystemModel->getAll();
$ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Especie - Sistema de Biodiversidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #005A3B;
            color: #fff;
            padding: 10px 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,.2);
            position: sticky; 
            top: 0; 
            z-index: 1000;
            margin-bottom: 30px;
        }
        .navbar-container {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            max-width: 1200px; 
            margin: 0 auto;
        }
        .navbar-brand { 
            font-size: 24px; 
            font-weight: bold; 
            color: #fff; 
            text-decoration: none; 
        }
        .navbar-links { 
            list-style: none; 
            margin:0; 
            padding:0; 
            display:flex; 
            align-items:center; 
            gap:25px; 
        }
        .navbar-links a { 
            color:#fff; 
            text-decoration:none; 
            font-size:16px; 
            padding:5px 10px; 
            border-radius:5px; 
            transition: background-color .3s; 
        }
        .navbar-links a:hover { 
            background:#004D31; 
        }
        .navbar-links a.nav-button { 
            background:#fff; 
            color:#005A3B; 
            font-weight:bold; 
            padding:8px 15px; 
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
            margin-top: 15px;
        }
        .current-image {
            max-width: 300px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="<?= BASE_URL ?>/index.html" class="navbar-brand">🌿 Biodiversidad</a>
            <nav>
                <ul class="navbar-links">
                    <li><a href="<?= BASE_URL ?>/index.html">Inicio</a></li>
                    <li><a href="<?= BASE_URL ?>/app/Views/species/index.php">Ver Especies</a></li>
                    <li><a href="<?= BASE_URL ?>/app/Views/ecosystems/index.php">Ver Ecosistemas</a></li>
                    <li><a href="<?= BASE_URL ?>/registrar-especie.php">Registrar Especie</a></li>
                    <li><a href="<?= BASE_URL ?>/registrar-ecosistema.php">Registrar Ecosistema</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="my-4">Editar Especie</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($errores): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/api/species.php?action=update&id=<?= $species['id_especie'] ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nombre Común *</label>
                <input type="text" class="form-control" name="nombre_comun" required 
                       value="<?= htmlspecialchars($species['nombre_comun']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Especie *</label>
                <select class="form-control" name="tipo" required>
                    <option value="Fauna" <?= $species['tipo'] === 'Fauna' ? 'selected' : '' ?>>Fauna</option>
                    <option value="Flora" <?= $species['tipo'] === 'Flora' ? 'selected' : '' ?>>Flora</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ecosistema *</label>
                <select class="form-control" name="id_ecosistema" required>
                    <option value="">-- Seleccione un ecosistema --</option>
                    <?php foreach ($ecosystems as $eco): ?>
                        <option value="<?= htmlspecialchars($eco['id']) ?>" 
                                <?= $eco['id'] == $species['id_ecosistema'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eco['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($species['descripcion']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto actual:</label><br>
                <?php if ($species['imagen_url']): ?>
                    <img src="<?= BASE_URL . '/' . $species['imagen_url'] ?>" 
                         alt="Foto especie" class="current-image">
                <?php else: ?>
                    <em>No hay imagen</em>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Subir nueva foto (jpg/png/webp, máx 2MB):</label>
                <input type="file" class="form-control" name="imagen" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Especie</button>
            <a href="<?= BASE_URL ?>/app/Views/species/index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>