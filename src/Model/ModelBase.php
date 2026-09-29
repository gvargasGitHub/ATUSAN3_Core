<?php

namespace Atusan\Model;

use Atusan\Persistence\DBConnection;
use Atusan\Persistence\DBDriverInterface;

class ModelBase
{
  static $driver = 'DB_DRIVER';
  static $host = 'DB_HOST';
  static $user = 'DB_USER';
  static $pass = 'DB_PASS';
  static $dbname = 'DB_NAME';

  protected DBDriverInterface $conn;

  /**
   * Constructor
   */
  function __construct()
  {
    $this->conn = DBConnection::connect(
      $_ENV[static::$driver],
      $_ENV[static::$host],
      $_ENV[static::$user],
      $_ENV[static::$pass],
      $_ENV[static::$dbname]
    );
  }

  /**
   * Connect
   * Ofrece una alternativa para obtener el recurso de conexión
   * a la base de datos.
   */
  public static function connect(): DBDriverInterface
  {
    return DBConnection::connect(
      $_ENV[static::$driver],
      $_ENV[static::$host],
      $_ENV[static::$user],
      $_ENV[static::$pass],
      $_ENV[static::$dbname]
    );
  }

  static function model(): static
  {
    return new static();
  }

  static function getNew(): static
  {
    return new static();
  }


}
