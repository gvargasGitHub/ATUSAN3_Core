<?php

namespace Atusan\Components;

use Atusan\Components\ButtonGroupBase;

class ButtonGroupRow extends ButtonGroupBase
{
  protected function defineGroupType(): string
  {
    return 'row';
  }

  protected function finalDefinitions(): void
  {
    $this->addCssClass($this->xml->hasAttribute('align')
      ? "ats-btn-align-" . $this->xml->getAttribute('align')
      : "ats-btn-align-left");
  }
}
