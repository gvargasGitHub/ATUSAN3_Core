<?php

namespace Atusan\Components\Traits;

use Atusan\Components\Component;
use Atusan\Controller\Module;
use Atusan\FileSystem\FileSystem;
use Atusan\XML\XMLExtended;
use Atusan\XML\XMLLoader;
use Exception;

trait TraitComponent
{
  /**
   * @var string $name
   */
  public string $name;

  /**
   * @var Module $owner
   */
  protected Module $owner;

  /**
   * @var XMLExtended $xml
   * De este objeto se extraen:
   * a) Atributos/Propiedades para definir la clase.
   * b) Colección de Componentes.
   */
  protected XMLExtended|false $xml;

  /**
   * @var string $layout
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
   * 
   */
  public function getOwner(): Module
  {
    return $this->owner;
  }
  /**
   * 
   */
  public function setOwner(Module $owner)
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

    if (!empty($this->layout)) {
      // Obtiene recurso XML
      $xmlext = XMLLoader::load($this->locateResource($this->layout));

      if ($xmlext === false)
        throw new Exception(XMLLoader::getError());

      // integra atributos de la raiz de la extension al controlador
      foreach ($xmlext->attributes() as $k => $v)
        if (isset($v)) $this->$k = (string) $v;

      // integra la extensión al controlador
      foreach($xmlext->children() as $ext) $this->xml->extend($ext);
    }

    unset($this->xmlRef);
  }

  /**
   * 
   */
  public function locateResource(string $viewname): string
  {
    $directories = [$this->owner->getDirectory(), APP_DIRECTORY];
    for ($d = 0; $d < count($directories); $d++) {
      // pasa el directorio en turno y el nombre del componente final
      $located = FileSystem::locateFile($directories[$d], basename($viewname));

      if ($located) return $located;
    }

    trigger_error("El recurso {$viewname} no existe", E_USER_ERROR);
  }
}
