<?php

namespace Atusan\Persistence;

abstract class DBDriverBase implements DBDriverInterface
{
  protected $conn;
  protected int $affectedRows;

  protected string $port;
  protected bool $ssl;

}
