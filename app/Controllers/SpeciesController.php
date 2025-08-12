<?php

/**
 * --- CONTROLADOR SpeciesController ---
 * Se encarga de recibir las peticiones del usuario, interactuar con el Modelo (Species.php)
 * para obtener o guardar datos, y cargar las Vistas correspondientes para mostrar la información.
 */

// Incluimos los modelos que vamos a necesitar y la configuración de la base de datos.
require_once '../app/Models/Species.php';
require_once '../app/Models/Ecosystem.php'; 
require_once '../config/database.php';

class SpeciesController {
    private $db;
    private $species;

    /**
     * Constructor: Se ejecuta automáticamente al crear un objeto SpeciesController.
     * Prepara la conexión a la base de datos y crea una instancia del modelo Species.
     */
    public function __construct() {
        $this->db = DB::conn();
        $this->species = new Species($this->db);
    }

    /**
     * ACCIÓN: index
     * Muestra la página principal con la lista de todas las especies registradas.
     * Corresponde a la funcionalidad "Ver especies registradas".
     */
    public function index() {
        $stmt = $this->species->getAll();
        $species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/Views/species/index.php';
    }

    /**
     * ACCIÓN: create
     * Muestra el formulario para registrar una nueva especie.
     * Corresponde a la funcionalidad "Registrar especie".
     */
    public function create() {
        $stmt = Ecosystem::getAll(); 
        $ecosystems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/Views/species/create.php';
    }

    /**
     * ACCIÓN: store
     * Procesa y guarda los datos enviados desde el formulario de creación.
     * No tiene una vista asociada, solo redirige al usuario.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->species->nombre_comun = $_POST['nombre_comun'] ?? '';
            $this->species->descripcion = $_POST['descripcion'] ?? '';
            $this->species->tipo = $_POST['tipo'] ?? 'Fauna';
            $this->species->id_ecosistema = $_POST['id_ecosistema'] ?? null;
            
            // Lógica para manejar la subida de la imagen
            $this->species->imagen_url = "";
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                
                // --- RUTA PARA GUARDAR EN EL SERVIDOR (FILE SYSTEM) ---
                $filesystem_dir = __DIR__ . '/../../public/uploads/species/';

                // --- RUTA PARA GUARDAR EN LA BASE DE DATOS (URL) ---
                $url_dir = 'uploads/species/';

                // Asegurarse de que el directorio exista
                if (!is_dir($filesystem_dir)) {
                    mkdir($filesystem_dir, 0755, true);
                }
                
                // Generar un nombre de archivo único para evitar sobreescrituras
                $image_name = uniqid() . '-' . basename($_FILES["imagen"]["name"]);
                
                $target_file_system = $filesystem_dir . $image_name;
                $target_url = $url_dir . $image_name;
                
                // Mover el archivo temporal a su ubicación final en el servidor
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file_system)) {
                    $this->species->imagen_url = $target_url;
                }
            }

            if ($this->species->create()) {
                header("Location: /species"); 
                exit();
            } else {
                echo "Hubo un error al registrar la especie.";
            }
        }
    }
}
