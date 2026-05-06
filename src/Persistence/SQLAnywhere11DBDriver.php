<?php

namespace Atusan\Persistence;

class SQLAnywhere11DBDriver extends DBDriverBase
{
  protected $affectedRows = 0;

  public function connect(): void
  {
    $this->conn = @odbc_connect(
      "Driver={SQL Anywhere 11};CommLinks=tcpip(Host={$this->host});ServerName={$this->db};CharSet=UTF-8;",
      $this->user,
      $this->pass
    );

    if (!$this->conn) {
      $errormsg = odbc_errormsg();

      $odbc_error = (mb_check_encoding($errormsg, 'utf-8'))
        ? $errormsg
        : mb_convert_encoding($errormsg, 'UTF-8');

      # 2019-02-21: en caso de error, dispara la excepcion
      $error_text = "No se pudo establecer la conexión con al base de datos.\n"
        . "Revise que el servicio esté en operación y que los parámetros estén correctos.\n"
        . $odbc_error;

      throw new DBDriverException($error_text);
    }
  }

  public function close(): void
  {
    odbc_close($this->conn);
  }

  public function query(string $sql, array $values = []): array
  {
    if (count($values) !== preg_match_all('/\?/', $sql))
      throw new DBDriverException('El número de valores no coincide con los parámetros de la consulta');

    if (count($values) > 0) {
      for ($i = 0; $i < count($values); $i++) {
        if (preg_match('/^[0-9]{2}(\/|\-)[0-9]{2}(\/|\-)[0-9]{4}$/', $values[$i])) {
          $values[$i] = substr($values[$i], 6)
            . '-' . substr($values[$i], 3, 2)
            . '-' . substr($values[$i], 0, 2);
        }
      }
    }
    $stmt = @odbc_prepare($this->conn, $sql);

    if ($stmt === false)
      throw new DBDriverException(odbc_errormsg($this->conn));

    $res = @odbc_execute($stmt, $values);
    if (!$res)
      throw new DBDriverException(odbc_errormsg($this->conn));

    $this->affectedRows = odbc_num_rows($stmt) & 0xffffffff;

    $results = [];
    while ($r = @odbc_fetch_array($stmt)) array_push($results, $r);

    odbc_free_result($stmt);

    return $results;
  }

  public function execute(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  public function routine(string $sql, array $values = [], $outvars = [], $outtypes = []): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $typ = array_merge(['BIT', 'VARCHAR(128)'], $outtypes);

    $s = '';
    for ($i = 0; $i < count($fov); $i++) $s .= "create variable {$fov[$i]} {$typ[$i]}; ";

    $this->query($s . "call {$sql}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ");", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    if ($results[0]['@err_flag'] == 0)
      new DBDriverException($results[0]['@err_text']);

    $s = '';
    for ($i = 0; $i < count($fov); $i++) $s .= "drop variable {$fov[$i]}; ";
    $this->query($s);

    return $results[0];
  }

  public function autocommit($mode = true): bool
  {
    return odbc_autocommit($this->conn, $mode);
  }

  public function commit(): bool
  {
    return odbc_commit($this->conn);
  }

  public function rollback(): bool
  {
    return odbc_rollback($this->conn);
  }

  public function sqlstate(): string
  {
    return '';
  }

  public function affectedRows(): string
  {
    return $this->affectedRows;
  }

  public function errorCode(): string
  {
    return odbc_error($this->conn);
  }

  public function errorMessage(): string
  {
    return odbc_errormsg($this->conn);
  }
}
