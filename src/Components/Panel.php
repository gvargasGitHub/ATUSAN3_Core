<?php

namespace Atusan\Components;

class Panel extends ComponentNest
{
  // ----------------------------------
  // Manejo de vista por Open/Close
  // ----------------------------------
  /**
   * Open Panel
   * @return string
   */
  public function open(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'panel/panel-open.view.php';
    return ob_get_clean();
  }
  /**
   * Close Panel
   * @return string
   */
  public function close(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'panel/panel-close.view.php';
    return ob_get_clean();
  }
  /**
   * Open PanelContent
   * @param string $panelName
   * @return string
   */
  public function openContent(string $panelName): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'panel/content-open.view.php';
    return ob_get_clean();
  }
  /**
   * Close Panel
   * @return string
   */
  public function closeContent(string $panelName): string
  {
     ob_start();
    include __DIR__ . DS . 'Views' . DS . 'panel/content-close.view.php';
    return ob_get_clean();
  }

  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * Set Sources
   */
  protected function setSources(): void
  {
    // Integra las secciones Left, Content & Right
    foreach ($this->xml->children() as $section) 
      $this->addSource(strtolower((string) $section->getName()), $section);
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
    include __DIR__ . DS . 'Views' . DS . 'panel/view.php';
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
