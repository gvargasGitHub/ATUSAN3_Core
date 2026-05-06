<?php

namespace Atusan\Controls;

use Atusan\Components\Component;
use Atusan\Components\ComponentInterface;

abstract class Control extends Component
{
  protected ComponentInterface $parent;

  public function setParent(ComponentInterface $parent)
  {
    $this->parent = $parent;
  }
}
