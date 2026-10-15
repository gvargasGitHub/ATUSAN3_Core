<?php

namespace Atusan\Persistence;

use Atusan\Exceptions\DBConnectionException;
use mysqli;
use mysqli_sql_exception;

class MySQLDBDriver extends DBDriverBase
{

  protected mysqli $mysqli;

  /**
   * Connect
   */
  public function connect(
    string $host, 
    string  $user, 
    string $pass, 
    string $database, 
    ?bool $ssl): static
  {
    // Siempre se debe activar el informe de errores para mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $this->mysqli = mysqli_init();
        
	  $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
	  
    $clientFlag = ($ssl === true) ? MYSQLI_CLIENT_SSL : 0;

    try {
      $this->mysqli->real_connect($host, $user, $pass, $database, NULL,
          NULL, $clientFlag);

      $this->mysqli->query("SET NAMES utf8mb4");

      $this->mysqli->query("SET lc_time_names='es_MX'");
    } catch (mysqli_sql_exception $ex) {
      throw new DBConnectionException($ex->getMessage());
    }

    return $this;
  }

  /**
   * Close
   */
  public function close(): void
  {
    $this->mysqli->close();
  }

  /**
   * Query
   * Se encarga de ejecutar la consulta SQL y devolver el resultado en un arreglo asociativo.
   * @param string $sql La consulta SQL a ejecutar.
   * @param array $values Los valores a utilizar en la consulta.
   * @return array Un arreglo asociativo con los resultados de la consulta.
   * 
   * Nota: Esta función utiliza consultas preparadas para prevenir SQL Injection.
   * Nota: Propuesta por ChatGPT.
   */
  public function query(
    string $sql, 
    array $values = []): array
  {
      if (count($values) !== substr_count($sql, '?')) {
          throw new DBConnectionException(
              'El número de valores no coincide con los parámetros.'
          );
      }

      $stmt = null;
      $result = null;

      try {
          if ($values !== []) {
              $stmt = $this->mysqli->prepare($sql);

              // Enviar los valores sin modificar su contenido.
              $params = array_values($values);
              $types = str_repeat('s', count($params));

              $stmt->bind_param($types, ...$params);
              $stmt->execute();

              $result = $stmt->get_result();
          } else {
              $result = $this->mysqli->query(
                  $sql,
                  MYSQLI_STORE_RESULT
              );
          }

          $rows = $result instanceof \mysqli_result
              ? $result->fetch_all(MYSQLI_ASSOC)
              : [];

          if ($result instanceof \mysqli_result) {
              $result->free();
              $result = null;
          }

          if ($stmt !== null) {
              // Consumir resultados adicionales de CALL,
              // incluso cuando el primer resultado esté vacío.
              while ($stmt->more_results()) {
                  $stmt->next_result();

                  $extra = $stmt->get_result();

                  if ($extra instanceof \mysqli_result) {
                      $extra->free();
                  }
              }
          } else {
              $this->clearStoredProcedure();
          }

          return $rows;
      } catch (mysqli_sql_exception $ex) {
          throw new DBConnectionException($ex->getMessage());
      } finally {
          if ($result instanceof \mysqli_result) {
              $result->free();
          }

          if ($stmt !== null) {
              $stmt->close();
          }
      }
  }

  public function queryObsoleto(
    string $sql, 
    array $values = []): array
  {
    if (count($values) !== preg_match_all('/\?/', $sql))
      new DBConnectionException('El número de valores no coincide con los parámetros de la consulta');

    if (count($values) > 0) {
      $p = [];
      for ($i = 0; $i < count($values); $i++) {
        $p[$i] = ($this->checkDataType($values[$i]) == 'string')
          ? $this->buildStringValue($this->mysqli->real_escape_string($values[$i]))
          : $values[$i];
      }

      for ($i = 0; $i < count($p); $i++) $sql = preg_replace('/\?/', $p[$i], $sql, 1);
    }
    $result = [];
    try {
      // devuelve objeto MySQL_Result para conjunto de resultados,
      // false en caso de fallo y true para consultas exitosas
      $q = $this->mysqli->query($sql, MYSQLI_STORE_RESULT);

      if ($q === true) return [];
      if ($q === false) new DBConnectionException($this->mysqli->error);

      
      while ($r = $q->fetch_assoc()) {
        array_push($result, $r);

        $this->clearStoredProcedure();
      }

      $q->free();
    } catch (mysqli_sql_exception $ex) {
      throw new DBConnectionException($ex->getMessage());
    }

    return $result;
  }

  /**
   * Execute
   */
  public function execute(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  /**
   * Exec
   */
  public function exec(string $sql, array $values = []): bool
  {
    return count($this->query($sql, $values)) === 0;
  }

  /**
   * Routine
   */
  public function routine(
    string $sql, 
    array $values = [], 
    array $outvars = [], 
    array $outvarstypes = []): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $this->query("call {$sql}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ")", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    return $results[0];
  }

  /**
   * Procedure Outs
   */
  public function procedureOuts(
    string $procedureName, 
    array $values = [], 
    array $outvars = [], 
    array $outvarstypes = []
  ): array
  {
    $fov = array_merge(['@err_flag', '@err_text'], $outvars);

    $this->query("call {$procedureName}(" . implode(',', array_merge(array_fill(0, count($values), '?'), $fov)) . ")", $values);

    $results = $this->query('SELECT ' . implode(',', $fov));

    return $results[0];
  }

  /**
   * Procedure Result
   */
  public function procedureResult(
    string $procedureName, 
    array $values = []
  ): array
  {
    $results = $this->query("call {$procedureName}(" . implode(',', array_fill(0, count($values), '?')) . ")", $values);

    if (!$results) {
      throw new DBConnectionException("Error al ejecutar el procedimiento almacenado: {$procedureName}");
    }

    return $results[0];
  }

  /**
   * Autocommit
   */
  public function autocommit(bool $mode = true): bool
  {
    return $this->mysqli->autocommit($mode);
  }

  public function commit(): bool
  {
    return $this->mysqli->commit();
  }

  public function rollback(): bool
  {
    return $this->mysqli->rollback();
  }

  public function sqlstate(): string
  {
    return $this->mysqli->sqlstate;
  }

  public function affectedRows(): string
  {
    return $this->mysqli->affected_rows;
  }

  public function errorCode(): string
  {
    return $this->mysqli->errno;
  }

  public function errorMessage(): string
  {
    return $this->mysqli->error;
  }

  private function clearStoredProcedure(): void
  {
    while ($this->mysqli->more_results() && $l_result = $this->mysqli->next_result()) {
      if ($l_result = $this->mysqli->store_result()) $l_result->free();
    }
  }

  /**
   * buildStringValue
   * Si el tipo del parametro es una cadena pasara por la validación
   * de formato de fecha y resolverá el valor de retorno.
   * Esta función previene SQL Injection ya que cualquier contenido
   * de la cadena es encapsulado dentro de las comillas.
   */
  private function buildStringValue(string $value): string
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
   * Check Data Type
   * Esta función determina el tipo de dato del parámetro para completar
   * el argumento de tipos que requiere la función mysqli_stmt_bind_param.
   * @param mixed $value
   */
  private function checkDataType(mixed $value): string
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
