<?php

namespace Atusan\Iterators;

class OptionTextValueIterator implements BaseIterator
{
  private $position = 0;

  public function __construct(private $data = [])
  {
    $this->position = 0;
  }

  public function add(string $text, string $value)
  {
    array_push($this->data, ['text'=>$text, 'value'=>$value]);
  }

  // ----------------------------------
  // BaseIterator Implementation
  // ----------------------------------
  public function count(): int
  {
    return count($this->data);
  }

  
  // ----------------------------------
  // Iterator Implementation
  // ----------------------------------
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
