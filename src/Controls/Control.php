<?php

namespace Atusan\Controls;

use Atusan\Components\Component;

abstract class Control extends Component
{
  protected Component $parent;

  public function setParent(Component $parent)
  {
    $this->parent = $parent;
  }
}
