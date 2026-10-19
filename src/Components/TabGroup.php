<?php

namespace Atusan\Components;

class TabGroup extends ComponentNest
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
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/tab-open.view.php';
    return ob_get_clean();
  }
  /**
   * Close TabGroup
   * @return string
   */
  public function close(): string
  {
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/tab-close.view.php';
    return ob_get_clean();
  }
  /**
   * Open TabGroupContent
   * @param string $contentId
   * @return string
   */
  public function openContent(string $contentId): string
  {
    $content = $this->getContentById($contentId);

    return $content->open();
  }
  /**
   * Close TabGroup
   * @return string
   */
  public function closeContent(string $contentId): string
  {
    $content = $this->getContentById($contentId);

    return $content->close();
  }

  public function getContentById(string $contentId): TabGroupContent
  {
    return $this->components->getById($contentId);
  }
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->addSource('tabgroup', $this->xml);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $content) $content->setParent($this);
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
    ob_start();
    include __DIR__ . DS . 'Views' . DS . 'tabgroup/view.php';
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
