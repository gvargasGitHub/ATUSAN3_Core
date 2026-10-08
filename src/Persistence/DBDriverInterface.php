<?php

namespace Atusan\Persistence;

interface DBDriverInterface
{
  /**
   * Connect
   */
  public function connect(
    string $host, 
    string  $user, 
    string $pass, 
    string $db, 
    ?bool $ssl): static;

  /** 
   * Close
   */
  public function close(): void;

  /**
   * Query
   */
  public function query(
    string $sql, 
    array $values = []): array;

  /**
   * Execute
   */
  public function execute(
    string $sql, 
    array $values = []): bool;

  /**
   * Exec
   */
  public function exec(
    string $sql, 
    array $values = []
  ):bool;

  /**
   * Routine
   */
  public function routine(
    string $sql, 
    array $values = [], 
    array $outvars = [], 
    array $outvarstypes = []): array;

  /**
   * Procedure Outs
   */
  public function procedureOuts(
    string $procedureName, 
    array $values = [], 
    array $outvars = [], 
    array $outvarstypes = []
  ): array;

  /**
   * Procedure Result
   */
  public function procedureResult(
    string $procedureName, 
    array $values = [],
  ): array;

  /**
   * Autocommit
   * @param bool $mode
   */
  public function autocommit(
    bool $mode = true): bool;

  public function commit(): bool;

  public function rollback(): bool;

  public function sqlstate(): string;

  public function affectedRows(): string;

  public function errorCode(): string;

  public function errorMessage(): string;
}
