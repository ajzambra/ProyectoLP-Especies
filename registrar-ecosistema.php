<?php
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'app/Models/Ecosystem.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrar Ecosistema - Sistema de Biodiversidad</title>
  <link rel="stylesheet" href="public/css/registrar-ecosistema.css"/>
  <style>
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
    
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: bold;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .alert ul {
      margin: 0;
      padding-left: 20px;
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
          <li><a href="<?= BASE_URL ?>/registrar-ecosistema.php" class="nav-button active">Registrar Ecosistema</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h2>Registrar Nuevo Ecosistema</h2>
    <p>Complete el formulario para registrar un nuevo ecosistema en el sistema.</p>
    
    <?php if ($success): ?>
      <div class="alert alert-success">
        ✅ <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger">
        ❌ <strong>Errores:</strong>
        <ul>
          <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form id="ecosystem-form"
          method="POST"
          action="<?= BASE_URL ?>/api/ecosystems.php?action=store"
          enctype="multipart/form-data">

      <div class="form-group">
        <label for="nombre">Nombre del Ecosistema *</label>
        <input type="text" id="nombre" name="nombre" required 
               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" />
      </div>

      <div class="form-group">
        <label for="clasificacion">Clasificación *</label>
        <select id="clasificacion" name="clasificacion" required>
          <option value="">-- Seleccione --</option>
          <option value="bosque" <?= ($_POST['clasificacion'] ?? '') === 'bosque' ? 'selected' : '' ?>>Bosque</option>
          <option value="lago" <?= ($_POST['clasificacion'] ?? '') === 'lago' ? 'selected' : '' ?>>Lago</option>
          <option value="playa" <?= ($_POST['clasificacion'] ?? '') === 'playa' ? 'selected' : '' ?>>Playa</option>
        </select>
      </div>

      <div class="form-group">
        <label for="lugar">Lugar / Ubicación</label>
        <input type="text" id="lugar" name="lugar" placeholder="Provincia / Cantón / Referencia" 
               value="<?= htmlspecialchars($_POST['lugar'] ?? '') ?>" />
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="imagen">Imagen del Ecosistema</label>
        <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg, image/webp" />
      </div>

      <div id="image-preview-container">
        <img src="" alt="Previsualización" id="image-preview" class="hidden" />
        <span id="image-preview-placeholder">Sin imagen seleccionada</span>
      </div>

      <button type="submit" class="submit-btn">Registrar Ecosistema</button>
    </form>

  </div>

  <script>
    document.getElementById('imagen').addEventListener('change', function() {
      const file = this.files[0];
      const preview = document.getElementById('image-preview');
      const placeholder = document.getElementById('image-preview-placeholder');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('hidden');
          placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
      }
    });

    document.getElementById('ecosystem-form').addEventListener('submit', function(e) {
      const nombre = document.getElementById('nombre').value;
      const clasificacion = document.getElementById('clasificacion').value;
      
      if (!nombre.trim()) {
        alert('El nombre del ecosistema es obligatorio');
        e.preventDefault();
        return;
      }
      
      if (!clasificacion) {
        alert('Debe seleccionar una clasificación');
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>