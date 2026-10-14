<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitDataForm;

class DataForm extends DataViewBase
{
  use TraitDataForm;

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
    foreach ($this->components as $component) 
      if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) 
        $component->setParent($this);
  }
}
