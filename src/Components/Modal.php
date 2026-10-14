<?php

namespace Atusan\Components;

class Modal extends ComponentNest
{

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * Set Sources
   */
  protected function setSources(): void
  {
    $this->addSource('modal', $this->xml);
  }

  /**
   * Final Definitions
   */
  protected function finalDefinitions(): void {}

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------
  /**
   * make
   * @return string
   */
  public function make(): string
  {
    if (!$this->validateProperty('title')) $this->set('title', '');
    if (!$this->validateProperty('footer')) $this->set('footer', '');

    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'modal/view.php';
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
