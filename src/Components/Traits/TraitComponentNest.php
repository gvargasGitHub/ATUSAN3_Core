<?php

namespace Atusan\Components\Traits;

use Atusan\Components\Component;
use Atusan\Exceptions\InternalServerException;
use Atusan\Iterators\ComponentsIterator;
use Atusan\Iterators\ComponentSourcesIterator;
use Atusan\XML\XMLExtended;

trait TraitComponentNest
{
  use TraitComponent;

  /**
   * @var array $source
   * Cada clase "Component" puede definir 1 o más fuentes de
   * declaraciones de "Componentes". Este arreglo es la
   * colección de fuentes.
   */
  protected ComponentSourcesIterator $sources;

  /**
   * @var ComponentsIterator components
   * Diccionario de "Componentes" integrados a la clase. La clase
   * presente es el "propietario (owner)" de los "Componentes"
   * integrados a este diccionario.
   */
  protected ComponentsIterator $components;

  /**
   * @var int $noNameCounter
   */
  protected int $noNameCounter = 0;

  /**
   * Find Component by ID
   * Este método permite obtener un "Component" integrado a la clase por su nombre de declaración.
   * Esto evita disparar la advertencia de "Cannot access to undefined property" vigente desde PHP 8.0.
   * 
   * @param string $id Nombre de declaración del "Component" a buscar.
   * 
   * @return Component | null Retorna el "Component" si se encuentra, de lo contrario retorna null.
   */
  public function findComponentById(string $id) : Component | null
  {
    foreach($this->components as $component) {
      if ($component->get('id') == $id) return $component;

      if (is_subclass_of($component, '\\Atusan\\Components\\ComponentNest')) {
        $found = $component->findComponentById($id);

        if ($found !== null) return $found;
      }
    }

    return null;
  }

  /**
   * Get Components
   * @return ComponentsIterator
   */
  public function getComponents(): ComponentsIterator
  {
    return $this->components;
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
  protected function buildXML(XMLExtended $xml): XMLExtended
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
   * @param ComponentSourcesIterator $sources
   * 
   */
  public function attachComponents(ComponentSourcesIterator $sources): void
  {
    $this->noNameCounter = 0;
    // Recorre las fuentes establecidas
    foreach ($sources as $source) {
      // Recorre los espacios de nombres
      foreach ($this->namespaces as $namespace=>$url) {
        // Cada $url corresponde a un "namespace" declarado en el manifiesto.
        // Recorre la fuente filtrando las declaraciones del "namespace".
        foreach ($source->xml->children($url) as $xml) {
          $this->noNameCounter++;

          // Obtiene el nombre de la declaración
          $elementName = (string) $xml->getName();

          // Obtiene el nombre del elemento o genera uno
          if (!$xml->hasAttribute('id')) $xml->addAttribute('id', "{$elementName}{$this->noNameCounter}");

          if (empty($url) || $namespace == 'atusan') {
            // Declaración sin nombre de espacios solo puede ser:
            // a) DataViewControl o MenuControl
            // b) Component | ComponentNest
            // c) Etiquetas HTML
            if (preg_match('/^(Control|Menu)/', $elementName)) {
              // Declaración de DataViewControl o NavControl ATUSAN
              $componentClass = "Atusan\\Controls\\{$this->get('type')}Control";
            } elseif (is_subclass_of("Atusan\\Components\\{$elementName}", "Atusan\\Components\\Component")) {
              // Declaración de un Component ATUSAN
              $componentClass = "Atusan\\Components\\{$elementName}";
            } else {
              // Si la declaración no se reconoce entonces se considera elemento HTML
              $componentClass = "Atusan\\Components\\HTML";
            }
          } elseif ($namespace === 'html') {
            $componentClass = "Atusan\\Components\\HTML";
          } else {
            // Corresponde a declaraciones de Clases personalizadas.
            $nss = str_replace('.', '\\', str_replace('clr-namespace:', '', $url));
            $componentClass = "\\$nss\\$elementName";
            $xml = $this->buildXML($xml);
          }
          // Integra la instancia al diccionario de "Componentes".
          // "onwer" es heredado desde "Module.construct".
          $this->components->add($componentClass::fromXML($this->getOwner(), $xml));
        }
      }
    }
  }

  /**
   * Set Sources
   */
  abstract protected function setSources(): void;

  /**
   * Set Final Definitions

   */
  abstract protected function finalDefinitions(): void;
}
