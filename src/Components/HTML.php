<?php

namespace Atusan\Components;

class HTML extends Component
{
  protected string $style;

  protected int $colspan;

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------
  /**
   * make
   * @return string
   */
  public function make(): string
  {
    return str_replace('<?xml version="1.0"?>', '', $this->xml->asXml());
  }

  /**
   * write
   * @return void
   */
  public function write(): void
  {
    echo $this->make();
  }
}
