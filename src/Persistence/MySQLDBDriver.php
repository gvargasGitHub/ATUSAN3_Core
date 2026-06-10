<?php

namespace Atusan\Persistence;

use mysqli;

class MySQLDBDriver extends DBDriverBase
{
  public function connect(): void
  {
    // Siempre se debe activar el informe de errores para mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

    $this->conn->query("SET NAMES utf8mb4");

    $this->conn->query("SET lc_time_names='es_MX'");
  }

  public function close(): void
  {
    $this->conn->close();
  }

  public function query(string $sql, array $values = []): array
  {
    if (count($values) !== preg_match_all('/\?/', $sql))
      new DBDriverException('El número de valores no coincide con los parámetros de la consulta');

    if (count($values) > 0) {
      $p = [];
      for ($i = 0; $i < count($values); $i++) {
        $p[$i] = ($this->checkDataType($values[$i]) == 'string')
          ? $this->buildStringValue($this->conn->real_escape_string($values[$i]))
          : $values[$i];
      }

      for ($i = 0; $i < count($p); $i++) $sql = preg_replace('/\?/', $p[$i], $sql, 1);
    }

    // devuelve objeto MySQL_Result para conjunto de resultados,
    // false en caso de fallo y true para consultas exitosas
    $q = $this->conn->query($sql, MYSQLI_STORE_RESULT);

    if ($q === true) return [];
    if ($q === false) new DBDriverException($this->conn->error);

    $result = [];
    while ($r = $q->fetch_assoc()) {
      array_push($result, $r);

      $this->clearStoredProcedure();
    }

    $q->free();

    return $result;
  }

  public function execute(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  public function routine(string $sql, array $values = [], $outvars = []): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $this->query("call {$sql}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ")", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    return $results[0];
  }



  public function autocommit(bool $mode = true): bool
  {
    return $this->conn->autocommit($mode);
  }

  public function commit(): bool
  {
    return $this->conn->commit();
  }

  public function rollback(): bool
  {
    return $this->conn->rollback();
  }

  public function sqlstate(): string
  {
    return $this->conn->sqlstate;
  }

  public function affectedRows(): string
  {
    return $this->conn->affected_rows;
  }

  public function errorCode(): string
  {
    return $this->conn->errno;
  }

  public function errorMessage(): string
  {
    return $this->conn->error;
  }

  private function clearStoredProcedure(): void
  {
    while ($this->conn->more_results() && $l_result = $this->conn->next_result()) {
      if ($l_result = $this->conn->store_result()) $l_result->free();
    }
  }

  /**
   * buildStringValue
   * Si el tipo del parametro es una cadena pasara por la validación
   * de formato de fecha y resolverá el valor de retorno.
   * Esta función previene SQL Injection ya que cualquier contenido
   * de la cadena es encapsulado dentro de las comillas.
   */
  private function buildStringValue($value)
  {
    # si el parámetro es de tipo date, los pasa por filtros de formato
    # para convertirlo al formato correcto
    if (preg_match("/^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/", $value))
      return "'" . substr($value, 6) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . "'";
    elseif (preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $value))
      return "'" . substr($value, 6) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . "'";
    elseif (preg_match("/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/", $value))
      return "'" . substr($value, 6) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . "'";
    elseif (preg_match("/\?/", $value))
      return preg_replace("/\?/", '_', $value);
    else
      return "'{$value}'";
  }

  /**
   * 
   */
  private function checkDataType($value)
  {
    if (is_array($value))
      return 'array';
    if (is_bool($value))
      return 'boolean';
    if (is_float($value))
      return 'float';
    if ($value == (string)(float)$value)
      return 'float';
    if (is_int($value))
      return 'integer';
    if (is_null($value))
      return 'null';
    if (is_numeric($value))
      return 'numeric';
    if (is_object($value))
      return 'object';
    if (is_resource($value))
      return 'resource';
    if (is_string($value))
      return 'string';

    return 'unknow';
  }
}
