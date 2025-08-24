<?php
require_once __DIR__ . '/../../../config/app.php';
require_once __DIR__ . '/../../Models/Species.php';
require_once __DIR__ . '/../../Models/Ecosystem.php';

$db = DB::conn();
$speciesModel = new Species($db);
$stmt = $speciesModel->getAll();
$species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lista de Especies - Sistema de Biodiversidad</title>
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
</style>
</head>
<body>
<header class="navbar">
<div class="navbar-container">
    <a href="<?= BASE_URL ?>/index.html" class="navbar-brand">🌿 Biodiversidad</a>
    <nav>
    <ul class="navbar-links">
        <li><a href="<?= BASE_URL ?>/index.html">Inicio</a></li>
        <li><a href="<?= BASE_URL ?>/app/Views/species/index.php" class="nav-button active">Ver Especies</a></li>
        <li><a href="<?= BASE_URL ?>/app/Views/ecosystems/index.php">Ver Ecosistemas</a></li>
        <li><a href="<?= BASE_URL ?>/registrar-especie.php">Registrar Especie</a></li>
        <li><a href="<?= BASE_URL ?>/registrar-ecosistema.html">Registrar Ecosistema</a></li>
    </ul>
    </nav>
</div>
</header>

<div class="container">
<h1 class="my-4">Lista de Especies</h1>

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

<a href="<?= BASE_URL ?>/registrar-especie.html" class="btn btn-primary mb-4">Registrar Nueva Especie</a>

<div class="row">
    <?php if (empty($species_list)): ?>
    <div class="col-12">
        <div class="alert alert-info">
        No hay especies registradas. <a href="<?= BASE_URL ?>/registrar-especie.html">Registrar la primera</a>.
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($species_list as $specie): ?>
        <div class="col-md-4">
        <div class="card">
            <?php if (!empty($specie['imagen_url'])): ?>
            <img src="<?= BASE_URL . '/' . htmlspecialchars($specie['imagen_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($specie['nombre_comun']) ?>">
            <?php else: ?>
            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center">
                <span>Sin imagen</span>
            </div>
            <?php endif; ?>
            
            <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($specie['nombre_comun']) ?></h5>
            <p class="card-text">
                <strong>Tipo:</strong> <?= htmlspecialchars($specie['tipo']) ?><br>
                <strong>Ecosistema:</strong> <?= htmlspecialchars($specie['nombre_ecosistema'] ?? 'No asignado') ?><br>
                <strong>Descripción:</strong> <?= !empty($specie['descripcion']) ? htmlspecialchars(substr($specie['descripcion'], 0, 100)) . '...' : 'Sin descripción' ?>
            </p>
            

            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/api/species.php?action=edit&id=<?= $specie['id_especie'] ?>" class="btn btn-warning btn-action">Editar</a>

            <form action="<?= BASE_URL ?>/app/Views/species/delete.php" method="POST" onsubmit="return confirm('¿Eliminar esta especie?');">
                <input type="hidden" name="id_especie" value="<?= $specie['id_especie'] ?>">
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