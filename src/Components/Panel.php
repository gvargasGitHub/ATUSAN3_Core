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
        foreach ($source->xml->children() as $child) $this->components->getByName($child->getAttribute('name'))->write();
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

  /**
   * 
   */
  public function write(): void
  {
    include __DIR__ . DS . 'Views' . DS . 'panel/view.php';
  }
}
