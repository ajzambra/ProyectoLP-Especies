<?php
require_once __DIR__ . '/../../config/database.php';
class Ecosystem {
  public static function create(string $nombre, ?string $descripcion, string $clasificacion, ?string $lugar, ?string $imagenUrl): int {
    $sql = "INSERT INTO ecosistemas (nombre, descripcion, clasificacion, lugar, imagen_url) VALUES (?, ?, ?, ?, ?)";
    $st = DB::conn()->prepare($sql);
    $st->execute([$nombre, $descripcion, $clasificacion, $lugar, $imagenUrl]);
    return (int) DB::conn()->lastInsertId();
  }
  
  public static function getAll() {
    $sql = "SELECT id, nombre, descripcion, clasificacion, lugar, imagen_url FROM ecosistemas ORDER BY nombre ASC";
    $stmt = DB::conn()->prepare($sql);
    $stmt->execute();
    return $stmt;
  }
  
  public static function find(int $id): ?array {
    $sql = "SELECT * FROM ecosistemas WHERE id = ?";
    $st = DB::conn()->prepare($sql);
    $st->execute([$id]);
    $result = $st->fetch();
    return $result ?: null;
  }
  
  public static function update(int $id, string $nombre, ?string $descripcion, string $clasificacion, ?string $lugar, ?string $imagenUrl): void {
    if ($imagenUrl) {
      $sql = "UPDATE ecosistemas SET nombre = ?, descripcion = ?, clasificacion = ?, lugar = ?, imagen_url = ?, updated_at = NOW() WHERE id = ?";
      $st = DB::conn()->prepare($sql);
      $st->execute([$nombre, $descripcion, $clasificacion, $lugar, $imagenUrl, $id]);
    } else {
      $sql = "UPDATE ecosistemas SET nombre = ?, descripcion = ?, clasificacion = ?, lugar = ?, updated_at = NOW() WHERE id = ?";
      $st = DB::conn()->prepare($sql);
      $st->execute([$nombre, $descripcion, $clasificacion, $lugar, $id]);
    }
  }
  
  public static function delete(int $id): void {
    $sql = "DELETE FROM ecosistemas WHERE id = ?";
    $st = DB::conn()->prepare($sql);
    $st->execute([$id]);
  }

  public static function getFiltered($filters = []) {
    $db = DB::conn();
    $query = "SELECT * FROM ecosistemas WHERE 1=1";
    $params = [];
    
    if (!empty($filters['clasificacion'])) {
        $query .= " AND clasificacion = ?";
        $params[] = $filters['clasificacion'];
    }
    
    if (!empty($filters['nombre'])) {
        $query .= " AND nombre LIKE ?";
        $params[] = '%' . $filters['nombre'] . '%';
    }
    
    if (!empty($filters['lugar'])) {
        $query .= " AND lugar LIKE ?";
        $params[] = '%' . $filters['lugar'] . '%';
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt;
}
}

