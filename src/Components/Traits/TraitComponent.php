<?php

namespace Atusan\Components\Traits;

use Atusan\Components\Component;
use Atusan\Controller\Module;
use Atusan\Exceptions\InternalServerException;
use Atusan\FileSystem\FileSystem;
use Atusan\XML\XMLExtended;
use Atusan\XML\XMLLoader;

trait TraitComponent
{
  /**
   * @var array $properties
   */
  protected array $properties = [];

  /**
   * @var Module $owner
   */
  protected Module $owner;

  /**
   * @var Component $parent
   */
  protected Component $parent;

  /**
   * @var XMLExtended $xml
   * De este objeto se extraen:
   * a) Atributos/Propiedades para definir la clase.
   * b) Colección de Componentes.
   */
  protected XMLExtended|false $xml;

  /**
   * @var string $layout
   * Extensión del manifiesto del Componente.
   */
  protected string $layout = '';

  /**
   * @var array $namespaces
   */
  protected array $namespaces;

  /**
   * fromXML
   * Puede retornar "Component" para la mayoría de los casos.
   * En el caso de "Panel" y "TabContent" puede retornar "Module".
   */
  static function fromXML(Module $owner, XMLExtended $xml): Component | Module
  {
    return new static($owner, $xml);
  }

  /**
   * 
   */
  public function setXML(XMLExtended $xml): void
  {
    $this->xml = $xml;
  }

  /**
   * get
   * Obtiene el valor de la propiedad.
   * @param string $propertyName
   * 
   * @return mixed
   */
  public function get(string $propertyName): mixed
  {
    return $this->properties[$propertyName] ?? null;
  }

  /**
   * set
   * Establee un valor para la propiedad.
   * @param string $propertyName
   * @param mixed $propertyValue
   * 
   * @return void
   */
  public function set(string $propertyName, mixed $propertyValue): void
  {
    $this->properties[$propertyName] = $propertyValue;
  }

  /**
   * validateProperty
   * Valida si la propiedad existe y tiene un valor.
   * @param string $propertyName
   * @return bool
   */
  public function validateProperty(string $propertyName): bool
  {
    return array_key_exists($propertyName, $this->properties) && $this->get($propertyName) !== null;
  }

  /**
   * getOwner
   * Retorna el Module propietario del Component.
   * @return Module
   */
  public function getOwner(): Module
  {
    return $this->owner;
  }

  /**
   * setOwner
   * Establece el Module propietario del Component.
   */
  public function setOwner(Module $owner)
  {
    $this->owner = $owner;
  }

  /**
   * getParent
   * Obtiene el Component padre del Component.
   * @return Component
   */
  public function getParent(): Component
  {
    return $this->parent;
  }

  /**
   * setParent
   * Establece el Component padre del Component.
   */
  public function setParent(Component $parent)
  {
    $this->parent = $parent;
  }

  /**
   * Inject XML
   * Integra la estructura definida en el archivo XML a la clase.
   */
  protected function injectXML(): void
  {
    // obtiene nombres de espacio
    $this->namespaces = array_merge(
      ['' => ''],
      $this->xml->getDocNamespaces(true, true)
    );

    // integra los atributos de "root" a la clase
    foreach ($this->namespaces as $url) {
      foreach ($this->xml->attributes($url) as $k => $v)
        if (isset($v)) $this->properties[$k] = (string) $v;
    }

    if ($this->validateProperty('layout')) {
      // Obtiene recurso XML
      $xmlext = XMLLoader::load($this->locateResource($this->get('layout')));

      if ($xmlext === false)
        throw new InternalServerException(XMLLoader::getError());

      // integra atributos de la raiz de la extension al controlador
      foreach ($xmlext->attributes() as $k => $v)
        if (isset($v)) $this->properties[$k] = (string) $v;

      // integra la extensión al controlador
      foreach($xmlext->children() as $ext) $this->xml->extend($ext);
    }

    unset($this->xmlRef);
  }

  /**
   * 
   */
  public function locateResource(string $fileName): string
  {
    $directories = [$this->owner->getDirectory(), APP_DIRECTORY];
    for ($d = 0; $d < count($directories); $d++) {
      // pasa el directorio en turno y el nombre del componente final
      $located = FileSystem::locateFile($directories[$d], basename($fileName));

      if ($located) return $located;
    }

    throw new InternalServerException("El recurso {$fileName} no existe");
  }
}
