<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../Models/Ecosystem.php';

$filters = [
    'clasificacion' => $_GET['clasificacion'] ?? '',
    'nombre' => $_GET['nombre'] ?? '',
    'lugar' => $_GET['lugar'] ?? ''
];

$ecosistemas = Ecosystem::getFiltered($filters)->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lista de Ecosistemas - Sistema de Biodiversidad</title>
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
.navbar-links a.nav-button.active { 
    background:#E6F0E6; 
    color:#005A3B; 
}
.card { 
    margin-bottom: 20px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    border: none;
    border-radius: 10px;
}
.card-img-top { 
    height: 200px; 
    object-fit: cover; 
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
.btn-action { 
    margin: 5px; 
}
.filter-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border: 1px solid #81c784;
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
                <li><a href="<?= BASE_URL ?>/app/Views/ecosystems/index.php" class="nav-button active">Ver Ecosistemas</a></li>
                <li><a href="<?= BASE_URL ?>/registrar-especie.php">Registrar Especie</a></li>
                <li><a href="<?= BASE_URL ?>/registrar-ecosistema.php">Registrar Ecosistema</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
<h1 class="my-4">Lista de Ecosistemas</h1>

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

<div class="card mb-4 filter-card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">🔍 Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" 
                       placeholder="Buscar por nombre">
            </div>
            
            <div class="col-md-3">
                <label for="clasificacion" class="form-label">Clasificación</label>
                <select class="form-control" id="clasificacion" name="clasificacion">
                    <option value="">Todas las clasificaciones</option>
                    <option value="bosque" <?= ($_GET['clasificacion'] ?? '') === 'bosque' ? 'selected' : '' ?>>Bosque</option>
                    <option value="lago" <?= ($_GET['clasificacion'] ?? '') === 'lago' ? 'selected' : '' ?>>Lago</option>
                    <option value="playa" <?= ($_GET['clasificacion'] ?? '') === 'playa' ? 'selected' : '' ?>>Playa</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="lugar" class="form-label">Lugar</label>
                <input type="text" class="form-control" id="lugar" name="lugar" 
                       value="<?= htmlspecialchars($_GET['lugar'] ?? '') ?>" 
                       placeholder="Buscar por lugar">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Filtrar</button>
                    <a href="<?= BASE_URL ?>/app/Views/ecosystems/index.php" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<a href="<?= BASE_URL ?>/registrar-ecosistema.php" class="btn btn-success mb-4">Registrar Nuevo Ecosistema</a>

<div class="row">
    <?php if (empty($ecosistemas)): ?>
    <div class="col-12">
        <div class="alert alert-info">
        No hay ecosistemas registrados. <a href="<?= BASE_URL ?>/registrar-ecosistema.php">Crear el primero</a>.
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($ecosistemas as $eco): ?>
        <div class="col-md-4">
        <div class="card">
            <?php if (!empty($eco['imagen_url'])): ?>
            <img src="<?= BASE_URL . '/' . htmlspecialchars($eco['imagen_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($eco['nombre']) ?>">
            <?php else: ?>
            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center">
                <span>Sin imagen</span>
            </div>
            <?php endif; ?>
            
            <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($eco['nombre']) ?></h5>
            <p class="card-text">
                <strong>Clasificación:</strong> <?= isset($eco['clasificacion']) ? htmlspecialchars($eco['clasificacion']) : 'No especificado' ?><br>
                <strong>Ubicación:</strong> <?= !empty($eco['lugar']) ? htmlspecialchars($eco['lugar']) : 'No especificado' ?>
            </p>
            <p class="card-text">
                <strong>Descripción:</strong> <?= !empty($eco['descripcion']) ? htmlspecialchars(substr($eco['descripcion'], 0, 100)) . '...' : 'Sin descripción' ?>
            </p>
            
            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/api/ecosystems.php?action=edit&id=<?= $eco['id'] ?>" class="btn btn-warning btn-action">Editar</a>
                <form action="<?= BASE_URL ?>/app/Views/ecosystems/delete.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ecosistema?');">
                <input type="hidden" name="id" value="<?= $eco['id'] ?>">
                <button type="submit" class="btn btn-danger btn-action">Eliminar</button>
                </form>
            </div>
            </div>
        </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>