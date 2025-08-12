<?php
session_start();
require_once __DIR__ . '/../../config/app.php';       // define('BASE_URL','/ProyectoLP-Especies');
require_once __DIR__ . '/../models/Ecosystem.php';

class EcosystemController {
  public static function store(): void {
    $errores = [];

    // 1) Datos del formulario
    $nombre        = trim($_POST['nombre'] ?? '');
    $descripcion   = trim($_POST['descripcion'] ?? '');
    $clasificacion = $_POST['clasificacion'] ?? 'bosque';
    $lugar         = trim($_POST['lugar'] ?? '');

    // 2) Validaciones
    if ($nombre === '')                 $errores[] = 'El nombre es obligatorio';
    if (mb_strlen($nombre) > 80)        $errores[] = 'El nombre no debe superar 80 caracteres';
    $permitidos = ['bosque','lago','playa'];
    if (!in_array($clasificacion, $permitidos, true)) $errores[] = 'Clasificación inválida';
    if ($lugar !== '' && mb_strlen($lugar) > 120)     $errores[] = 'El lugar no debe superar 120 caracteres';

    // 3) Upload (opcional)
    $imagenRel = null; // ruta relativa a guardar en BD
    if (!empty($_FILES['imagen']['name'])) {
      if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Error al subir la imagen';
      } else {
        $max = 2 * 1024 * 1024; // 2MB
        if ($_FILES['imagen']['size'] > $max) $errores[] = 'La imagen supera 2MB';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        $mimesOk = [
          'image/jpeg' => 'jpg',
          'image/png'  => 'png',
          'image/webp' => 'webp',
        ];
        if (!isset($mimesOk[$mime])) $errores[] = 'Formato de imagen no permitido (solo JPG, PNG, WEBP)';

        // Si todo bien, mover al directorio /uploads/ecosystems
        if (!$errores) {
          $ext   = $mimesOk[$mime];
          $fname = 'eco_' . uniqid() . '.' . $ext;

          $destDirAbs = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/uploads/ecosystems';
          if (!is_dir($destDirAbs)) mkdir($destDirAbs, 0777, true);

          $destAbs = $destDirAbs . '/' . $fname;
          if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destAbs)) {
            $errores[] = 'No se pudo guardar la imagen';
          } else {
            $imagenRel = 'uploads/ecosystems/' . $fname;
          }
        }
      }
    }

    if ($errores) {
      $_SESSION['errores'] = $errores;
      header('Location: ' . BASE_URL . '/app/Views/ecosystems/create.php');
      exit;
    }

    // 4) Guardar
    try {
      $id = Ecosystem::create($nombre, $descripcion ?: null, $clasificacion, $lugar ?: null, $imagenRel);
      $_SESSION['success'] = "Ecosistema creado (ID: $id)";
    } catch (Throwable $e) {
      $_SESSION['errores'] = ['Error al guardar: ' . $e->getMessage()];
    }

    header('Location: ' . BASE_URL . '/app/Views/ecosystems/create.php');
    exit;
  }

  public static function edit(int $id): void {
    $ecosistema = Ecosystem::find($id);
    if (!$ecosistema) {
      $_SESSION['errores'] = ["Ecosistema con ID $id no encontrado."];
      header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
      exit;
    }
    require_once __DIR__ . '/../views/ecosystems/edit.php';
  }

  public static function update(int $id): void {
    $errores = [];

    $nombre        = trim($_POST['nombre'] ?? '');
    $descripcion   = trim($_POST['descripcion'] ?? '');
    $clasificacion = $_POST['clasificacion'] ?? 'bosque';
    $lugar         = trim($_POST['lugar'] ?? '');

    if ($nombre === '')                 $errores[] = 'El nombre es obligatorio';
    if (mb_strlen($nombre) > 80)        $errores[] = 'El nombre no debe superar 80 caracteres';
    $permitidos = ['bosque','lago','playa'];
    if (!in_array($clasificacion, $permitidos, true)) $errores[] = 'Clasificación inválida';
    if ($lugar !== '' && mb_strlen($lugar) > 120)     $errores[] = 'El lugar no debe superar 120 caracteres';

    $imagenRel = null;
    if (!empty($_FILES['imagen']['name'])) {
      if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Error al subir la imagen';
      } else {
        $max = 2 * 1024 * 1024; // 2MB
        if ($_FILES['imagen']['size'] > $max) $errores[] = 'La imagen supera 2MB';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        $mimesOk = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($mimesOk[$mime])) $errores[] = 'Formato de imagen no permitido (solo JPG, PNG, WEBP)';

        if (!$errores) {
          $ext   = $mimesOk[$mime];
          $fname = 'eco_' . uniqid() . '.' . $ext;

          $destDirAbs = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/uploads/ecosystems';
          if (!is_dir($destDirAbs)) mkdir($destDirAbs, 0777, true);

          $destAbs = $destDirAbs . '/' . $fname;
          if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destAbs)) {
            $errores[] = 'No se pudo guardar la imagen';
          } else {
            $imagenRel = 'uploads/ecosystems/' . $fname;
          }
        }
      }
    }

    if ($errores) {
      $_SESSION['errores'] = $errores;
      header("Location: " . BASE_URL . "/app/Views/ecosystems/edit.php?id=$id");
      exit;
    }

    try {
      Ecosystem::update($id, $nombre, $descripcion ?: null, $clasificacion, $lugar ?: null, $imagenRel);
      $_SESSION['success'] = "Ecosistema actualizado correctamente.";
    } catch (Throwable $e) {
      $_SESSION['errores'] = ['Error al actualizar: ' . $e->getMessage()];
      header("Location: " . BASE_URL . "/app/Views/ecosystems/edit.php?id=$id");
      exit;
    }

    header("Location: " . BASE_URL . "/app/Views/ecosystems/edit.php?id=$id");
    exit;
  }
}

