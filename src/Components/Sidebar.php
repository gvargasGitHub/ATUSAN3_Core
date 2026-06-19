<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitNavbar;

class Sidebar extends ComponentNest
{
  use TraitNavbar;
  /* ------------------------
  Properties
  ------------------------ */
  public string $type = 'Sidebar';
  
   // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // Integra las secciones Top & Bottom
    foreach ($this->xml->children() as $section) $this->addSource((string) $section->getName(), $section);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) $component->setParent($this);
  }

  /**
   * Write :: TraitComponent
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = "";
    include __DIR__ . DS . 'Views' . DS . 'sidebar/view.php';
  }
}
