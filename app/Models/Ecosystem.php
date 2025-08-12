<?php
require_once __DIR__ . '/../../config/database.php';

class Ecosystem {
  public static function create(string $nombre, ?string $descripcion, string $clasificacion, ?string $lugar, ?string $imagenUrl): int {
    $sql = "INSERT INTO ecosistemas
            (nombre, descripcion, clasificacion, lugar, imagen_url)
            VALUES (?, ?, ?, ?, ?)";
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
}
