<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitComponent;
use Atusan\Controller\Module;
use Atusan\XML\XMLExtended;

abstract class Component implements ComponentInterface
{
  use TraitComponent;

  function __construct(Module $owner, XMLExtended $xml)
  {
    $this->name = $xml->getAttribute('name');
    $this->xml = $xml;
    $this->owner = $owner;
  }
}
