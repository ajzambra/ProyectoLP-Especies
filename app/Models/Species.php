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

    // Conexion para la base de datos
    private $conn;
    private $table_name = "especies";

    public function __construct($db) {
        $this->conn = $db;
    }

    // -----------------------------------------------------------------
    // --- MÉTODOS EXISTENTES ---
    // -----------------------------------------------------------------

    /**
     * OBTENER TODOS los registros de especies.
     */
    public function getAll() {
        $query = "SELECT 
                    e.id_especie, 
                    e.nombre_comun, 
                    e.descripcion,
                    e.tipo, 
                    e.imagen_url, 
                    e.id_ecosistema,
                    eco.nombre as nombre_ecosistema
                  FROM 
                    " . $this->table_name . " e
                  LEFT JOIN 
                    ecosistemas eco ON e.id_ecosistema = eco.id
                  ORDER BY 
                    e.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * CREAR un nuevo registro de especie en la base de datos.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    id_ecosistema=:id_ecosistema, 
                    nombre_comun=:nombre_comun, 
                    descripcion=:descripcion, 
                    tipo=:tipo, 
                    imagen_url=:imagen_url";

        $stmt = $this->conn->prepare($query);

        $this->nombre_comun = htmlspecialchars(strip_tags($this->nombre_comun));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->id_ecosistema = htmlspecialchars(strip_tags($this->id_ecosistema));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url));

        $stmt->bindParam(":nombre_comun", $this->nombre_comun);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id_ecosistema", $this->id_ecosistema);
        $stmt->bindParam(":imagen_url", $this->imagen_url);

        return $stmt->execute();
    }

    // -----------------------------------------------------------------
    // --- NUEVOS MeTODOS (AGREGADOS) ---
    // -----------------------------------------------------------------

    /**
     * OBTENER una especie por ID
     */
    public function find(int $id): ?array {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_especie = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * ACTUALIZAR una especie
     */
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . " SET 
                    id_ecosistema=:id_ecosistema, 
                    nombre_comun=:nombre_comun, 
                    descripcion=:descripcion, 
                    tipo=:tipo, 
                    imagen_url=:imagen_url,
                    fecha_actualizacion = NOW()
                  WHERE id_especie=:id_especie";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_especie", $this->id_especie);
        $stmt->bindParam(":nombre_comun", $this->nombre_comun);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id_ecosistema", $this->id_ecosistema);
        $stmt->bindParam(":imagen_url", $this->imagen_url);

        return $stmt->execute();
    }

    /**
     * ELIMINAR una especie
     */
    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_especie = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * OBTENER todas las especies (para API)
     */
    public function getAllAPI() {
        $query = "SELECT 
                    id_especie, 
                    nombre_comun, 
                    descripcion,
                    tipo, 
                    imagen_url, 
                    id_ecosistema
                  FROM " . $this->table_name . " 
                  ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    public function getFiltered($filters = []) {
    $query = "SELECT 
                e.id_especie, 
                e.nombre_comun, 
                e.descripcion,
                e.tipo, 
                e.imagen_url, 
                e.id_ecosistema,
                eco.nombre as nombre_ecosistema
              FROM " . $this->table_name . " e
              LEFT JOIN ecosistemas eco ON e.id_ecosistema = eco.id
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['tipo'])) {
        $query .= " AND e.tipo = ?";
        $params[] = $filters['tipo'];
    }
    
    if (!empty($filters['id_ecosistema'])) {
        $query .= " AND e.id_ecosistema = ?";
        $params[] = $filters['id_ecosistema'];
    }
    
    if (!empty($filters['nombre'])) {
        $query .= " AND e.nombre_comun LIKE ?";
        $params[] = '%' . $filters['nombre'] . '%';
    }
    
    $query .= " ORDER BY e.fecha_creacion DESC";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt;
}
}