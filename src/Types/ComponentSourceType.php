<?php

namespace Atusan\Types;

use Atusan\XML\XMLExtended;

class ComponentSourceType
{
  function __construct(public string $name, public XMLExtended $xml) {}
}
