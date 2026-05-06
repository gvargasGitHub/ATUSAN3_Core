<?php

namespace Atusan\Iterators;

use Atusan\Components\ComponentInterface;
use Atusan\Controller\ModuleNested;

class ComponentsIterator implements BaseIterator
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
  public function add(ComponentInterface|ModuleNested $component)
  {
    array_push($this->data, $component);
  }

  /**
   * 
   */
  public function getByName(string $name): ComponentInterface | null
  {
    foreach ($this as $component) if ($component->name == $name) return $component;

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
  public function current(): ComponentInterface|ModuleNested
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
