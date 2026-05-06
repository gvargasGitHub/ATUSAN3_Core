<?php

namespace Atusan\Types;

use Atusan\XML\XMLExtended;

class ButtonType
{
  function __construct(protected XMLExtended $xml) {}

  function properties(): array
  {
    $name = $this->xml->getAttribute('name');
    $text = $this->xml->getAttribute('text');
    $icon = ($this->xml->hasAttribute('icon')) ? "<i class=\"{$this->xml->getAttribute('icon')}\"></i>" : '';

    if (!$this->xml->hasAttribute('onclick', 'html', true) && !$this->xml->hasAttribute('type', 'html', true))
      $this->xml->setAttribute('html:onclick', "{$name}(event)", 'html');

    return [
      'name' => $name,
      'text' => $text,
      'icon' => $icon
    ];
  }

  static function pairs(XMLExtended $xml): static
  {
    return new static($xml);
  }
}
