<?php

namespace Atusan\Components;

abstract class ButtonGroupBase extends Component
{
  protected string $type;

  protected array $css_classes = [];

  function __construct($owner, $xml)
  {
    parent::__construct($owner, $xml);

    $this->type = $this->defineGroupType();

    array_push($this->css_classes, 'ats-btn-group-' . $this->type);

    $this->finalDefinitions();
  }

  abstract protected function defineGroupType(): string;

  abstract protected function finalDefinitions(): void;

  protected function addCssClass(string $class): void
  {
    array_push($this->css_classes, $class);
  }

  protected function printCssClass(): string
  {
    return implode(' ', $this->css_classes);
  }

  public function write(): void
  {
    include __DIR__ . DS . 'Views' . DS . 'buttongroup/view.php';
  }
}
