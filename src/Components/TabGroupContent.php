<?php

namespace Atusan\Components;

use Atusan\Types\TabGroupButtonType;

class TabGroupContent extends ComponentNest
{
  /* ------------------------
  Properties
  ------------------------ */
  public string $text;

  public string $view;

  public $closeable = false;

  public function begin()
  {
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content-begin.view.php';
  }

  public function end()
  {
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content-end.view.php';
  }

  public function button()
  {
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/button.view.php';
  }

  protected function itemType()
  {
    $name = $this->xml->getAttribute('name');
    $text = $this->xml->getAttribute('text');
    $icon = ($this->xml->hasAttribute('icon')) ? $this->xml->getAttribute('icon') : '';
    $module = ($this->xml->hasAttribute('module')) ? $this->xml->getAttribute('module') : '';

    return new TabGroupButtonType($this->owner->name, $this->parent->name, $name, $text, $icon, $module);
  }

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->closeable = property_exists($this, 'closeable');

    $this->addSource('content', $this->xml);
  }

  protected function finalDefinitions(): void {}

  public function write(): void
  {
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content.view.php';
  }
}
