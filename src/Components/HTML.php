<?php

namespace Atusan\Components;

class HTML extends Component
{
  protected $style;

  protected $colspan;

  function write(): void
  {
    echo str_replace('<?xml version="1.0"?>', '', $this->xml->asXml());
  }
}
