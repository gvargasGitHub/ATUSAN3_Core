<?php

namespace Atusan\Components\Traits;

use Atusan\Http\Request\Request;
use Atusan\Iterators\InputFileIterator;
use Atusan\Iterators\OptionTextValueIterator;
use Exception;

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
   * @exception
   */
  public function writeControl(string $name)
  {
    if (!($control = $this->components->getByName($name)))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    if (count($this->data) == 0) $this->data[0] = [];

    $control->setData($this->data[0]);

    $control->write();
  }

  /**
   * 
   */
  public function getItem(string $name)
  {
    if (!$this->components->getByName($name))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    return $this->data[0][$name];
  }

  /**
   * 
   */
  public function setItem(string $name, mixed $value)
  {
    if (!$this->components->getByName($name))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    $this->data[0][$name] = $value;
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
  public function feedList(string $name, array $data)
  {
    if (!($control = $this->components->getByName($name)))
      throw new Exception(basename(__FUNCTION__) . "El control {$name} no existe para {$this->name}");

    if ($control->getType() != 'Select')
      throw new Exception(basename(__FUNCTION__) . "El control {$name} no es una lista.");

    $control->createList(new OptionTextValueIterator($data));
  }

  /**
   * Type Control
   * Modifica el tipo del Control.
   * 
   * @exception
   */
  public function typeControl(string $name, string $type = null)
  {
    if (!($control = $this->components->getByName($name)))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    $lastType = $control->getType();

    if ($type != null) $control->setType($type);

    return $lastType;
  }

  /**
   * Enable Control
   * 
   * @exception
   */
  public function enableControl(string $name, bool $enable)
  {
    if (!($control = $this->components->getByName($name)))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    $control->setEnable($enable);
  }

  /**
   * Files Of
   */
  public function filesOf(string $name): InputFileIterator | null
  {
    if (!($this->components->getByName($name)))
      throw new Exception(basename(__FUNCTION__) . ":El control {$name} no existe para {$this->name}");

    return Request::instance()->files()->$name;
  }
}
