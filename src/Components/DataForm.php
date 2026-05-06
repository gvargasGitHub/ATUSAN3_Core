<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitDataForm;

class DataForm extends DataViewBase
{
  use TraitDataForm;

  /**
   * 
   */
  protected function setType()
  {
    $this->type = 'DataForm';
  }

  /**
   * Init DataView Properties
   */
  protected function initProperties()
  {
    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
    $this->addDataAndCsrf();
  }
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
    $this->data[0] = array_merge($this->data[0], $data);

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

    $this->addSource('form', $this->xml);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setParent($this);
  }

  /**
   * 
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = '';
    if (!property_exists($this, 'footer')) $this->footer = '';
    if (!property_exists($this, 'route')) $this->route = $_SERVER['REQUEST_URI'];

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
  }
}
