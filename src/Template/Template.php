<?php

namespace Atusan\Template;

use Atusan\Controller\Module;
use Atusan\FileSystem\FileSystem;
use Exception;

class Template
{
  public static Module $module;

  public static array $board = [
    'notice'=>[],
    'warning'=>[],
    'unknow'=>[],
    'exception'=>[],
    'error'=>[]
  ];
  /**
   * Render
   * @invoked by: Response::view
   */
  static public function render(Module $module)
  {
    self::$module = $module;

    $viewfile = self::getViewFilename();
    // getViewFilename generará una Excepción en caso de no encontrar la "Vista".

    require __DIR__ . DS . 'Views' . DS . 'document' . DS . 'document.begin.view.php';
    require $viewfile;
    require __DIR__ . DS . 'Views' . DS . 'document' . DS . 'document.close.view.php';
  }

  /**
   * Render Nested
   * @invoked by: Response::view
   */
  static public function renderNested(Module $module)
  {
    self::$module = $module;

    $viewfile = self::getViewFilename();
    // getViewFilename generará una Excepción en caso de no encontrar la "Vista".

    require __DIR__ . DS . 'Views' . DS . 'document' . DS . 'module.begin.view.php';
    require $viewfile;
  }

  /**
   * 
   */
  static protected function getViewFilename(): string
  {
    $viewfile = FileSystem::locateFile(APP_DIRECTORY . DS
      . implode(DS, ['Templates', self::$module->getTemplate()]), self::$module->getTemplate() . ".view");

    if ($viewfile === false)
      throw new Exception("El sistema no puede encontrar " . self::$module->template . ".view");

    return $viewfile;
  }

  /**
   * 
   */
  static public function extend(string $layout): void
  {
    require APP_DIRECTORY . DS . $layout;
  }

  static public function renderException(string $message, string $detail)
  {
    include __DIR__ . DS . 'Views' . DS . 'errors' . DS . 'exception.view.php';
  }

  static public function renderNotice(string $message)
  {
    self::$board['notice'][] = [$message, $message];
  }

  static public function renderWarning(string $message)
  {
    self::$board['warning'][] = [$message, $message];
  }

  static public function renderUnknow(string $message, string $detail)
  {
    include __DIR__ . DS . 'Views' . DS . 'errors' . DS . 'unknow.view.php';
  }

  static public function renderBoard()
  {
    foreach(self::$board as $k=>$stack) {
      foreach($stack as $data) {
        $message = $data[0];
        $detail = $data[1];
        include __DIR__ . DS . 'Views' . DS . 'errors' . DS . "{$k}.view.php";
      }
    }
  }
  /**
   * 
   */
  public static function writeCSS(): string
  {
    $dirs = [
      'reset.css',
      'flexbox.css',
      'alerts.css',
      'buttons.css',
      'dataform.css',
      'datamultiform.css',
      'datagrid.css',
      'datatree.css',
      'loader.css',
      'modal.css',
      'navbar.css',
      'subnavbar.css',
      'panels.css',
      'tabgroup.css',
      'sidebar.css'
    ];
    $content = '';
    foreach ($dirs as $css)
      $content .= file_get_contents(APP_CORE . '/src/Statics/css/' . $css) . "\n";

    foreach (\App\Config\Config::$cssResources as $css)
      $content .= file_get_contents(APP_ROOT . '/public/' . APP_NAME . '/css/' . $css) . "\n";

    return "<style>\n" . self::minificarCSS($content) . "\n</style>";
  }

  /**
   * 
   */
  public static function writeJS(): string
  {
    $dirs = [
      'atusan.js',
      'controls.js',
      'components.js',
      'controllers.js'
    ];
    $content = '';
    foreach ($dirs as $js) {

      $content .= self::minificarJS(file_get_contents(APP_CORE . '/src/Statics/js/' . $js)) . "\n";
    }

    foreach (\App\Config\Config::$jsResources as $js)
      $content .= self::minificarJS(file_get_contents(APP_ROOT . '/public/' . APP_NAME . '/js/' . $js)) . "\n";

    return "<script>\n$content</script>\n";
  }

  /**
   * Minificador seguro para JS
   */
  public static function minificarJS(string $input) : string
  {
    $output = '';
    $length = strlen($input);

    $inString = false;
    $stringChar = '';
    $inRegex = false;
    $inSingleComment = false;
    $inMultiComment = false;

    for ($i = 0; $i < $length; $i++) {
      $char = $input[$i];
      $next = $i + 1 < $length ? $input[$i + 1] : '';

      // =========================
      // Comentarios
      // =========================
      if (!$inString && !$inRegex) {
        if ($char === '/' && $next === '/') {
          $inSingleComment = true;
          $i++;
          continue;
        }

        if ($char === '/' && $next === '*') {
          $inMultiComment = true;
          $i++;
          continue;
        }
      }

      if ($inSingleComment && ($char === "\n" || $char === "\r")) {
        $inSingleComment = false;
        continue;
      }

      if ($inMultiComment && $char === '*' && $next === '/') {
        $inMultiComment = false;
        $i++;
        continue;
      }

      if ($inSingleComment || $inMultiComment) {
        continue;
      }

      // =========================
      // Strings
      // =========================
      if (!$inRegex && ($char === '"' || $char === "'" || $char === '`')) {
        if ($inString && $char === $stringChar) {
          $inString = false;
        } elseif (!$inString) {
          $inString = true;
          $stringChar = $char;
        }

        $output .= $char;
        continue;
      }

      if ($inString) {
        $output .= $char;

        if ($char === '/' && $next) {
          $output .= $next;
          $i++;
        }

        continue;
      }

      // =========================
      // Manejo de espacios
      // =========================
      if (ctype_space($char)) {
        if ($output !== '' && !ctype_space(substr($output, -1))) {
          $output .= ' ';
        }
        continue;
      }
      // Quitar espacios antes de símbolos
      if (strpos('{}[]();,:=+-*/<>', $char) !== false) {
        $output = rtrim($output);
      }

      $output .= $char;
    }

    return trim($output);
  }


  /**
   * Minificador seguro para CSS
   */
  public static function minificarCSS(string $input) : string
  {
    // Eliminar comentarios
    $input = preg_replace('!/\*.*?\*/!s', '', $input);

    // Eliminar espacios innecesarios
    $input = preg_replace('/\s+/', ' ', $input);
    $input = preg_replace('/\s*([{}:;,>])\s*/', '$1', $input);

    // Optimización extra
    $input = str_replace(';}', '}', $input);

    return trim($input);
  }
}
