<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitComponentNest;
use Atusan\Iterators\ComponentsIterator;
use Atusan\Iterators\ComponentSourcesIterator;

abstract class ComponentNest extends Component
{
  use TraitComponentNest;

  function __construct($owner, $xml)
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
