<?php
/**
 * --- MODELO Species ---
 * * Esta clase representa a una especie y es responsable de todas las operaciones
 * de la base de datos relacionadas con la tabla 'especies'.
 * Contiene métodos para leer, crear, actualizar y eliminar registros.
 */
class Species {
    public $id_especie;
    public $id_ecosistema;
    public $nombre_comun;
    public $descripcion;
    public $tipo;
    public $imagen_url;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $nombre_ecosistema;

    //Conexion para la base de datos
    private $conn;
    private $table_name = "especies";


    public function __construct($db) {
        $this->conn = $db;
    }

    // -----------------------------------------------------------------
    // --- MÉTODOS ASIGNADOS A ANDRÉS ZAMBRANO ---
    // -----------------------------------------------------------------

    /**
     * OBTENER TODOS los registros de especies.
     * Este método se usa en la página principal para listar todas las especies.
     * Realiza un JOIN con la tabla 'ecosistemas' para obtener también el nombre del hábitat.
     * * @return PDOStatement El resultado de la consulta.
     */
    public function getAll() {
        // Consulta SQL para seleccionar los campos necesarios
        $query = "SELECT 
                    e.id_especie, 
                    e.nombre_comun, 
                    e.descripcion,
                    e.tipo, 
                    e.imagen_url, 
                    eco.nombre as nombre_ecosistema
                  FROM 
                    " . $this->table_name . " e
                  LEFT JOIN 
                    ecosistemas eco ON e.id_ecosistema = eco.id
                  ORDER BY 
                    e.fecha_creacion DESC";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Ejecutar la consulta
        $stmt->execute();

        return $stmt;
    }

    /**
     * CREAR un nuevo registro de especie en la base de datos.
     * Este método se usa para procesar los datos del formulario de registro.
     * * @return bool Devuelve true si la creación fue exitosa, de lo contrario false.
     */
    public function create() {
        // Consulta SQL para insertar un nuevo registro
        $query = "INSERT INTO " . $this->table_name . " SET
                    id_ecosistema=:id_ecosistema, 
                    nombre_comun=:nombre_comun, 
                    descripcion=:descripcion, 
                    tipo=:tipo, 
                    imagen_url=:imagen_url";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // --- Sanitizar los datos ---
        // Se limpian los datos para prevenir ataques XSS o inyecciones de código.
        $this->nombre_comun = htmlspecialchars(strip_tags($this->nombre_comun));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->id_ecosistema = htmlspecialchars(strip_tags($this->id_ecosistema));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url));

        // --- Vincular los valores con la consulta (Binding) ---
        // Esto previene la inyección de SQL.
        $stmt->bindParam(":nombre_comun", $this->nombre_comun);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id_ecosistema", $this->id_ecosistema);
        $stmt->bindParam(":imagen_url", $this->imagen_url);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // -----------------------------------------------------------------
    // --- MÉTODOS ASIGNADOS A ROBERTO BARRIOS ---
    // -----------------------------------------------------------------
    
}