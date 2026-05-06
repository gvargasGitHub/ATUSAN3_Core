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

  /** */
  public function write(): void
  {
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/view.php';
  }
}
