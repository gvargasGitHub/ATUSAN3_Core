<?php

namespace Atusan\Components;

use Atusan\Components\ButtonGroupBase;

class ButtonGroupBar extends ButtonGroupBase
{
  protected function defineGroupType(): string
  {
    return 'bar';
  }

  protected function finalDefinitions(): void
  {
    $percent = 100 / $this->xml->count();

    foreach ($this->xml->children() as $xml) $xml->setAttribute('html:style', "width:{$percent}%", 'html');
  }
}
