<?php
session_start();

$success = $_SESSION['success'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['success'], $_SESSION['errores']);

require_once __DIR__ . '/../../../config/app.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Especie</title>
</head>
<body>
    <h1>Editar Especie</h1>

    <?php if ($success): ?>
        <div style="color: green;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($errores): ?>
        <ul style="color: red;">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/species/update?id=<?= $species['id_especie'] ?>" enctype="multipart/form-data">

        <div>
            <label for="nombre_comun">Nombre Común:</label><br>
            <input type="text" id="nombre_comun" name="nombre_comun" required maxlength="80" value="<?= htmlspecialchars($species['nombre_comun']) ?>">
        </div>
        <br>

        <div>
            <label for="tipo">Tipo de Especie:</label><br>
            <select id="tipo" name="tipo" required>
                <option value="Fauna" <?= $species['tipo'] === 'Fauna' ? 'selected' : '' ?>>Fauna</option>
                <option value="Flora" <?= $species['tipo'] === 'Flora' ? 'selected' : '' ?>>Flora</option>
            </select>
        </div>
        <br>

        <div>
            <label for="id_ecosistema">Ecosistema:</label><br>
            <select id="id_ecosistema" name="id_ecosistema" required>
                <option value="">-- Seleccione un ecosistema --</option>
                <?php foreach ($ecosystems as $eco): ?>
                    <option value="<?= htmlspecialchars($eco['id']) ?>" <?= $eco['id'] == $species['id_ecosistema'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($eco['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>

        <div>
            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?= htmlspecialchars($species['descripcion']) ?></textarea>
        </div>
        <br>

        <div>
            <label>Foto actual:<br>
                <?php if ($species['imagen_url']): ?>
                    <img src="<?= BASE_URL . '/' . $species['imagen_url'] ?>" alt="Foto especie" style="max-width:200px;">
                <?php else: ?>
                    <em>No hay imagen</em>
                <?php endif; ?>
            </label>
        </div>
        <br>

        <div>
            <label for="imagen">Subir nueva foto (jpg/png/webp, máx 2MB):</label><br>
            <input type="file" id="imagen" name="imagen" accept="image/*">
        </div>
        <br>

        <button type="submit">Actualizar</button>
    </form>

    <p><a href="<?= BASE_URL ?>/species">Volver al listado</a></p>
</body>
</html>
