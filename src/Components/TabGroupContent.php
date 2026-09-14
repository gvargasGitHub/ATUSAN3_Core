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

  public function makeButton(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/button.view.php';
    return ob_get_clean();
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

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------
  /**
   * make
   * @return string
   */
  public function make(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content.view.php';
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
