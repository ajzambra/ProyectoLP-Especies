<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Especie - Sistema de Biodiversidad</title>
  <link rel="stylesheet" href="public/css/registrar-especie.css">
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
  </style>
</head>
<body>

  <header class="navbar">
    <div class="navbar-container">
      <a href="/ProyectoLP-Especies/index.html" class="navbar-brand">🌿 Biodiversidad</a>
      <nav>
        <ul class="navbar-links">
          <li><a href="/ProyectoLP-Especies/index.html">Inicio</a></li>
          <li><a href="/ProyectoLP-Especies/app/Views/species/index.php">Ver Especies</a></li>
          <li><a href="/ProyectoLP-Especies/app/Views/ecosystems/index.php">Ver Ecosistemas</a></li>
          <li><a href="/ProyectoLP-Especies/registrar-especie.php" class="nav-button active">Registrar Especie</a></li>
          <li><a href="/ProyectoLP-Especies/registrar-ecosistema.php">Registrar Ecosistema</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h2>Registrar Nueva Especie</h2>
    <p>Complete el formulario para registrar una nueva especie en el sistema.</p>
    
    <form id="species-form" method="POST" action="/ProyectoLP-Especies/api/species.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="store">

      <div class="form-group">
        <label for="nombre_comun">Nombre de la Especie *</label>
        <input type="text" id="nombre_comun" name="nombre_comun" required>
      </div>

      <div class="form-group">
        <label>Tipo de Especie *</label>
        <div class="radio-group">
          <input type="radio" id="tipoFauna" name="tipo" value="Fauna" checked>
          <label for="tipoFauna">Fauna</label>
          <input type="radio" id="tipoFlora" name="tipo" value="Flora">
          <label for="tipoFlora">Flora</label>
        </div>
      </div>

      <div class="form-group">
        <label for="id_ecosistema">Ecosistema *</label>
        <select id="id_ecosistema" name="id_ecosistema" required>
          <option value="">-- Seleccione un ecosistema --</option>
          <?php
          require_once 'config/database.php';
          require_once 'app/Models/Ecosystem.php';
          
          try {
            $db = DB::conn();
            $ecosystemModel = new Ecosystem($db);
            $stmt = $ecosystemModel->getAll();
            $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($ecosystems as $eco) {
              echo '<option value="' . $eco['id'] . '">' . htmlspecialchars($eco['nombre']) . '</option>';
            }
          } catch (Exception $e) {
            echo '<option value="">Error al cargar ecosistemas</option>';
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción y Características</label>
        <textarea id="descripcion" name="descripcion" rows="4"></textarea>
      </div>

      <div class="form-group">
        <label for="imagen">Imagen de la Especie</label>
        <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg, image/webp">
      </div>

      <div id="image-preview-container">
        <img src="" alt="Previsualización" id="image-preview" class="hidden">
        <span id="image-preview-placeholder">Sin imagen seleccionada</span>
      </div>

      <button type="submit" class="submit-btn">Registrar Especie</button>
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

    document.getElementById('species-form').addEventListener('submit', function(e) {
      const nombre = document.getElementById('nombre_comun').value;
      const ecosistema = document.getElementById('id_ecosistema').value;
      
      if (!nombre.trim()) {
        alert('El nombre de la especie es obligatorio');
        e.preventDefault();
        return;
      }
      
      if (!ecosistema) {
        alert('Debe seleccionar un ecosistema');
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>