<?php
namespace Atusan\Components\Traits;

use Atusan\XML\XMLExtended;

trait TraitNavbar
{
  /* ------------------------
  Properties
  ------------------------ */
  public string $title = '';

  protected int $itemNoNameCounter = 0;

  /**
   * setText
   */
  public function setText(string $itemName, string $newValue)
  {
    $this->components->getByName($itemName)->setAttribute('text', $newValue);
  }
  /**
   * setIcon
   */
  public function setIcon(string $itemName, string $newValue)
  {
    $this->components->getByName($itemName)->setAttribute('icon', $newValue);
  }
  /**
   * setRoute
   */
  public function setRoute(string $itemName, string $newValue)
  {
    $this->components->getByName($itemName)->setAttribute('route', $newValue);
  }
  /**
   * setView
   */
  public function setView(string $itemName, string $newValue)
  {
    $this->components->getByName($itemName)->setAttribute('view', $newValue);
  }
  /**
   * 
   */
  public function setTitle(string $title): void
  {
    $this->title = $title;
  }
  /**
   * 
   */
  public function getItemNoNameCounter(): int
  {
    return ++$this->itemNoNameCounter;
  }
  /**
   * 
   */
  protected function getNSBlocks(): XMLExtended | null
  {
    $namespaces = array_merge($this->owner->namespaces, $this->xml->getDocNamespaces());
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
}