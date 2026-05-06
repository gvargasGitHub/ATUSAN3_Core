<?php

namespace Atusan\Model;

use Atusan\Persistence\DBConnection;

class ModelBase
{
  static $driver = 'DB_DRIVER';
  static $host = 'DB_HOST';
  static $user = 'DB_USER';
  static $pass = 'DB_PASS';
  static $dbname = 'DB_NAME';

  protected $conn;

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

  static public function connect(): DBConnection
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
}
