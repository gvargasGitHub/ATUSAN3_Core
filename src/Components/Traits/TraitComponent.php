<?php

namespace Atusan\Components\Traits;

use Atusan\Controller\Controller;
use Atusan\Controller\Module;
use Atusan\FileSystem\FileSystem;
use Atusan\XML\XMLExtended;
use Atusan\XML\XMLLoader;
use Exception;

trait TraitComponent
{
  /**
   * @var $name
   */
  public string $name;

  /**
   * @var $owner
   */
  protected Controller $owner;

  /**
   * @var XMLExtended $xml
   * De este objeto se extraen:
   * a) Atributos/Propiedades para definir la clase.
   * b) Colección de Componentes.
   */
  protected XMLExtended|false $xml;

  /**
   * @var Array $namespaces
   */
  protected array $namespaces;

  /**
   * 
   */
  static function fromXML(Module $owner, XMLExtended $xml): static
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
   * 
   */
  public function getOwner(): Controller
  {
    return $this->owner;
  }
  /**
   * 
   */
  public function setOwner($owner)
  {
    $this->owner = $owner;
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
        if (isset($v)) $this->$k = (string) $v;
    }

    if (property_exists($this, 'layout')) {
      $xmlext = XMLLoader::load($this->layout);

      if ($xmlext === false)
        throw new Exception(XMLLoader::getError());

      // integra atributos de la raiz de la extension al controlador
      foreach ($xmlext->attributes() as $k => $v)
        if (isset($v)) $this->$k = (string) $v;

      // integra la extensión al controlador
      $this->xml->extend($xmlext);
    }

    unset($this->xmlRef);
  }
  /**
   * 
   */
  public function locateViewFile(string $viewname): string
  {
    $directories = [$this->owner->getDirectory(), APP_DIRECTORY];
    for ($d = 0; $d < count($directories); $d++) {
      $located = FileSystem::locateFile($directories[$d], basename($viewname));

      if ($located) return $located;
    }

    trigger_error("La vista {$viewname} no existe", E_USER_ERROR);
  }
}
