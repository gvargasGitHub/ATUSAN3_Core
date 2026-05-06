<?php

namespace Atusan\Persistence;

interface DBDriverInterface
{

  public function connect(): void;

  public function close(): void;

  public function query(string $sql, array $values = []): array;

  public function execute(string $sql, array $values = []): bool;

  public function routine(string $sql, array $values = [], $outvars = []): array;

  public function autocommit(bool $mode = true): bool;

  public function commit(): bool;

  public function rollback(): bool;

  public function sqlstate(): string;

  public function affectedRows(): string;

  public function errorCode(): string;

  public function errorMessage(): string;
}
