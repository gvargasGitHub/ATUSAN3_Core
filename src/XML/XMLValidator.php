<?php

namespace Atusan\XML;

class XMLValidator
{
  public static function toLower(string $filename): string
  {
    // Carga el contenido del archivo XML
    $xml = file_get_contents($filename);

    // -----------------------------
    // 1. Convertir nombres de etiquetas de apertura
    // -----------------------------
    $xml = preg_replace_callback(
      '/<([A-Za-z_][A-Za-z0-9:_-]*)(\s|>)/',
      function ($m) {
        return '<' . strtolower($m[1]) . $m[2];
      },
      $xml
    );

    // -----------------------------
    // 2. Convertir nombres de atributos
    // -----------------------------
    $xml = preg_replace_callback(
      '/\s([A-Za-z_][A-Za-z0-9:_-]*)=/',
      function ($m) {
        return ' ' . strtolower($m[1]) . '=';
      },
      $xml
    );

    // -----------------------------
    // 3. Convertir nombres de etiquetas de cierre
    // -----------------------------
    $xml = preg_replace_callback(
      '/<\/([A-Za-z_][A-Za-z0-9:_-]*)>/',
      function ($m) {
        return '</' . strtolower($m[1]) . '>';
      },
      $xml
    );

    return $xml;
  }

  public static function isView(XMLExtended $xml): bool
  {
    $elmName = $xml->getName();

    if ($elmName === 'view') return true;

    if (!class_exists("Atusan\\Components\\{$elmName}")) return false;

    if ($xml->hasAttribute('class')) {
      $xml->attributes()->class = "Atusan\\Components\\{$elmName}";
    } else {
      $xml->addAttribute('class', "Atusan\\Components\\{$elmName}");
    }

    return true;
  }

  public static function exists($filename): bool
  {
    return @file_exists($filename);
  }
}
