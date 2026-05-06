<?php

namespace Atusan\Iterators;

use Atusan\Types\ComponentSourceType;
use Atusan\XML\XMLExtended;

class ComponentSourcesIterator implements BaseIterator
{
  private int $position = 0;
  private array $data = [];

  function __construct()
  {
    $this->position = 0;
  }

  public function add(string $sourceName, XMLExtended $source)
  {
    array_push($this->data, new ComponentSourceType($sourceName, $source));
  }

  /**
   * Get By Position
   * Retorna el elemento XMLExtend de la posición requerida o "false".
   */
  public function getByPosition(int $position): XMLExtended | null
  {
    return $this->data[$position]->xml ?? null;
  }
  /**
   * Get By Name
   * Retorna el elemento XMLExtend de la primera ocurrencia o "false"
   * en caso de no existir coincidencia.
   */
  public function getByName(string $sourceName): XMLExtended | null
  {
    foreach ($this as $source) if ($source->name == $sourceName) return $source->xml;

    return null;
  }

  /**
   * Get All By Name
   */
  public function getAllByName(string $sourceName): ComponentSourcesIterator
  {
    $collection = new ComponentSourcesIterator;

    foreach ($this as $source) if ($source->name == $sourceName) $collection->add($source->name, $source->xml);

    return $collection;
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
  public function current(): ComponentSourceType
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
