<?php

namespace Atusan\Components;

class HTML extends Component
{
  protected string $style;

  protected int $colspan;

  function write(): string
  {
    return str_replace('<?xml version="1.0"?>', '', $this->xml->asXml());
  }
}
