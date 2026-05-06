<?php

namespace Atusan\XML;

use SimpleXMLElement;

class XMLExtended extends SimpleXMLElement
{
  /**
   * Get Attribute
   */
  public function getAttribute(string $name, string $namespace = null, bool $prefix = false): string
  {
    return (string) $this->attributes($namespace, $prefix)->$name;
  }

  /**
   * Set Attribute
   */
  public function setAttribute(string $name, mixed $value, string $namespace = null)
  {
    $ns = ($namespace) ?: null;

    if ($this->hasAttribute($name, $namespace, 1))
      $this->attributes($namespace, 1)->$name = $value;
    else
      $this->addAttribute($name, $value, $namespace);
  }

  /**
   * Get Element Child
   */
  public function getChild(int $index): XMLExtended
  {
    $child = false;
    $i = 0;
    foreach ($this->children() as $child) {
      if ($i == $index) return $child;
      $i += 1;
    }
    return false;
  }

  /**
   * Has Attribute
   */
  public function hasAttribute(string $name, string $namespace = null, bool $prefix = false): bool
  {
    return isset($this->attributes($namespace, $prefix)->$name);
  }

  /**
   * Has Text
   */
  public function hasText(): bool
  {
    return (strlen(trim((string)$this)) > 0);
  }

  /**
   * Extend
   */
  public function extend(XMLExtended $node, bool $includeAttributes = true): void
  {
    $xml = ($node->hasChildren())
      ? $this->addChild($node->getName())
      : $this->addChild($node->getName(), (string)$node);

    if ($includeAttributes) {
      # Integra atributos de raiz del nodo al nuevo elemento
      foreach ($node->attributes() as $k => $v) $xml->addAttribute($k, $v);

      # Integra atributos con nombre de espacios al nuevo elemento
      $nss = $node->getNamespaces(true);
      foreach ($nss as $ns => $urn)
        foreach ($node->attributes($ns, true) as $k => $v) $xml->addAttribute("$ns:$k", $v, $ns);
    }

    # Si tiene hijos los integra al nuevo elemento
    foreach ($node->children() as $ch) $xml->extend($ch, $includeAttributes);
  }

  /**
   * 
   */
  public function buildAttributesPairs($ns = ''): string
  {
    $output = [];

    foreach ($this->attributes($ns, true) as $k => $v)
      $output[] = "{$k}=\"{$v}\"";

    return implode(' ', $output);
  }

  public function buildPairs($separator, $keyval, $valwrap, $ns = ''): string
  {
    $output = [];

    foreach ($this->attributes($ns, true) as $k => $v)
      $output[] = "{$k}{$keyval}{$valwrap}{$v}{$valwrap}";

    return implode($separator, $output);
  }

  /**
   * 
   */
  public function writex()
  {
    echo str_replace('<?xml version="1.0"?>', '', $this->asXml());
  }
}
