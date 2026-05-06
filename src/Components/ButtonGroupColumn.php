<?php

namespace Atusan\Components;

use Atusan\Components\ButtonGroupBase;

class ButtonGroupColumn extends ButtonGroupBase
{
  protected function defineGroupType(): string
  {
    return 'column';
  }

  protected function finalDefinitions(): void {}
}
