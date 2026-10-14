<?php

namespace Atusan\Types;

class TabGroupButtonType
{
  /**
   * 
   */
  function __construct(
    public string $ownerId, 
    public string $parentId, 
    public string $id,  
    public string $text, 
    public string $icon, 
    public string $moduleRoute) {}

  public function buildPairs()
  {
    $output = [
      'ats-owner="' . $this->ownerId . '"',
      'ats-parent="' . $this->parentId . '"',
      'ats-name="' . $this->id . '"',
      'ats-module="' . $this->moduleRoute . '"'
    ];

    return implode(' ', $output);
  }
}
