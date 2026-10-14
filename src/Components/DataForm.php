<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitDataForm;

class DataForm extends DataViewBase
{
  use TraitDataForm;

  // /**
  //  * 
  //  */
  // protected function setType()
  // {
  //   $this->type = 'DataForm';
  // }

  // /**
  //  * Init DataView Properties
  //  */
  // protected function initProperties()
  // {
  //   // El desarrollador deberá generar, asignar y validar
  //   // manualmente el token a este control mediante el
  //   // método "setCsrf(string)"
  //   $this->addDataAndCsrf();
  // }

  // /**
  //  * Build
  //  */
  // public function build(): string
  // {
  //   ob_start();

  //   include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/body.view.php';

  //   $html = ob_get_contents();

  //   ob_clean();

  //   return $html;
  // }
  // ----------------------------------
  //  DataViewBase
  // ----------------------------------

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // $this->setType();

    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
    $this->addDataAndCsrf();

    $this->addSource('form', $this->xml);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setParent($this);
  }

  // // ----------------------------------
  // //  ComponentInterface
  // // ----------------------------------
  // /**
  //  * make
  //  * @return string
  //  */
  // public function make(): string
  // {
  //   if (!$this->validateProperty('title')) $this->set('title', '');
  //   if (!$this->validateProperty('footer')) $this->set('footer', '');

  //   ob_start();
  //   include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
  //   return ob_get_clean();
  // }

  // /**
  //  * write
  //  * @return void
  //  */
  // public function write(): void
  // {
  //   echo $this->make();
  // }
}
