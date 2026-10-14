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
    $this->set('id', ($xml->hasAttribute('id')) ? $xml->getAttribute('id') : basename(get_class($this)));
    $this->setXml($xml);
    $this->setOwner($owner);

    $this->set('type', basename(get_class($this)));
  }
}
