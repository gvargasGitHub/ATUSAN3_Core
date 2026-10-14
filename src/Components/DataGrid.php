<?php

namespace Atusan\Components;

class DataGrid extends DataViewBase
{
  // ----------------------------------
  //  DataViewBase
  // ----------------------------------
  
  /**
   * Import
   */
  public function import(array $data): int
  {
    $this->data = (is_array($data) && array_diff_key($data, array_keys($data)))
      ? [$data] : $data;

    return count($this->data);
  }

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // Recorre las secciones Thead, Tbody, Tsummary & Tfoot
    foreach ($this->xml->children() as $section) {
      $sectioName = (string) $section->getName();

      $this->addSource($sectioName, $section);
    }
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    // Recorre cada Componente para localizar Controles y asignarles el "Padre"
    foreach ($this->components as $component) 
      if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) 
        $component->setParent($this);
  }
}
