<?php

namespace Atusan\Components;

use Atusan\Controller\Module;
use Atusan\XML\XMLExtended;

abstract class DataViewBase extends ComponentNest
{
  // Properties
  // public string $title;

  // public string $footer;

  // public string $route;

  // /**
  //  * @var string $type
  //  */
  // protected string $type;

  // protected string $view;

  protected array $data = [];

  protected int $index = 0;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->set('type', basename(__CLASS__));
  }

  /**
   * 
   */
  // abstract protected function setType();

  /**
   * 
   */
  // abstract protected function initProperties();

  // /**
  //  * Build
  //  */
  // abstract public function build(): string;

  /**
   * Import
   */
  abstract public function import(array $data): int;
  /**
   * 
   */
  public function rowsCount(): int
  {
    return count($this->data);
  }

  /**
   * 
   */
  public function getData(): array
  {
    return $this->data;
  }

  protected function getView(): string
  {
    return __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . '.view.php';
  }

   /**
   * Build
   */
  public function build(): string
  {
    ob_start();

    include __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . '/body.view.php';

    $html = ob_get_contents();

    ob_clean();

    return $html;
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
    if (!$this->validateProperty('footer')) $this->set('footer', '');

    ob_start();
    include __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . '/view.php';
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
