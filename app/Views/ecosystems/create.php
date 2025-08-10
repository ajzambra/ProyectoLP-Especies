<?php
session_start();
require_once __DIR__ . '/../../../config/app.php';
$errores = $_SESSION['errores'] ?? [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['errores'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Ecosistema</title>
</head>
<body>
  <h1>Registrar Ecosistema</h1>

  <?php if ($success): ?>
    <div style="color:green"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($errores): ?>
    <ul style="color:red">
      <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/api/ecosystems.php" enctype="multipart/form-data">
    <label>Nombre:<br>
      <input type="text" name="nombre" maxlength="80" required>
    </label><br><br>

    <label>Descripción:<br>
      <textarea name="descripcion" rows="4"></textarea>
    </label><br><br>

    <label>Clasificación:<br>
      <select name="clasificacion" required>
        <option value="bosque">Bosque</option>
        <option value="lago">Lago</option>
        <option value="playa">Playa</option>
      </select>
    </label><br><br>

    <label>Lugar:<br>
      <input type="text" name="lugar" maxlength="120" placeholder="Provincia / Cantón / Referencia">
    </label><br><br>

    <label>Foto (jpg/png/webp, máx 2MB):<br>
      <input type="file" name="imagen" accept="image/*">
    </label><br><br>

    <button type="submit">Guardar</button>
  </form>

  <p style="margin-top:12px;color:#666">Las imágenes se guardan en <code>/uploads/ecosystems</code> y la ruta se almacena en la columna <code>imagen_url</code>.</p>
</body>
</html>
