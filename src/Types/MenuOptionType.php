<?php

namespace Atusan\Types;

class MenuOptionType
{
  function __construct(public $owner, public $name, public $row) {}

  public function buildPairs()
  {
    $output = [
      'ats-owner="' . $this->owner . '"',
      'ats-name="' . $this->name . '"',
      'ats-row="' . $this->row . '"'
    ];

    return implode(' ', $output);
  }
}
