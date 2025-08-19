<?php
class DB {
  private static ?PDO $conn = null;

  public static function conn(): PDO {
    if (!self::$conn) {
      $dsn = "mysql:host=127.0.0.1;port=3306;dbname=biodiversidad;charset=utf8mb4";
      self::$conn = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    }
    return self::$conn;
  }
}

