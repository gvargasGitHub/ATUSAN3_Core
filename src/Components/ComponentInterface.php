<?php

namespace Atusan\Components;

interface ComponentInterface
{
  /**
   * make
   * @return string
   */
  public function make(): string;
  
  /**
   * write
   * @return void
   */
  public function write(): void;
}
