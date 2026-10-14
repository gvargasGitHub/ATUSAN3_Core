<?php

namespace Atusan\Components;

use Atusan\Controls\NavbarControl;
use Atusan\XML\XMLExtended;

abstract class NavBase extends ComponentNest
{
  
  /**
   * @var int $itemCounter
   */
  protected int $itemCounter = 0;
  // ----------------------------------
  // Class
  // ----------------------------------
  /**
   * 
   */
  private function getMenuItem(string $itemName): NavbarControl
  {
    return $this->getMenuItem($itemName);
  }

  /**
   * setText
   */
  public function setText(string $itemName, string $newValue)
  {
    $this->getMenuItem($itemName)->setAttribute('text', $newValue);
  }
  /**
   * setIcon
   */
  public function setIcon(string $itemName, string $newValue)
  {
    $this->getMenuItem($itemName)->setAttribute('icon', $newValue);
  }
  /**
   * setRoute
   */
  public function setRoute(string $itemName, string $newValue)
  {
    $this->getMenuItem($itemName)->setAttribute('route', $newValue);
  }
  /**
   * setView
   */
  public function setView(string $itemName, string $newValue)
  {
    $this->getMenuItem($itemName)->setAttribute('view', $newValue);
  }
  /**
   * setTitle
   */
  public function setTitle(string $title): void
  {
    $this->set('title', $title);
  }
  /**
   * 
   */
  public function getitemCounter(): int
  {
    return ++$this->itemCounter;
  }
  
  /**
   * 
   */
  protected function getNSBlocks(): XMLExtended | null
  {
    $namespaces = array_merge($this->getOwner()->namespaces, $this->xml->getDocNamespaces());
    foreach ($namespaces as $ns => $url) {
      if (!preg_match('/^clr-namespace:/', $url)) continue;
      foreach ($this->xml->children($ns, 1) as $xml) return $xml;
    }

    return null;
  }
  /** */
  protected function getBlocks(): XMLExtended | null
  {
    return (get_class($this) == 'Atusan\\Components\\Navbar')
      ? $this->xml
      : $this->getNSBlocks();
  }
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // Integra las secciones Left & Right
    foreach ($this->xml->children() as $section) $this->addSource((string) $section->getName(), $section);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) $component->setParent($this);
  }

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------
  /**
   * make
   * @return string
   */
  public function make(): string
  {
    if (!$this->validateProperty('title')) $this->set('title', '');

    ob_start();
    include __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . DS . 'view.php';
    return ob_get_clean();
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
