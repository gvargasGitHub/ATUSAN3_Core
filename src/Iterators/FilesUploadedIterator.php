<?php

namespace Atusan\Iterators;

use Atusan\Types\InputFileType;

class FilesUploadedIterator implements BaseIterator
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
  public function add(string $inputName)
  {
    array_push($this->data, new InputFileType($inputName, new InputFileIterator()));
  }

  /**
   * __get
   * Permite acceder a los elementos "input" mediante "->$inputName"
   */
  function __get($name): InputFileIterator | null
  {
    foreach ($this->data as $input)
      if ($input->name == $name) return $input->files;

    return null;
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
  public function current(): InputFileType
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
