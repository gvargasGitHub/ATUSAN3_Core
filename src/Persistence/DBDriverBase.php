<?php

namespace Atusan\Persistence;

abstract class DBDriverBase implements DBDriverInterface
{
  protected $conn;
  protected $affectedRows;

  function __construct(protected $host, protected $user, protected $pass, protected $db) {}
}
