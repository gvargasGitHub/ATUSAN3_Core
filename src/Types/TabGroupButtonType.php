<?php

namespace Atusan\Types;

class TabGroupButtonType
{
  /**
   * 
   */
  function __construct(public $owner, public $parent, public $name, public $text, public $icon, public $module) {}

  public function buildPairs()
  {
    $output = [
      'ats-owner="' . $this->owner . '"',
      'ats-parent="' . $this->parent . '"',
      'ats-name="' . $this->name . '"',
      'ats-module="' . $this->module . '"'
    ];

    return implode(' ', $output);
  }
}
