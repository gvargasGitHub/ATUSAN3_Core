<?php

namespace Atusan\Components\Traits;

use Atusan\Http\Request\Request;
use Atusan\Iterators\InputFileIterator;
use Atusan\Iterators\OptionTextValueIterator;

trait TraitDataForm
{
  /**
   * Add Data And Csrfc
   */
  protected function addDataAndCsrf()
  {
    $this->data = [['csrf_token' => '']];
    // Integra de manera predeterminada el control Csrf
    $csrf = $this->xml->addChild('ControlCsrf');
    $csrf->addAttribute('name', 'csrf_token');
    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
  }
  /**
   * Write Control
   * 
   * @return void
   */
  public function writeControl(string $id): void
  {
    if (!($control = $this->components->getById($id)))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    if (count($this->data) == 0) $this->data[0] = [];

    $control->setData($this->data[0]);

    echo $control->make();
  }

  /**
   * 
   */
  public function getItem(string $id)
  {
    if (!$this->components->getById($id))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    return array_key_exists($id, $this->data[0]) ? $this->data[0][$id] : null;
  }

  /**
   * 
   */
  public function setItem(string $id, mixed $value)
  {
    if (!$this->components->getById($id))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    $this->data[0][$id] = $value;
  }

  /**
   * 
   */
  public function setCsrf(string $code)
  {
    $this->setItem('csrf_token', $code);
  }
  /**
   * Feed List
   * 
   * @exception
   */
  public function feedList(string $id, array $data)
  {
    if (!($control = $this->components->getById($id)))
      trigger_error(basename(__FUNCTION__) . "El control {$id} no existe para {$this->get('id')}");

    if ($control->getType() != 'Select')
      trigger_error(basename(__FUNCTION__) . "El control {$id} no es una lista.");

    $control->createList(new OptionTextValueIterator($data));
  }

  /**
   * Type Control
   * Modifica el tipo del Control.
   * 
   * @exception
   */
  public function typeControl(string $id, string $type = null)
  {
    if (!($control = $this->components->getById($id)))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    $lastType = $control->getType();

    if ($type != null) $control->setType($type);

    return $lastType;
  }

  /**
   * Enable Control
   * 
   * @exception
   */
  public function enableControl(string $id, bool $enable)
  {
    if (!($control = $this->components->getById($id)))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    $control->setEnable($enable);
  }

  /**
   * Files Of
   */
  public function filesOf(string $id): InputFileIterator | null
  {
    if (!($this->components->getById($id)))
      trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

    return Request::instance()->files()->$id;
  }

  // ----------------------------------
  //  DataViewBase
  // ----------------------------------
  /**
   * Import
   */
  public function import(array $data): int
  {
    foreach($data as $id=>$value) {
      if (!$this->components->getById($id))
        trigger_error(basename(__FUNCTION__) . ":El control {$id} no existe para {$this->get('id')}");

      $this->data[0][$id] = $value;
    }

    return count($this->data);
  }
}
