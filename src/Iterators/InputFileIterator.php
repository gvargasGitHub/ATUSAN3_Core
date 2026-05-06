<?php

namespace Atusan\Iterators;

use Atusan\Types\FileUploadedType;

class InputFileIterator implements BaseIterator
{
  private int $position = 0;
  private array $data = [];

  function __construct()
  {
    $this->position = 0;
  }

  /**
   * 
   */
  public function add(FileUploadedType $file)
  {
    array_push($this->data, $file);
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
  public function rewind(): void
  {
    $this->position = 0;
  }

  // #[\ReturnTypeWillChange]
  public function current(): FileUploadedType
  {
    return $this->data[$this->position];
  }

  // #[\ReturnTypeWillChange]
  public function key(): int
  {
    return $this->position;
  }

  public function next(): void
  {
    ++$this->position;
  }

  public function valid(): bool
  {
    return isset($this->data[$this->position]);
  }
}
