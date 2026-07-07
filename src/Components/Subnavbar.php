<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitNavbar;

class Subnavbar extends ComponentNest
{
  use TraitNavbar;
  /* ------------------------
  Properties
  ------------------------ */
  public string $type = 'Subnavbar';
  
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // Integra las secciones Left & Right
    foreach ($this->xml->children() as $section) $this->addSource((string) $section->getName(), $section);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) $component->setParent($this);
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
    if (!property_exists($this, 'title')) $this->title = "";
    ob_start(); 
    include __DIR__ . DS . 'Views' . DS . 'subnavbar/view.php';
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
