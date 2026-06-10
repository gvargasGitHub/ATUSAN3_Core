<?php

namespace Atusan\Persistence;

abstract class DBDriverBase implements DBDriverInterface
{
  protected $conn;
  protected int $affectedRows;

  function __construct(protected string $host, protected string $user, protected string $pass, protected string $db) {}
}
