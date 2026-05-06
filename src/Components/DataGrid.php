<?php

namespace Atusan\Components;

class DataGrid extends DataViewBase
{
  /**
   * 
   */
  protected function setType()
  {
    $this->type = 'DataGrid';
  }

  /**
   * Init DataView Properties
   */
  protected function initProperties() {}

  /**
   * Build
   */
  public function build(): string
  {
    ob_start();

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/body.view.php';

    $html = ob_get_contents();

    ob_clean();

    return $html;
  }

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
    $this->setType();

    $this->initProperties();

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
    foreach ($this->components as $component) if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setParent($this);
  }

  /**
   * 
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = '';
    if (!property_exists($this, 'footer')) $this->footer = '';

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
  }
}
