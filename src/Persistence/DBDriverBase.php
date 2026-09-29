<?php

namespace Atusan\Persistence;

abstract class DBDriverBase implements DBDriverInterface
{
  protected int $affectedRows = 0;

  protected string $port;
  protected bool $ssl;

  /** 
   * Get New
   * 
   */
  public static function getNew(): static
  {
    return new static();
  }

}
