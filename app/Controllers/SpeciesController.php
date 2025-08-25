<?php
require_once '../app/Models/Species.php';
require_once '../app/Models/Ecosystem.php'; 
require_once '../config/database.php';

class SpeciesController {
    private $db;
    private $species;

    public function __construct() {
        $this->db = DB::conn();
        $this->species = new Species($this->db);
    }

    public function index() {
        $stmt = $this->species->getAll();
        $species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/Views/species/index.php';
    }

    public function create() {
        $stmt = Ecosystem::getAll(); 
        $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/Views/species/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->species->nombre_comun = $_POST['nombre_comun'] ?? '';
            $this->species->descripcion = $_POST['descripcion'] ?? '';
            $this->species->tipo = $_POST['tipo'] ?? 'Fauna';
            $this->species->id_ecosistema = $_POST['id_ecosistema'] ?? null;

            $this->species->imagen_url = "";
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $filesystem_dir = __DIR__ . '/../../public/uploads/species/';
                $url_dir = 'uploads/species/';

                if (!is_dir($filesystem_dir)) {
                    mkdir($filesystem_dir, 0755, true);
                }

                $image_name = uniqid() . '-' . basename($_FILES["imagen"]["name"]);
                $target_file_system = $filesystem_dir . $image_name;
                $target_url = $url_dir . $image_name;

                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file_system)) {
                    $this->species->imagen_url = $target_url;
                }
            }

            

            if ($this->species->create()) {
                $errorInfo = $this->conn->errorInfo();
                echo "Error al crear especie: " . implode(', ', $errorInfo);
                header("Location: /ProyectoLP-Especies/public/species"); 
                exit();
            } else {
                echo "Hubo un error al registrar la especie.";
            }
        }
    }

    public function edit(int $id) {
        $species = $this->species->find($id);
        if ($this->species->create()) {
            // Añadimos la ruta completa del proyecto
            header("Location: /ProyectoLP-Especies/public/species"); 
            exit();
        }

        $stmt = Ecosystem::getAll();
        $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once '../app/Views/species/edit.php';
    }

    public function listAPI() {
    header('Content-Type: application/json; charset=UTF-8');
    try {
        $stmt = $this->species->getAll();
        $species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($species_list);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener especies', 'details' => $e->getMessage()]);
        exit;
    }
    }

    public function getAPI(int $id) {
    header('Content-Type: application/json; charset=UTF-8');
    try {
        $species = $this->species->find($id);
        if ($species) {
            echo json_encode($species);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Especie no encontrada']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener especie', 'details' => $e->getMessage()]);
    }
    exit;
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
                $filesystem_dir = __DIR__ . '/../../public/uploads/species/';
                $url_dir = 'uploads/species/';
                if (!is_dir($filesystem_dir)) mkdir($filesystem_dir, 0755, true);

                $image_name = uniqid() . '-' . basename($_FILES['imagen']['name']);
                $target_file_system = $filesystem_dir . $image_name;
                $target_url = $url_dir . $image_name;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file_system)) {
                    $imagen_url = $target_url;
                } else {
                    $errors[] = 'Error al subir la imagen';
                }
            }

            if ($errors) {
                session_start();
                $_SESSION['errores'] = $errors;
                header("Location: /species/edit?id=$id");
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
                $this->species->imagen_url = $this->species->find($id)['imagen_url'] ?? '';
            }

            if ($this->species->update()) {
                session_start();
                $_SESSION['success'] = "Especie actualizada correctamente.";
                header('Location: /species/edit?id=' . $id);
                exit;
            } else {
                session_start();
                $_SESSION['errores'] = ['Error al actualizar la especie.'];
                header("Location: /species/edit?id=$id");
                exit;
            }
        }
    }
}

