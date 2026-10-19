<?php

namespace Atusan\Components;

use Atusan\Types\TabGroupButtonType;

class TabGroupContent extends ComponentNest
{
  // ----------------------------------
  // Manejo de vista por Open/Close
  // ----------------------------------
  /**
   * Open TabGroup
   * @return string
   */
  public function open(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content-open.view.php';
    return ob_get_clean();
  }
  /**
   * Close TabGroup
   * @return string
   */
  public function close(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/content-close.view.php';
    return ob_get_clean();
  }

  /**
   * MakeButton
   * @return string
   */
  public function makeButton(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/button.view.php';
    return ob_get_clean();
  }

  /**
   * ItemType
   * @return TabGroupButtonType
   */
  protected function itemType(): TabGroupButtonType
  {
    return new TabGroupButtonType(
      $this->getOwner()->get('id'), 
      $this->parent->get('id'), 
      $this->xml->getAttribute('id'),
      $this->xml->getAttribute('text'),
      ($this->xml->hasAttribute('icon')) ? $this->xml->getAttribute('icon') : '', 
      ($this->xml->hasAttribute('module')) ? $this->xml->getAttribute('module') : '');
  }

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * Set Sources
   */
  protected function setSources(): void
  {
    // $this->closeable = property_exists($this, 'closeable');
    if (!$this->validateProperty('closeable')) $this->set('closeable', false);

    $this->addSource('content', $this->xml);
  }

  /**
   * Final Definitions
   */
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
