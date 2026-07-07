<?php

namespace Atusan\Components;

class TabGroup extends ComponentNest
{
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->addSource('tabgroup', $this->xml);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $content) $content->setParent($this);
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
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/view.php';
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
