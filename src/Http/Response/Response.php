<?php

namespace Atusan\Http\Response;

use Atusan\Controller\Module;
use Atusan\Http\Request\Request;
use Atusan\Json\JsonUtil;
use Atusan\Template\Template;

class Response implements ResponseInterface
{
  /**
   * @var array $data
   */
  private array $data = [];

  /**
   * @var String $message
   */
  private String $message = '';

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
   * extendido de la clase Atusan\Controller\Module.
   * Finaliza el tratamiento de la petición invocando Template::render
   * el cual, recibe como parámetro el módulo presente para incluir y
   * procesar la plantilla (template) establecida.
   */
  public function view(Module $module): void
  {
    $className = ($module instanceof \Atusan\Controller\ModuleNested) ? 'ModuleNested' : 'Module';

    echo Template::render($className, $module);
  }

  /**
   * Add
   */
  public function add(string $key, mixed $value): void
  {
    $this->data[$key] = $value;
  }

  /**
   * Message
   */
  public function message(string $message): void
  {
    $this->message = $message;
  }

  /**
   * Json
   */
  public function json(array $data = []): void
  {
    if(Request::instance()->hasInvalidJsonBody()) {
      $this->message = Request::instance()->jsonBodyError();
      $this->exception('Petición con JSON inválido.', $this->message);
    } else {
      $this->data = array_merge($this->data, $data);
      $this->jsonResponse(['status' => 'ok', 'message' => $this->message, 'data' => $this->data]);
    }
  }

  /**
   * Exceptions
   */
  public function exception(string $message, string $detail): void
  {
    match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderException($message, $detail),
      'JSON' => $this->jsonResponse(['status' => 'error', 'message' => $message, 'detail' => $detail])
    };
  }

  /**
   * Notice
   */
  public function notice(string $message): void
  {
    match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderNotice($message),
      'JSON' => $this->jsonResponse(['status' => 'notice', 'message' => $message, 'detail' => $message])
    };
  }

  /**
   * Warning
   */
  public function warning(string $message): void
  {
    match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderWarning($message),
      'JSON' => $this->jsonResponse(['status' => 'warning', 'message' => $message, 'detail' => $message])
    };
  }

  /**
   * Unknow
   */
  public function unknow(string $message, string $detail): void
  {
    match (CONTENT_TYPE_REQUESTED) {
      'HTML' => Template::renderUnknow($message, $detail),
      'JSON' => $this->jsonResponse(['status' => 'unknow', 'message' => $message, 'detail' => $detail])
    };
  }

  /**
   * Json Response (ChatGPT/ATUSAN 3/Definir CONTENT_TYPE_REQUESTED)
   */
  private function jsonResponse(array $data): never
  {
      header('Content-Type: application/json; charset=utf-8');

      exit(JsonUtil::toStringFormat($data));
  }

  /**
   * Status
   * ChatGPT/ATUSAN 3/Parse JSON Body
   */
  public static function status(int $code): void
  {
      http_response_code($code);
  }
}
