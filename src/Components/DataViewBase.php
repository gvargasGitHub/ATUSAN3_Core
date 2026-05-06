<?php

namespace Atusan\Components;

abstract class DataViewBase extends ComponentNest
{
  /* ------------------------
  Properties
  ------------------------ */
  public $title;

  public $footer;

  public $route;

  protected $type;

  protected $view;

  protected $data = [];

  protected $index = 0;

  /**
   * 
   */
  abstract protected function setType();

  /**
   * 
   */
  abstract protected function initProperties();

  /**
   * Build
   */
  abstract public function build(): string;

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
    return __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '.view.php';
  }
}
