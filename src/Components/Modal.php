<?php

namespace Atusan\Components;

class Modal extends ComponentNest
{
  public $title;

  public $footer;

  public $view;

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->addSource('modal', $this->xml);
  }

  /**
   * 
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
    if (!property_exists($this, 'title')) $this->title = '';
    if (!property_exists($this, 'footer')) $this->footer = '';

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
