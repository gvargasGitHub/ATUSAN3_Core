<?php

namespace Atusan\Controls;

use Atusan\Controller\Module;
use Atusan\Types\ControlSelectOptionType;
use Atusan\XML\XMLExtended;

abstract class DataViewControlBase extends Control
{

  /**
   * @var
   * String $type
   */
  protected string $type;

  protected array $data;

  protected int $row;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    // Obtiene "type" del "Control"
    $this->type = substr($this->xml->getName(), 7);
  }

  abstract function getId(): string;

  public function getType(): string
  {
    return $this->type;
  }

  public function setType(string $type): void
  {
    $this->type = $type;
  }

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
    return $this->data[$this->name] ?? null;
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

  public function createList(ControlSelectOptionType $options): void
  {
    foreach ($options as $option) {
      $child = $this->xml->addChild('Option');
      $child->addAttribute('value', $option['value']);
      $child->addAttribute('text', $option['text']);
    }
  }
}
