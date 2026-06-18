<?php

namespace Atusan\Persistence;

class DBConnection
{
  protected DBDriverBase $driver;

  static public function connect(string $driver, string $host, string  $user, string $pass, string $database, ?bool $ssl): DBConnection
  {
    $conn = new DBConnection();

    $class = "Atusan\\Persistence\\{$driver}DBDriver";

    $conn->driver = new $class();

    // Connect generará una excepción del tipo DBDriverException
    $conn->driver->connect($host, $user, $pass, $database, $ssl);

    return $conn;
  }

  /**
   * 
   */
  public function close(): void
  {
    if (is_a($this->driver, 'Atusan\\Persistence\\DBDriverInterface')) $this->driver->close();
  }

  /**
   * 
   */
  public function query(string $sql, array $params = []): array
  {
    return $this->driver->query($sql, $params);
  }
  /**
   * 
   */
  public function execute(string $sql, array $params = []): bool
  {
    return $this->driver->execute($sql, $params);
  }
  /**
   * 
   */
  public function routine(string $sql, array $params = [], array $outvars = [], array $outvarstypes = []): array
  {
    return $this->driver->routine($sql, $params, $outvars, $outvarstypes);
  }
  /**
   * 
   */
  public function autocommit(bool $mode): void
  {
    $this->driver->autocommit($mode);
  }
  /**
   * 
   */
  public function commit(): void
  {
    $this->driver->commit();
  }
  /**
   * 
   */
  public function rollback(): void
  {
    $this->driver->rollback();
  }
  /**
   * 
   */
  public function sqlstate(): void
  {
    $this->driver->sqlstate();
  }
  /**
   * 
   */
  public function affectedRows(): void
  {
    $this->driver->affectedRows();
  }
}
