<?php

namespace Atusan\Components\Traits;

use Atusan\Iterators\ComponentsIterator;
use Atusan\Iterators\ComponentSourcesIterator;
use Atusan\XML\XMLExtended;

trait TraitComponentNest
{
  use TraitComponent;

  /**
   * @var Array $source
   * Cada clase "Component" puede definir 1 o más fuentes de
   * declaraciones de "Componentes". Este arreglo es la
   * colección de fuentes.
   */
  protected ComponentSourcesIterator $sources;

  /**
   * @var Array components
   * Diccionario de "Componentes" integrados a la clase. La clase
   * presente es el "propietario (owner)" de los "Componentes"
   * integrados a este diccionario.
   */
  protected ComponentsIterator $components;

  /**
   * @var Int $noNameCounter
   */
  protected int $noNameCounter = 0;

  /**
   * Get
   * Permite que los "Components" sean accesibles como propiedades
   * públicas del objeto.
   */
  function __get($name)
  {
    return $this->components->getByName($name) ?? null;
  }

  /**
   * Add Source
   */
  protected function addSource(string $key, XMLExtended $source)
  {
    $this->sources->add($key, clone $source);
  }

  /**
   * Build XML
   * Si la declaración del Elemento en el XML pertenece a un
   * nombre de espacios, entonces crea un objeto XML con la
   * declaración de los nombres de espacios y procesa la
   * integración.
   */
  protected function buildXML($xml): XMLExtended
  {
    $nss = [];
    foreach ($this->namespaces as $ns => $url) {
      if (empty($ns)) continue;
      $nss[] = "xmlns:{$ns}=\"{$url}\"";
    }

    $content = "<?xml version='1.0'?><Root ";
    $content .= implode(" ", $nss);
    $content .= ">" . $xml->asXML() . "</Root>";

    return simplexml_load_string($content, XMLExtended::class);
  }

  /**
   * Attach Components
   * Integra instancias de los "Componentes" en el diccionario.
   * 
   */
  public function attachComponents()
  {
    $this->noNameCounter = 0;
    // Recorre las fuentes establecidas
    foreach ($this->sources as $source) {
      // Recorre los espacios de nombres
      foreach ($this->namespaces as $url) {
        // Cada $url corresponde a un "namespace" declarado en el manifiesto.
        // Recorre la fuente filtrando las declaraciones del "namespace".
        foreach ($source->xml->children($url) as $xml) {
          $this->noNameCounter++;

          // Obtiene el nombre de la declaración
          $elementName = (string) $xml->getName();

          // Obtiene el nombre del elemento o genera uno
          if (!$xml->hasAttribute('name')) $xml->addAttribute('name', "{$elementName}{$this->noNameCounter}");

          if (empty($url)) {
            // Declaración sin nombre de espacios solo puede ser:
            // a) Component
            // b) DataViewControl
            // c) Etiquetas HTML
            if (preg_match('/^(Control|Menu)/', $elementName))
              $componentName = "Atusan\\Controls\\{$this->type}Control";
            elseif (is_subclass_of("Atusan\\Components\\{$elementName}", "Atusan\\Components\\Component"))
              $componentName = "Atusan\\Components\\{$elementName}";
            else
              $componentName = "Atusan\\Components\\HTML";
          } else {
            $nss = str_replace('.', '\\', str_replace('clr-namespace:', '', $url));
            $componentName = "\\$nss\\$elementName";
            $xml = $this->buildXML($xml);
          }
          // Integra la instancia al diccionario de "Componentes".
          // "onwer" es heredado desde "Module.construct".
          $this->components->add($componentName::fromXML($this->owner, $xml));
        }
      }
    }
  }

  /**
   * 
   */
  abstract protected function setSources(): void;

  /**
   * 
   */
  abstract protected function finalDefinitions(): void;
}
