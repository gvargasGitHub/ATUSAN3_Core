<?php

namespace Atusan\Types;

use Atusan\XML\XMLExtended;

class ButtonType
{
  function __construct(protected XMLExtended $xml) {}

  function properties(): array
  {
    $id = $this->xml->getAttribute('id');
    $text = $this->xml->getAttribute('text');
    $icon = ($this->xml->hasAttribute('icon')) ? "<i class=\"{$this->xml->getAttribute('icon')}\"></i>" : '';

    // ChatGPT: ajuste
    $type = $this->xml->hasAttribute('type', 'html', true)
      ? strtolower(trim($this->xml->getAttribute('type', 'html', true)))
      : 'button';

    if (
      !$this->xml->hasAttribute('onclick', 'html', true)
      && $type === 'button'
    ) {
      $this->xml->setAttribute(
        'html:onclick',
        "window.{$id}(event)",
        'html'
      );
    }

    return [
      'id' => $id,
      'text' => $text,
      'icon' => $icon
    ];
  }

  static function pairs(XMLExtended $xml): static
  {
    return new static($xml);
  }
}
