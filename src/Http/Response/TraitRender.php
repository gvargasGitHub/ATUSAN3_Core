<?php
namespace Atusan\Http\Response;

use Atusan\Controller\Module as Controller;
use Atusan\Exceptions\InternalServerException;
use Atusan\FileSystem\FileSystem;

trait TraitRender {
  /**
   * Render View (HTML)
   */
  private function renderView(
    bool $isNested, 
    Controller $module): string
  {
    // Establece el nombre del módulo para ser usado en la plantilla (template)
    $moduleName = $module->name;

    $templateFile = FileSystem::locateFile(APP_DIRECTORY . DS
      . implode(DS, ['Templates', $module->getTemplate()]), 
      $module->getTemplate() . ".view");

    $dirViews = __DIR__ . DS . 'Views' . DS . 'document' . DS;

    // Inicia la captura de buffer de salida contralada.
    // Aqui se atrapará todo lo generado por el controlador
    ob_start();

    // establece $className el cual pasará al contexto de
    // las vistas para la declaración de los objetos JS
    $className = $isNested ? 'ModuleNested' : 'Module';
    
    $prefix = $dirViews;
    $prefix .= $isNested ? 'module.' : 'document.';

    require $prefix . 'begin.view.php';

    if ($templateFile)
      require $templateFile;
    else
      echo $module->make();

    if (!$isNested) require $prefix . 'close.view.php';

    // finalmente, retorna el buffer y limpia
    return ob_get_clean();
  }

  /**
   * Render Html
   */
  private function renderHtml(
    bool $isNested, 
    Controller $module): string
  {
    return $this->renderView($isNested, $module);
  }

  /**
   * Render JSON
   */
  private function renderJson(
    string $status, 
    string $message, 
    ?array $data, 
    ?string $detail): string
  {
    $body = [
      'status' => $status,
      'message' => $message
    ];

    if ($data !== null) $body['data'] = $data;

    if (APP_DEBUG && $detail !== null) $body['detail'] = $detail;

    $res = json_encode($body, JSON_NUMERIC_CHECK);

    if (json_last_error() !== JSON_ERROR_NONE)
      throw new InternalServerException(json_last_error_msg());

    return $res;
  }

  /**
   * Render Board (HTMl)
   */
  public function renderBoard(
    array $board) : string
  {
    ob_start();
    foreach($board as $k=>$stack) {
      foreach($stack as $data) {
        $message = $data[0];
        $detail = $data[1];
        include __DIR__ . DS . 'Views' . DS . 'errors' . DS . "{$k}.view.php";
      }
    }

    return ob_get_clean();
  }

  public function renderError(
    string $contentTypeRequested,
    string $type,
    string $message, 
    ?string $detail): string
  {
    if ($contentTypeRequested == 'JSON') {
      // $type siempre será pasará "error" como "status" en peticiones JSON
      return self::renderJson('error', $message, null, $detail);
    } else {
      ob_start();
      include __DIR__ . DS . 'Views' . DS . 'errors' . DS . 'error.view.php';
      return ob_get_clean();
    }
  }

  /**
   * Write CSS
   */
  public function writeCSS(): string
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
   * Write JS
   */
  public function writeJS(): string
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
  protected function minificarJS(string $input) : string
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
  protected function minificarCSS(string $input) : string
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