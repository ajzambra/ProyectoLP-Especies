<?php
require_once __DIR__ . '/../Models/Species.php';
require_once __DIR__ . '/../Models/Ecosystem.php'; 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';

class SpeciesController {
    private $db;
    private $species;

    public function __construct() {
        $this->db = DB::conn();
        $this->species = new Species($this->db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $stmt = $this->species->getAll();
        $species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $success = $_SESSION['success'] ?? null;
        $errores = $_SESSION['errores'] ?? [];
        unset($_SESSION['success'], $_SESSION['errores']);
        
        require_once __DIR__ . '/../Views/species/index.php';
    }

    public function create() {
        $stmt = Ecosystem::getAll(); 
        $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../Views/species/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $nombre_comun = $_POST['nombre_comun'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $tipo = $_POST['tipo'] ?? 'Fauna';
            $id_ecosistema = $_POST['id_ecosistema'] ?? null;

            if (empty(trim($nombre_comun))) {
                $errors[] = 'El nombre común es obligatorio';
            }
            
            if (empty($id_ecosistema)) {
                $errors[] = 'Debe seleccionar un ecosistema';
            }

            if (!empty($errors)) {
                $_SESSION['errores'] = $errors;
                header("Location: " . BASE_URL . "/registrar-especie.php");
                exit;
            }

            $this->species->nombre_comun = $nombre_comun;
            $this->species->descripcion = $descripcion;
            $this->species->tipo = $tipo;
            $this->species->id_ecosistema = $id_ecosistema;
            $this->species->imagen_url = "";

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $filesystem_dir = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/uploads/species/';
                $url_dir = 'uploads/species/';

                if (!is_dir($filesystem_dir)) {
                    if (!mkdir($filesystem_dir, 0755, true)) {
                        $errors[] = 'No se pudo crear la carpeta de imágenes';
                    }
                }

                if (empty($errors)) {
                    $image_name = uniqid() . '-' . basename($_FILES["imagen"]["name"]);
                    $target_file_system = $filesystem_dir . $image_name;
                    $target_url = $url_dir . $image_name;

                    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file_system)) {
                        $this->species->imagen_url = $target_url;
                    } else {
                        $errors[] = 'No se pudo guardar la imagen';
                    }
                }
            }

            if (!empty($errors)) {
                $_SESSION['errores'] = $errors;
                header("Location: " . BASE_URL . "/registrar-especie.php");
                exit;
            }

            if ($this->species->create()) {
                $_SESSION['success'] = "Especie creada correctamente (ID: " . $this->db->lastInsertId() . ")";
                header("Location: " . BASE_URL . "/app/Views/species/index.php");
                exit();
            } else {
                $_SESSION['errores'] = ['Error al registrar la especie en la base de datos'];
                header("Location: " . BASE_URL . "/registrar-especie.php");
                exit();
            }
        }
    }

    public function edit(int $id) {
        $species = $this->species->find($id);
        if (!$species) {
            $_SESSION['errores'] = ["Especie con ID $id no encontrada."];
            header('Location: ' . BASE_URL . '/app/Views/species/index.php');
            exit;
        }

        $stmt = Ecosystem::getAll();
        $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $success = $_SESSION['success'] ?? null;
        $errores = $_SESSION['errores'] ?? [];
        unset($_SESSION['success'], $_SESSION['errores']);

        require_once __DIR__ . '/../Views/species/edit.php';
    }

    public function update(int $id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_comun = $_POST['nombre_comun'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $tipo = $_POST['tipo'] ?? 'Fauna';
            $id_ecosistema = $_POST['id_ecosistema'] ?? null;

            $errors = [];
            if (trim($nombre_comun) === '') {
                $errors[] = 'El nombre común es obligatorio';
            }
            if (!$id_ecosistema) {
                $errors[] = 'Debe seleccionar un ecosistema';
            }
            $tipos_validos = ['Fauna', 'Flora'];
            if (!in_array($tipo, $tipos_validos)) {
                $errors[] = 'Tipo de especie inválido';
            }

            $imagen_url = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $filesystem_dir = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/uploads/species/';
                $url_dir = 'uploads/species/';
                if (!is_dir($filesystem_dir)) {
                    if (!mkdir($filesystem_dir, 0755, true)) {
                        $errors[] = 'No se pudo crear la carpeta de imágenes';
                    }
                }

                if (empty($errors)) {
                    $image_name = uniqid() . '-' . basename($_FILES['imagen']['name']);
                    $target_file_system = $filesystem_dir . $image_name;
                    $target_url = $url_dir . $image_name;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file_system)) {
                        $imagen_url = $target_url;
                    } else {
                        $errors[] = 'Error al subir la imagen';
                    }
                }
            }

            if ($errors) {
                $_SESSION['errores'] = $errors;
                header("Location: " . BASE_URL . "/api/species.php?action=edit&id=$id");
                exit;
            }

            $this->species->id_especie = $id;
            $this->species->nombre_comun = $nombre_comun;
            $this->species->descripcion = $descripcion;
            $this->species->tipo = $tipo;
            $this->species->id_ecosistema = $id_ecosistema;
            
            if ($imagen_url) {
                $this->species->imagen_url = $imagen_url;
            } else {
                $current_species = $this->species->find($id);
                $this->species->imagen_url = $current_species['imagen_url'] ?? '';
            }

            if ($this->species->update()) {
                $_SESSION['success'] = "Especie actualizada correctamente.";
                header('Location: ' . BASE_URL . '/app/Views/species/index.php');
                exit;
            } else {
                $_SESSION['errores'] = ['Error al actualizar la especie.'];
                header("Location: " . BASE_URL . "/api/species.php?action=edit&id=$id");
                exit;
            }
        }
    }

    // -----------------------------------------------------------------
    // --- MeTODOS PARA API ---
    // -----------------------------------------------------------------

    public function listAPI() {
        try {
            $stmt = $this->species->getAllAPI();
            $species = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($species);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al listar especies: ' . $e->getMessage()]);
        }
    }

    public function getAPI(int $id) {
        try {
            $species = $this->species->find($id);
            
            if (!$species) {
                http_response_code(404);
                echo json_encode(['error' => 'Especie no encontrada']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($species);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener especie: ' . $e->getMessage()]);
        }
    }

    public function createAPI(array $data) {
        try {
            if (empty($data['nombre_comun'])) {
                http_response_code(400);
                echo json_encode(['error' => 'El nombre común es obligatorio']);
                return;
            }

            // Verificar que el ecosistema existe
            if (!empty($data['id_ecosistema'])) {
                $ecosystemModel = new Ecosystem($this->db);
                $ecosistema = $ecosystemModel->find($data['id_ecosistema']);
                if (!$ecosistema) {
                    http_response_code(400);
                    echo json_encode(['error' => 'El ecosistema especificado no existe']);
                    return;
                }
            }

            $this->species->nombre_comun = $data['nombre_comun'];
            $this->species->descripcion = $data['descripcion'] ?? '';
            $this->species->tipo = $data['tipo'] ?? 'Fauna';
            $this->species->id_ecosistema = $data['id_ecosistema'] ?? null;
            $this->species->imagen_url = '';

            if ($this->species->create()) {
                $id = $this->db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    'success' => 'Especie creada correctamente',
                    'id_especie' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear especie']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear especie: ' . $e->getMessage()]);
        }
    }

    public function updateAPI(int $id, array $data) {
        try {
            $species = $this->species->find($id);
            
            if (!$species) {
                http_response_code(404);
                echo json_encode(['error' => 'Especie no encontrada']);
                return;
            }

            $this->species->id_especie = $id;
            $this->species->nombre_comun = $data['nombre_comun'] ?? $species['nombre_comun'];
            $this->species->descripcion = $data['descripcion'] ?? $species['descripcion'];
            $this->species->tipo = $data['tipo'] ?? $species['tipo'];
            $this->species->id_ecosistema = $data['id_ecosistema'] ?? $species['id_ecosistema'];
            $this->species->imagen_url = $species['imagen_url'];

            if ($this->species->update()) {
                http_response_code(200);
                echo json_encode(['success' => 'Especie actualizada correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar especie']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar especie: ' . $e->getMessage()]);
        }
    }

    public function deleteAPI(int $id) {
        try {
            $species = $this->species->find($id);
            
            if (!$species) {
                http_response_code(404);
                echo json_encode(['error' => 'Especie no encontrada']);
                return;
            }

            if ($this->species->delete($id)) {
                http_response_code(200);
                echo json_encode(['success' => 'Especie eliminada correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar especie']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar especie: ' . $e->getMessage()]);
        }
    }

}