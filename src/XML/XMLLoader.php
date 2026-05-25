<?php

namespace Atusan\XML;

use Atusan\XML\XMLValidator;

class XMLLoader
{
  protected static $errstr = '';
  /**
   * Load
   */
  public static function load(string $filename): XMLExtended|false
  {
    libxml_use_internal_errors(true);
    // Propiedad unica de la clase para proveer mensaje de error
    self::$errstr = '';

    // Verifica la extension
    $info = pathinfo($filename);
    if (!isset($info['extension'])) $filename .= '.xml';

    // Valida si el archivo existe
    if (!XMLValidator::exists($filename)) {
      self::$errstr = basename($filename) . ' no existe';
      return false;
    }
    // lee el contenido del archivo para convertir a minúsculas
    $xml = simplexml_load_file($filename, XMLExtended::class);

    if ($xml === false) {
      self::$errstr = "No se pudo cargar manifiesto (" . basename($filename) . ")";

      foreach (libxml_get_errors() as $err)
        self::$errstr .= "{$err->message} en la línea {$err->line}";
    }

    return $xml;
  }

  public static function getError(): string
  {
    return self::$errstr;
  }

  /**
   * Empty
   * Crea un objeto SimpleXML con elemento Root.
   */
  public static function empty(): XMLExtended
  {
    return simplexml_load_string("<?xml version=\"1.0\" ?><Root></Root>", XMLExtended::class);
  }
}
