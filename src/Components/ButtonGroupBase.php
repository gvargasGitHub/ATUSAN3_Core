<?php

namespace Atusan\Components;

use Atusan\Controller\Module;
use Atusan\XML\XMLExtended;

abstract class ButtonGroupBase extends Component
{
  protected string $type;

  protected array $css_classes = [];

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->type = $this->defineGroupType();

    array_push($this->css_classes, 'ats-btn-group-' . $this->type);

    $this->finalDefinitions();
  }

  // ----------------------------------
  //  Abstract Methods
  // ----------------------------------
  abstract protected function defineGroupType(): string;

  abstract protected function finalDefinitions(): void;

  /**
   * Adds a CSS class to the button group.
   */
  protected function addCssClass(string $class): void
  {
    array_push($this->css_classes, $class);
  }

  protected function printCssClass(): string
  {
    return implode(' ', $this->css_classes);
  }

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------
  /**
   * make
   * @return string
   */
  public function make(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'buttongroup/view.php';
    return ob_get_clean();
  }

  /**
   * write
   * @return void
   */
  public function write(): void
  {
    echo $this->make();
  }
}
