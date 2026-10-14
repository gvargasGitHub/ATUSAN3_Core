<?php

namespace Atusan\Controls;

use Atusan\Components\Component;
use Atusan\Controller\Module;
use Atusan\Controls\Traits\TraitNavbarControl;
use Atusan\XML\XMLExtended;

abstract class NavControlBase extends Component
{
  use TraitNavbarControl;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->set('type', substr($this->xml->getName(), 4));
  }
}
