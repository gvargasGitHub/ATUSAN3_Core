<?php

namespace Atusan\Controls\Traits;

trait TraitNavbarControl 
{
  /**
   * setAttribute
   */
  public function setAttribute(string $attributeName, string $newValue): void
  {
    $this->xml->setAttribute($attributeName, $newValue);
  }

  /**
   * setText
   */
  public function setText(string $newValue)
  {
    $this->setAttribute('text', $newValue);
  }
  /**
   * setIcon
   */
  public function setIcon(string $newValue)
  {
    $this->setAttribute('icon', $newValue);
  }
  /**
   * setRoute
   */
  public function setRoute(string $newValue)
  {
    $this->setAttribute('route', $newValue);
  }
  /**
   * setView
   */
  public function setView(string $newValue)
  {
    $this->setAttribute('view', $newValue);
  }
}