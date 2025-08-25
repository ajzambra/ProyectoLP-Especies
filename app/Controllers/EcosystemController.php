<?php
require_once __DIR__ . '/../../config/app.php';       
require_once __DIR__ . '/../models/Ecosystem.php';

class EcosystemController {
  
  public static function index(): void {
    $ecosistemas = Ecosystem::getAll()->fetchAll(PDO::FETCH_ASSOC);
    $success = $_SESSION['success'] ?? null;
    $errores = $_SESSION['errores'] ?? [];
    unset($_SESSION['success'], $_SESSION['errores']);
    
    require_once __DIR__ . '/../views/ecosystems/index.php';
  }
  
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
    
    // 3) Upload
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
        $mimesOk = [
          'image/jpeg' => 'jpg',
          'image/png'  => 'png',
          'image/webp' => 'webp',
        ];
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
      header('Location: ' . BASE_URL . '/registrar-ecosistema.php');
      exit;
    }
    
    try {
      $id = Ecosystem::create($nombre, $descripcion ?: null, $clasificacion, $lugar ?: null, $imagenRel);
      $_SESSION['success'] = "Ecosistema creado correctamente (ID: $id)";
    } catch (Throwable $e) {
      $_SESSION['errores'] = ['Error al guardar: ' . $e->getMessage()];
    }
    
    header('Location: ' . BASE_URL . '/registrar-ecosistema.php');
    exit;
  }
  
  public static function edit(int $id): void {
    $ecosistema = Ecosystem::find($id);
    if (!$ecosistema) {
      $_SESSION['errores'] = ["Ecosistema con ID $id no encontrado."];
      header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
      exit;
    }
    $success = $_SESSION['success'] ?? null;
    $errores = $_SESSION['errores'] ?? [];
    unset($_SESSION['success'], $_SESSION['errores']);
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
    $ecosistemaActual = Ecosystem::find($id);
    $imagenAnterior = $ecosistemaActual['imagen_url'] ?? null;
    
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
                    // Eliminar imagen anterior si existe
                    if ($imagenAnterior && file_exists($_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/' . $imagenAnterior)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/' . $imagenAnterior);
                    }
                } 
            } 
        } 
    } else {
        $imagenRel = $imagenAnterior;
    }
    
    if ($errores) {
        $_SESSION['errores'] = $errores;
        header("Location: " . BASE_URL . "/api/ecosystems.php?action=edit&id=$id");
        exit;
    }
    
    try {
        Ecosystem::update($id, $nombre, $descripcion ?: null, $clasificacion, $lugar ?: null, $imagenRel);
        $_SESSION['success'] = "Ecosistema actualizado correctamente.";
    } catch (Throwable $e) {
        $_SESSION['errores'] = ['Error al actualizar: ' . $e->getMessage()];
        header("Location: " . BASE_URL . "/api/ecosystems.php?action=edit&id=$id");
        exit;
    }
    
    header('Location: ' . BASE_URL . '/app/Views/ecosystems/index.php');
    exit;
  }
  
  public static function deleteAPI(int $id): void {
    try {
      $ecosistema = Ecosystem::find($id);
      if (!$ecosistema) {
        http_response_code(404);
        echo json_encode(['error' => 'Ecosistema no encontrado']);
        return;
      }
      
      Ecosystem::delete($id);
      
      http_response_code(200);
      echo json_encode(['success' => 'Ecosistema eliminado correctamente']);
    } catch (Throwable $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Error al eliminar: ' . $e->getMessage()]);
    }
  }
  
  public static function getAPI(int $id): void {
    try {
      $ecosistema = Ecosystem::find($id);
      if (!$ecosistema) {
        http_response_code(404);
        echo json_encode(['error' => 'Ecosistema no encontrado']);
        return;
      }
      
      http_response_code(200);
      echo json_encode($ecosistema);
    } catch (Throwable $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Error al obtener: ' . $e->getMessage()]);
    }
  }
  

  public static function createAPI(array $data): void {
    try {
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(['error' => 'El nombre es obligatorio']);
            return;
        }
        
        $id = Ecosystem::create(
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['clasificacion'] ?? 'bosque',
            $data['lugar'] ?? null,
            null 
        );
        
        http_response_code(201);
        echo json_encode([
            'success' => 'Ecosistema creado correctamente',
            'id' => $id
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear: ' . $e->getMessage()]);
    }
  }

  public static function updateAPI(int $id, array $data): void {
    try {
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(['error' => 'El nombre es obligatorio']);
            return;
        }
        
        Ecosystem::update(
            $id,
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['clasificacion'] ?? 'bosque',
            $data['lugar'] ?? null,
            null 
        );
        
        http_response_code(200);
        echo json_encode(['success' => 'Ecosistema actualizado correctamente']);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
    }
  }

  public static function listAPI(): void {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $filters = $input['filters'] ?? [];
        
        $ecosistemas = Ecosystem::getFiltered($filters)->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($ecosistemas);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al listar ecosistemas: ' . $e->getMessage()]);
    }
}

}