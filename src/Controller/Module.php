<?php

namespace Atusan\Controller;

use Atusan\Components\Traits\TraitComponentNest;
use Atusan\Exceptions\InternalServerException;
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

  protected XMLExtended $xmlTemplate;

  function __construct()
  {
    // constuctor de Controller
    parent::__construct();

    $this->set('id', $this->name);

    // Obtiene el directorio de la clase
    $this->directory = FileSystem::getClassDirectory($this);

    // TraitComponent: Se establece como "propietario"
    $this->owner = $this;

    // TraitComponentNest: Inicializa "$sources"
    $this->sources = new ComponentSourcesIterator;

    // TraitCompoentNest: Inicializa "$components"
    $this->components = new ComponentsIterator;

    // Inicializa Componentes
    $this->_initializeComponents();
  }

  /**
   * getDirectory
   * @return string
   */
  public function getDirectory(): string
  {
    return $this->directory;
  }

  /**
   * setTemplate
   * @param string $templateName
   */
  public function setTemplate(string $templateName): void
  {
    // Establece el valor
    $this->set('template', $templateName);

    // Inyecta el manifiesto en la clase
    $this->_injectTemplateXML();

    // Crea Iterator para la nueva fuente
    $sources = new ComponentSourcesIterator;

    $sources->add('template', clone $this->xmlTemplate);
    // Integra los Components de la nueva fuenta a la clase
    $this->attachComponents($sources);

  }

  /**
   * 
   */
  public function getTemplate(): string | null
  {
    return $this->get('template');
  }

  /**
   * 
   */
  public function setTitle(string $title): void
  {
    $this->set('title', $title);
  }

  /**
   * 
   */
  public function getTitle(): string
  {
    return $this->get('title');
  }

  /**
   * 
   */
  public function setView(string $viewFile): void
  {
    $this->set('view', $viewFile);
  }

  /**
   * 
   */
  public function getView(): string
  {
    return $this->get('view');
  }

  /**
   * Initialize Components
   * Obtiene el manifiesto del "Modulo" y lo integra al objeto.
   */
  protected function _initializeComponents(): void
  {
    if (($ref = $this->_getXMLFilename()) == null)
      $this->xml = XMLLoader::empty();
    else
      if (($this->xml = XMLLoader::load($ref)) === false) 
        throw new InternalServerException(XMLLoader::getError());

    // Carga Manifiesto del "Module"
    $this->injectXML();

    // Carga Manifiesto de "Template"
    $this->_injectTemplateXML();

    // Establece las fuentes de donde obtendrá los Componentes
    $this->setSources();

    // Carga Componentes
    $this->attachComponents($this->sources);
  }

  /**
   * Get XML Filename
   */
  protected function _getXMLFilename(): string | null
  {
    # El archivo XML de un Módulo puede tener las siguiente nomenclatura:
    // - module-directory/Components.xml
    // - app-directory/Components/{module-name}.xml
    $dirs = [
      $this->directory . "/Components.xml",
      APP_DIRECTORY . "/Components/{$this->get('id')}.xml"
    ];

    foreach ($dirs as $dir) if (FileSystem::exists($dir)) return $dir;

    return null;
  }

  /**
   * Get View Filename
   */
  public function getViewFilename(): string | null
  {
    // Si la propiedad "view" está definida, se busca la vista en la ruta especificada
    if ($this->validateProperty('view')) {
      $dirs = [APP_DIRECTORY . DS . "{$this->get('view')}"];
    } else {
      // Si la propiedad "view" no está definida, se busca la vista en las rutas predeterminadas
      $dirs = [
        $this->directory . DS . "View.php",
        APP_DIRECTORY . DS . "/Views/{$this->get('id')}.php"
      ];
    }
    
    foreach ($dirs as $dir) if (FileSystem::exists($dir)) return $dir;

    return null;
  }

  /**
   * 
   */
  protected function _injectTemplateXML()
  {
    if ($this->validateProperty('template')) {
      // Se construye la ruta al archivo Template.xml
      // La ruta predeterminada es: APP_DIRECTORY/Templates/[template_name]/[template_name.xml]
      $templateDir = APP_DIRECTORY . DS
        . implode(DS, ['Templates', $this->get('template')]) . DS
        . $this->get('template') . ".xml";

      // Se obtiene el archivo Template.xml declarado o un template vacio
      $this->xmlTemplate = FileSystem::exists($templateDir) ? XMLLoader::load($templateDir) : XMLLoader::empty();
    } else {
      $this->xmlTemplate = XMLLoader::empty();
    }

    if ($this->xmlTemplate === false)
      throw new InternalServerException(XMLLoader::getError());

    # Actualiza "namespaces"
    $this->namespaces = array_merge(
      $this->namespaces,
      $this->xmlTemplate->getDocNamespaces(true, true)
    );
  }
  
  /**
   * Make
   */
  public function make(): string
  {
    if (($ref = $this->getViewFilename()) == null)
      throw new InternalServerException("La vista {$ref} de {$this->get('id')} no existe");
    
    ob_start();
    
    include $ref;

    return ob_get_clean();
  }
  /**
   * Write
   */
  public function write(): void
  {
    echo $this->make();
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
}
