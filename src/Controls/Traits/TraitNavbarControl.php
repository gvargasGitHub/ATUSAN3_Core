<?php

namespace Atusan\Controls\Traits;

use Atusan\Components\NavBase;
use Atusan\Types\MenuItemType;

trait TraitNavbarControl 
{
  /**
   * getNavParent
   * 
   * @return NavBase
   */
  protected function getNavParent() : NavBase
  {
    return $this->parent;
  }
  
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

  /**
   * 
   */
  protected function itemType(): MenuItemType
  {
    $itemNumber = $this->getNavParent()->getItemCounter();

    return new MenuItemType($this->parent->get('id'), $itemNumber, $this->xml);
  }
}