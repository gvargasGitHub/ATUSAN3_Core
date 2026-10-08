<?php
namespace Atusan\Persistence;

use Atusan\Exceptions\DBConnectionException;

class MySQLOdbcDriver extends DBDriverBase
{
  protected mixed $odbc;

  /**
   * Connect
   * @param string $host
   * @param string $user
   * @param string $pass
   */
  public function connect(
    string $host, 
    string  $user, 
    string $pass, 
    ?string $db, 
    ?bool $ssl): static
  {
    $this->odbc = @odbc_connect(
      $host,
      $user,
      $pass
    );

    if (!$this->odbc) {
      $errormsg = odbc_errormsg();

      $odbc_error = (mb_check_encoding($errormsg, 'utf-8'))
        ? $errormsg
        : mb_convert_encoding($errormsg, 'UTF-8');

      # en caso de error, dispara la excepcion
      $error_text = "No se pudo establecer la conexión con al base de datos.\n"
        . "Revise que el servicio esté en operación y que los parámetros estén correctos.\n"
        . $odbc_error;

      throw new DBConnectionException($error_text);
    }

    return $this;
  }

  /**
   * Close
   */
  public function close(): void
  {
    odbc_close($this->odbc);
  }

  /**
   * Query
   * @param string $sql
   * @param array $values
   * @return array
    */
  public function query(
    string $sql, 
    array $values = []): array
  {
    if (count($values) !== preg_match_all('/\?/', $sql))
      throw new DBConnectionException('El número de valores no coincide con los parámetros de la consulta');

    $stmt = odbc_prepare($this->odbc, $sql);
    if (!$stmt) {
      throw new DBConnectionException('Error al preparar la consulta: ' . odbc_errormsg($this->odbc));
    }

    $result = odbc_execute($stmt, $values);
    if (!$result) {
      throw new DBConnectionException('Error al ejecutar la consulta: ' . odbc_errormsg($this->odbc));
    }

    $results = [];
    while ($row = odbc_fetch_array($stmt)) {
      $results[] = $row;
    }

    odbc_free_result($stmt);

    return $results;
  }

  /**
   * Execute
   * @param string $sql
   * @param array $values
   * @return bool
   */
  public function execute(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  /**
   * Exec
   * @param string $sql
   * @param array $values
   * @return bool
   */
  public function exec(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  /**
   * Routine
   * @param string $sql
   * @param array $values
   * @param array $outvars
   * @param array $outtypes
   * @return array
   */
  public function routine(
    string $sql, 
    array $values = [], 
    array $outvars = [], 
    array $outtypes = []): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $this->query("call {$sql}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ")", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    return $results[0];
  }

  /**
   * Procedure Outs
   * @param string $procedureName
   * @param array $values
   * @param array $outvars
   * @param array $outtypes
   * @return array
   */
  public function procedureOuts(
    string $procedureName, 
    array $values = [], 
    array $outvars = [], 
    array $outtypes = []): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $this->query("call {$procedureName}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ")", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    return $results[0];
  }

  /**
   * Procedure Result
   * @param string $procedureName
   * @param array $values
   * @return array
   */

  public function procedureResult(
    string $procedureName, 
    array $values = []): array
  {
    $results = $this->query("call {$procedureName}(" . implode(',', array_fill(0, count($values), '?')) . ");", $values);

    if (!$results) {
      throw new DBConnectionException("Error al ejecutar el procedimiento almacenado: {$procedureName}");
    }

    return $results;
  }

  /**
   * Autocommit
   * @param bool $mode
   * @return bool
   */
  public function autocommit(bool $mode = true): bool
  {
    return odbc_autocommit($this->odbc, $mode);
  }

  public function commit(): bool
  {
    return odbc_commit($this->odbc);
  }

  public function rollback(): bool
  {
    return odbc_rollback($this->odbc);
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
    return odbc_error($this->odbc);
  }

  public function errorMessage(): string
  {
    return odbc_errormsg($this->odbc);
  }
}