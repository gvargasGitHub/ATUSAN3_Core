<?php

namespace Atusan\Persistence;

class DBConnection
{
  protected $driver;

  static public function connect(string $driver, string $host, string  $user, string $pass, ?string $db): DBConnection
  {
    $obj = new DBConnection();

    $class = "Atusan\\Persistence\\{$driver}DBDriver";

    $obj->driver = new $class($host, $user, $pass, $db);

    try {
      $obj->driver->connect();

      return $obj;
    } catch (\Exception $ex) {
      trigger_error($ex->getMessage(), E_USER_ERROR);
    }
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
  public function routine(string $sql, array $params = []): array
  {
    return $this->driver->routine($sql, $params);
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
