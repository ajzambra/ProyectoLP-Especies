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
    $sql = "SELECT id, nombre FROM ecosistemas ORDER BY nombre ASC";
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
    $sql = "UPDATE ecosistemas SET nombre = ?, descripcion = ?, clasificacion = ?, lugar = ?, imagen_url = ?, updated_at = NOW() WHERE id = ?";
    $st = DB::conn()->prepare($sql);
    $st->execute([$nombre, $descripcion, $clasificacion, $lugar, $imagenUrl, $id]);
  }
  
  public static function all(array $filters = []): array {
    $sql = "SELECT id, nombre, descripcion, clasificacion, lugar, imagen_url, created_at
            FROM ecosistemas";
    $where = [];
    $p = [];

    if (!empty($filters['q'])) {
      $where[] = "(nombre LIKE :q OR lugar LIKE :q)";
      $p[':q'] = '%' . $filters['q'] . '%';
    }
    if (!empty($filters['clasificacion'])) {
      $where[] = "clasificacion = :c";
      $p[':c'] = $filters['clasificacion'];
    }
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);

    $sql .= " ORDER BY created_at DESC, id DESC";

    $st = DB::conn()->prepare($sql);
    $st->execute($p);
    return $st->fetchAll();
  }

}
