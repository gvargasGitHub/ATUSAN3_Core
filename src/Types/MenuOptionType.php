<?php

namespace Atusan\Types;

use Atusan\XML\XMLExtended;

class MenuOptionType
{
  public string $name;

  public string $text;

  public string $icon;

  function __construct(public string $owner, public int $row, protected XMLExtended $body) 
  {
    $this->text = ($body->hasAttribute('text'))
      ? $body->getAttribute('text') : trigger_error('Se requiere el atributo "text"', E_USER_ERROR);
    $this->name = ($body->hasAttribute('name'))
      ? $body->getAttribute('name') : trigger_error('Se requiere el atributo "name"', E_USER_ERROR);
    $this->icon = $body->getAttribute('icon');
  }

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
