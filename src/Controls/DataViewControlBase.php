<?php

namespace Atusan\Controls;

use Atusan\Controller\Module;
use Atusan\Iterators\OptionTextValueIterator;
use Atusan\XML\XMLExtended;

abstract class DataViewControlBase extends ControlBase
{
  protected array $data;

  protected int $row;

  /**
   * 
   */
  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    // Obtiene "type" del "Control"
    $this->set('type', substr($this->xml->getName(), 7));
  }

  abstract function getId(): string;
  
  public function getData(): array
  {
    return $this->data;
  }

  public function setData(array $data): void
  {
    $this->data = $data;
  }

  public function getValue(): mixed
  {
    return $this->data[$this->get('id')] ?? null;
  }

  public function getRow(): int
  {
    return $this->row;
  }

  public function setRow(int $row): void
  {
    $this->row = $row + 1;
  }

  public function setEnable(bool $enable)
  {
    $this->xml->setAttribute('html:disabled', 'html', $enable ? '' : 'disabled');
  }

  public function createList(OptionTextValueIterator $options): void
  {
    $this->xml->removeChildren();
    
    foreach ($options as $option) {
      $child = $this->xml->addChild('Option');
      $child->addAttribute('value', $option['value']);
      $child->addAttribute('text', $option['text']);
    }
  }
}
