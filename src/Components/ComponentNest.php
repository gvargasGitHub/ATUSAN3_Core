<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitComponentNest;
use Atusan\Controller\Module;
use Atusan\Iterators\ComponentsIterator;
use Atusan\Iterators\ComponentSourcesIterator;
use Atusan\XML\XMLExtended;

abstract class ComponentNest extends Component
{
  use TraitComponentNest;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->injectXML();

    $this->sources = new ComponentSourcesIterator;

    $this->components = new ComponentsIterator;

    $this->setSources();

    $this->attachComponents();

    $this->finalDefinitions();
  }
}
