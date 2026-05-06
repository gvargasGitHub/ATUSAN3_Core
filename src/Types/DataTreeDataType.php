<?php

namespace Atusan\Types;

class DataTreeDataType
{
  public array $children = [];

  function __construct(public int $index, public int $level, public array $data) {}

  function hasChildren(): bool
  {
    return (count($this->children) > 0);
  }
}
