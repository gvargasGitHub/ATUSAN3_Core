<?php

namespace Atusan\Controller;

use Atusan\Components\Traits\TraitComponentNest;
use Atusan\FileSystem\FileSystem;
use Atusan\Iterators\ComponentsIterator;
use Atusan\Iterators\ComponentSourcesIterator;
use Atusan\Template\Template;
use Atusan\XML\XMLExtended;
use Atusan\XML\XMLLoader;

abstract class Module extends Controller
{
  use TraitComponentNest;

  protected string $directory = '';

  protected string $template = '';

  protected string $title = '';

  protected XMLExtended $xmlTemplate;

  function __construct()
  {
    // constuctor de Controller
    parent::__construct();

    // Obtiene el directorio de la clase
    $this->directory = FileSystem::getClassDirectory($this);

    // TraitComponent: Se establece como "propietario"
    $this->owner = $this;

    // TraitComponentNest: Inicializa "$sources"
    $this->sources = new ComponentSourcesIterator;

    // TraitCompoentNest: Inicializa "$components"
    $this->components = new ComponentsIterator;

    // Inicializa Componentes
    $this->initComponents();
  }

  /**
   * 
   */
  public function getDirectory(): string
  {
    return $this->directory;
  }

  /**
   * 
   */
  public function setTemplate(string $templatename): void
  {
    $this->template = $templatename;
  }

  /**
   * 
   */
  public function getTemplate(): string | null
  {
    return $this->template;
  }

  /**
   * 
   */
  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  /**
   * 
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * Initialize Components
   * Obtiene el manifiesto del "Modulo" y lo integra al objeto.
   */
  protected function initComponents(): void
  {
    if (($ref = $this->getXMLFilename()) == null)
      $this->xml = XMLLoader::empty();
    else
      if (($this->xml = XMLLoader::load($ref)) === false) throw new \Exception(XMLLoader::getError());

    // Carga Manifiesto del "Module"
    $this->injectXML();

    // Carga Manifiesto de "Template"
    $this->injectTemplateXML();

    // Establece las fuentes de donde obtendrá los Componentes
    $this->setSources();

    // Carga Componentes
    $this->attachComponents();
  }

  /**
   * Get XML Filename
   */
  protected function getXMLFilename(): string | null
  {
    # El archivo XML de un Módulo puede tener las siguiente nomenclatura:
    // a) module-directory/{module-name}.xml
    // b) module-directory/Components.xml
    // c) app-directory/Components/{module-name}.xml
    $dirs = [
      $this->directory . "/{$this->name}.xml",
      $this->directory . "/Components.xml",
      APP_DIRECTORY . "/Components/{$this->name}.xml"
    ];

    foreach ($dirs as $dir) if (FileSystem::exists($dir)) return $dir;

    return null;
  }

  /**
   * Get View Filename
   */
  public function getViewFilename(): string | null
  {
    // La vista puede tener las siguientes nomenclaturas:
    // a) module-directory/[module-name].view.php
    // b) module-directory/View.php
    // c) app-directory/Views/{module-name}.view.php
    $dirs = [
      $this->directory . DS . "{$this->name}.view.php",
      $this->directory . DS . "View.php",
      APP_DIRECTORY . DS . "/Views/{$this->name}.view.php"
    ];
    foreach ($dirs as $dir) if (FileSystem::exists($dir)) return $dir;

    return null;
  }

  /**
   * 
   */
  protected function injectTemplateXML()
  {
    // Se construye la ruta al archivo Template.xml
    // La ruta predeterminada es: APP_DIRECTORY/Templates/[template_name]/[template_name.xml]
    $templateDir = APP_DIRECTORY . DS
      . implode(DS, ['Templates', $this->template]) . DS
      . $this->template . ".xml";

    // Se obtiene el archivo Template.xml declarado o un template vacio
    $this->xmlTemplate = FileSystem::exists($templateDir) ? XMLLoader::load($templateDir) : XMLLoader::empty();

    if ($this->xmlTemplate === false)
      throw new \Exception(XMLLoader::getError());

    # Actualiza "namespaces"
    $this->namespaces = array_merge(
      $this->namespaces,
      $this->xmlTemplate->getDocNamespaces(true, true)
    );
  }
    
  // ----------------------------------
  // Template extensions
  // ----------------------------------
  public function extend(string $layout): void
  {
    Template::extend($layout);
  }
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * Set Source
   * Un "Module" integra "Components" desde los siguientes manifiestos:
   * a) Components.xml
   * b) Template.xml
   */
  protected function setSources(): void
  {
    // Se establece Root como el contenedor de los Componentes del módulo
    $this->addSource('module', $this->xml);

    $this->addSource('template', $this->xmlTemplate);
  }

  /**
   * 
   */
  protected function finalDefinitions(): void {}

  /**
   * Write
   */
  public function write(): void
  {
    if (($ref = $this->getViewFilename()) == null)
      throw new \Exception("La vista de {$this->name} no existe");

    include $ref;
  }
}
