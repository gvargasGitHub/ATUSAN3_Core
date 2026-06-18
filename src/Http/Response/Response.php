<?php

namespace Atusan\Http\Response;

use Atusan\Controller\Module;
use Atusan\Json\JsonUtil;
use Atusan\Template\Template;

class Response implements ResponseInterface
{
  /**
   * @var array $data
   */
  private array $data = [];

  /**
   * 
   */
  static private ?self $response = null;

  /**
   * Response
   * Recibe y establece la propiedad "module". Este valor
   * puede ser nulo para el caso de los controladores de
   * tipo "Service".
   */
  function __construct()
  {
    $this->data = [];
  }

  public static function instance()
  {
    if (!isset(self::$response)) self::$response = new self();

    return self::$response;
  }

  /**
   * View
   * Este método es invocado desde el método "index" de cada Módulo
   * extendido de la clase Atusan\Module.
   * Finaliza el tratamiento de la petición invocando Template::render
   * el cual, recibe como parámetro el módulo presente para incluir y
   * procesar la plantilla (template) establecida.
   */
  public function view(Module $module): void
  {
    if (is_subclass_of($module, 'Atusan\\Controller\\ModuleNested'))
      Template::renderNested($module);
    else
      exit(Template::render($module));
  }

  /**
   * Add
   */
  public function add(string $key, mixed $value): void
  {
    $this->data[$key] = $value;
  }

  /**
   * Json
   */
  public function json(array $data = []): string
  {
    $this->data = array_merge($this->data, $data);
    exit(JsonUtil::toStringFormat(['status' => 'ok', 'data' => $this->data]));
  }

  /**
   * Error
   */
  public function error(string $message, string $detail): void
  {
    echo match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderError($message, $detail),
      'XHR' => JsonUtil::toStringFormat(['status' => 'error', 'message' => $message, 'detail' => $detail])
    };
  }

  /**
   * Notice
   */
  public function notice(string $message): void
  {
    echo match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderNotice($message),
      'XHR' => exit(JsonUtil::toStringFormat(['status' => 'notice', 'message' => $message, 'detail' => $message]))
    };
  }

  /**
   * Warning
   */
  public function warning(string $message): void
  {
    echo match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderWarning($message),
      'XHR' => exit(JsonUtil::toStringFormat(['status' => 'warning', 'message' => $message, 'detail' => $message]))
    };
  }

  /**
   * Unknow
   */
  public function unknow(string $message, string $detail): void
  {
    echo match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderUnknow($message, $detail),
      'XHR' => exit(JsonUtil::toStringFormat(['status' => 'unknow', 'message' => $message, 'detail' => $detail]))
    };
  }
}
