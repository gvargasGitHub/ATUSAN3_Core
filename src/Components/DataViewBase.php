<?php

namespace Atusan\Components;

use Atusan\Controller\Module;
use Atusan\XML\XMLExtended;

abstract class DataViewBase extends ComponentNest
{
  protected array $data = [];

  protected int $index = 0;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->set('type', basename(get_class($this)));
  }

  /**
   * Import
   * @param array $data
   * 
   * @return int
   */
  abstract public function import(array $data): int;
  
  /**
   * RowCount
   * @return int
   */
  public function rowsCount(): int
  {
    return count($this->data);
  }

  /**
   * Get Data
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * Get View
   * Retorna la ruta absoluta al archivo View del Component.
   * @return string
   */
  protected function getView(): string
  {
    return __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . '.view.php';
  }

   /**
   * Build
   * Construye el Component dentro de un buffer controlado y lo retorna.
   * 
   * @return string
   */
  public function build(): string
  {
    ob_start();

    include __DIR__ . DS . 'Views' . DS . strtolower($this->get('type')) . DS . 'body.view.php';

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
