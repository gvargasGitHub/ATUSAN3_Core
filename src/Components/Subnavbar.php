<?php

namespace Atusan\Components;

use Atusan\Controls\DataViewControlBase;
use Atusan\XML\XMLExtended;

class Subnavbar extends ComponentNest
{
  /* ------------------------
  Properties
  ------------------------ */
  public string $title = '';

  public string $type = 'Subnavbar';

  protected int $itemNoNameCounter = 0;

  /**
   * 
   */
  public function editItem(string $itemName, string $attributeName, string $attributeNewValue): void
  {
    $item = $this->seekRecursive($this->xml, $itemName);

    if ($item) {
      $item->setAttribute($attributeName, $attributeNewValue);
    }
  }

  /**
   * 
   */
  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  public function getItemNoNameCounter(): int
  {
    return ++$this->itemNoNameCounter;
  }

  /** */
  protected function getNSBlocks(): XMLExtended | null
  {
    $namespaces = array_merge($this->owner->namespaces, $this->xml->getDocNamespaces());
    foreach ($namespaces as $ns => $url) {
      if (!preg_match('/^clr-namespace:/', $url)) continue;
      foreach ($this->xml->children($ns, 1) as $xml) return $xml;
    }

    return null;
  }

  protected function getBlocks(): XMLExtended | null
  {
    return (get_class($this) == 'Atusan\\Components\\NavBar')
      ? $this->xml
      : $this->getNSBlocks();
  }

  protected function seekRecursive(XMLExtended $item, string $name): XMLExtended | false
  {
    if ($name == $item->getAttribute('name')) return $item;

    foreach ($item->children() as $child) {
      $res = $this->seekRecursive($child, $name);

      if ($res) return $res;
    }

    return false;
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

  /**
   * Write :: Component
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = "";
    include __DIR__ . DS . 'Views' . DS . 'subnavbar/view.php';
  }
}
