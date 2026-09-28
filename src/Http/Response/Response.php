<?php

namespace Atusan\Http\Response;

use Atusan\Controller\Module as Controller;

class Response implements ResponseInterface
{
  use TraitRender;

  /**
   * @var array $data
   */
  private array $data = [];

  /**
   * @var String $message
   */
  private String $message = '';

  /**
   * @var array $board
   */
  private array $board = [
    'notice'=>[],
    'warning'=>[]
  ];

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
    $this->board = [];
  }

  /**
   * Instance
   */
  public static function instance()
  {
    if (!isset(self::$response)) self::$response = new self();

    return self::$response;
  }

  /**
   * Add
   */
  public function add(string $key, mixed $value): void
  {
    $this->data[$key] = $value;
  }

  /**
   * Add Notice (board)
   * @param String $message
   */
  public function addNotice(string $message):void
  {
    $this->board['notice'][] = [$message, $message];
  }

  /**
   * Add Warning (board)
   * @param String $message
   */
  public function addWarning(string $message):void
  {
    $this->board['warning'][] = [$message, $message];
  }

  /**
   * Message
   * @param String $message
   */
  public function message(string $message): void
  {
    $this->message = $message;
  }

  /**
   * View
   * Este método es invocado desde el método "index" de cada Módulo
   * extendido de la clase Atusan\Controller\Module.
   * Finaliza el tratamiento de la petición invocando renderView
   * el cual, recibe como parámetro el módulo presente para incluir y
   * procesar la plantilla (template) establecida.
   */
  public function view(
    Controller $module): void
  {
    // establece los Encabezados de Documento
    header('Content-Type: text/html; charset=utf-8');

    // Establece si el módulo solicitado es de la clase ModuleNested.
    // Esta distinción es importante porque determina:
    // a) Contrucción de la vista.
    // b) Finalizar o no
    $isNested = ($module instanceof \Atusan\Controller\ModuleNested);

    echo $this->renderView($isNested, $module);
  }

  /**
   * Html
   * Este método es invocado desde el método "index" de cada Módulo
   * extendido de la clase Atusan\Controller\Module.
   * Finaliza el tratamiento de la petición invocando renderView
   * el cual, recibe como parámetro el módulo presente para incluir y
   * procesar la plantilla (template) establecida.
   */
  public function html(
    Controller $module): void
  {
    $this->view($module);
  }

  /**
   * Json
   * Este método es invocado por el método "resolve" de cada Módulo o Servicio.
   */
  public function json(array $data = []): void
  {
    header('Content-Type: application/json; charset=utf-8');

    // mezcla los valores previamente agregados con los pasados
    $this->data = array_merge($this->data, $data);
    // emite la respuesta
    echo self::renderJson('ok', $this->message, $this->data, null);
  }

   /**
   * Notice (JSON)
   * Este método solo es deben ser invocados desde los métodos "resolve" para
   * responder a peticiones JSON (AJAX, Fetch, APIs).
   * Este método no corresponde a un error de tipo http.
   */
  public function notice(
    string $message): void
  {
    header('Content-Type: application/json; charset=utf-8');

    echo self::renderJson(
        'notice',
        $message,
        null,
        null);
    exit();
  }

  /**
   * Warning  (JSON)
   * Este método solo es deben ser invocados desde los métodos "resolve" para
   * responder a peticiones JSON (AJAX, Fetch, APIs).
   * Este método no corresponde a un error de tipo http.
   */
  public function warning(
    string $message): void
  {
    header('Content-Type: application/json; charset=utf-8');

    echo self::renderJson(
        'warning',
        $message,
        null,
        null);
    exit();
  }

  /**
   * Error (JSON)
   */
  public function error(
    int $code,
    string $message
  ) : void
  {
    header('Content-Type: application/json; charset=utf-8');

    $this->throwExitError('JSON', $code, $message, null, null);
  }

  /**
   * Write Board
   * Este método despliega contenido HTML.
   * 
   * @invoked by: Views/document/document.begin.view
   */
  protected function writeBoard(): void
  {
    echo $this->renderBoard($this->board);
  }

  /**
   * Throw Exit Error
   */
  public function throwExitError(
    string $contentTypeRequested,
    int $code,
    string $message,
    ?string $type, 
    ?string $detail
    ) : never
  {
    $this->status($code);

    $type = ($type == null) ? 'error' : $type;

    echo self::renderError($contentTypeRequested, $type, $message, $detail);
    exit(1);
  }
  
  /**
   * Status
   * ChatGPT/ATUSAN 3/Parse JSON Body
   */
  public function status(int $code): void
  {
      http_response_code($code);
  }
}
