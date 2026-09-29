<?php

namespace Atusan\Persistence;

class DBConnection
{
  // protected DBDriverInterface $driver;

  /**
   * connect
   * @param string $driver
   * @param string $host
   * @param string $user
   * @param string $pass
   * @param string $database
   * @param ?bool $ssl
   * 
   * @return DBDriverInterface
   */
  public static function connect(
    string $driver, 
    string $host, 
    string $user, 
    string $pass,
    string $database, 
    ?bool $ssl = false): DBDriverInterface
  {
    $resource = DBConnection::createDriver($driver);

    $resource->connect($host, $user, $pass, $database, $ssl);

    return $resource;
  }

  /**
   * Get New
   */
  public static function getNew() : static
  {
    return new static();
  }

  /**
   * 
   */
  public static function createDriver(string $driverName) : DBDriverInterface
  {
    $driverClassName = "Atusan\\Persistence\\{$driverName}DBDriver";

    return $driverClassName::getNew();
  }
  // /**
  //  * 
  //  */
  // public function close(): void
  // {
  //   if (is_a($this->driver, 'Atusan\\Persistence\\DBDriverInterface')) $this->driver->close();
  // }

  // /**
  //  * 
  //  */
  // public function query(string $sql, array $params = []): array
  // {
  //   return $this->driver->query($sql, $params);
  // }
  // /**
  //  * 
  //  */
  // public function execute(string $sql, array $params = []): bool
  // {
  //   return $this->driver->execute($sql, $params);
  // }
  // /**
  //  * 
  //  */
  // public function routine(string $sql, array $params = [], array $outvars = [], array $outvarstypes = []): array
  // {
  //   return $this->driver->routine($sql, $params, $outvars, $outvarstypes);
  // }
  // /**
  //  * 
  //  */
  // public function autocommit(bool $mode): void
  // {
  //   $this->driver->autocommit($mode);
  // }
  // /**
  //  * 
  //  */
  // public function commit(): void
  // {
  //   $this->driver->commit();
  // }
  // /**
  //  * 
  //  */
  // public function rollback(): void
  // {
  //   $this->driver->rollback();
  // }
  // /**
  //  * 
  //  */
  // public function sqlstate(): void
  // {
  //   $this->driver->sqlstate();
  // }
  // /**
  //  * 
  //  */
  // public function affectedRows(): void
  // {
  //   $this->driver->affectedRows();
  // }
}
