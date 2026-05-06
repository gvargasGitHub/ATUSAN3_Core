<?php

namespace Atusan\Types;

use Iterator;

class ControlSelectOptionType implements Iterator
{
  private $position = 0;

  public function __construct(private $data = [])
  {
    $this->position = 0;
  }

  public function current(): mixed
  {
    return $this->data[$this->position];
  }

  public function key(): mixed
  {
    return $this->position;
  }

  public function next(): void
  {
    ++$this->position;
  }

  public function rewind(): void
  {
    $this->position = 0;
  }

  public function valid(): bool
  {
    return isset($this->data[$this->position]);
  }
}
