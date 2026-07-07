<?php

namespace Atusan\Components;

class Panel extends ComponentNest
{
  /**
   * Write Panel
   * Incluye la vista establecida o recorre el "diccionario" de secciones/componentes.
   */
  protected function writePanel(string $position): void
  {
    foreach ($this->sources->getAllByName($position) as $source) {
      if ($source->xml->hasAttribute('view'))
        include $this->owner->getDirectory() . DS . "{$source->xml->getAttribute('view')}.php";
      else
        foreach ($source->xml->children() as $child) echo $this->components->getByName($child->getAttribute('name'))->make();
    }
  }
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // Integra las secciones Left, Content & Right
    foreach ($this->xml->children() as $section) $this->addSource((string) $section->getName(), $section);
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
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'panel/view.php';
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
