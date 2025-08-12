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
  <meta charset="UTF-8" />
  <title>Editar Ecosistema</title>
</head>
<body>
  <h1>Editar Ecosistema</h1>

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

  <form method="POST" action="<?= BASE_URL ?>/api/ecosystems.php?action=update&id=<?= $ecosistema['id'] ?>" enctype="multipart/form-data">
    <label>Nombre:<br>
      <input type="text" name="nombre" maxlength="80" required value="<?= htmlspecialchars($ecosistema['nombre']) ?>">
    </label><br><br>

    <label>Descripción:<br>
      <textarea name="descripcion" rows="4"><?= htmlspecialchars($ecosistema['descripcion']) ?></textarea>
    </label><br><br>

    <label>Clasificación:<br>
      <select name="clasificacion" required>
        <option value="bosque" <?= $ecosistema['clasificacion'] === 'bosque' ? 'selected' : '' ?>>Bosque</option>
        <option value="lago" <?= $ecosistema['clasificacion'] === 'lago' ? 'selected' : '' ?>>Lago</option>
        <option value="playa" <?= $ecosistema['clasificacion'] === 'playa' ? 'selected' : '' ?>>Playa</option>
      </select>
    </label><br><br>

    <label>Lugar:<br>
      <input type="text" name="lugar" maxlength="120" placeholder="Provincia / Cantón / Referencia" value="<?= htmlspecialchars($ecosistema['lugar']) ?>">
    </label><br><br>

    <label>Foto actual:<br>
      <?php if ($ecosistema['imagen_url']): ?>
        <img src="<?= BASE_URL . '/' . $ecosistema['imagen_url'] ?>" alt="Foto ecosistema" style="max-width:200px;">
      <?php else: ?>
        <em>No hay imagen</em>
      <?php endif; ?>
    </label><br><br>

    <label>Subir nueva foto (jpg/png/webp, máx 2MB):<br>
      <input type="file" name="imagen" accept="image/*">
    </label><br><br>

    <button type="submit">Actualizar</button>
  </form>

  <p><a href="<?= BASE_URL ?>/app/Views/ecosystems/index.php">Volver al listado</a></p>
</body>
</html>
