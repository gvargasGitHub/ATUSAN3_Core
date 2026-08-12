<?php

namespace Atusan\Http\Request;

use Atusan\Iterators\FilesUploadedIterator;
use Atusan\Log\Log;
use Atusan\Types\FileUploadedType;

class Request implements RequestInterface
{
  /**
   * @var array routeParams
   */
  private $routeParams;

  /**
   * Get the singleton instance of the request
   */
  private static ?self $request = null;

  private array $get;
  private array $post;
  private array $server;
  private array $headers;
  private array $json;
  /**
   * @var array $uriparams
   * Este arreglo contiene los parámetros de la "uri" que son obtenidos
   * desde la ruta establecida en "Route.php". Estos parámetros son 
   * agregados al objeto Request mediante el método "addUriParam" invocado 
   * desde el método "parseUriParams" de la clase "Route". 
   * Los parámetros de la "uri" son aquellos que están definidos en la ruta 
   * con llaves, por ejemplo: "/user/{id}".
   * Estos parámetros se pasan como argumentos al método del controlador 
   * invocado para resolver la petición.
   */
  private array $uriparams;

  private bool $jsonBodyInvalid = false;
  private ?string $jsonBodyError = null;

  private FilesUploadedIterator $files;

  private function __construct()
  {
    $this->get     = $_GET;
    $this->post    = $_POST;
    $this->server  = $_SERVER;
    $this->headers = $this->parseHeaders();
    $this->json    = $this->parseJsonBody();
    $this->files   = $this->parseFiles();
    $this->uriparams = [];
  }

  /**
   * Get the singleton instance of the request
   */
  public static function instance(): self
  {
    if (self::$request == NULL)
      self::$request = new self();

    return self::$request;
  }
  /**
   * Alias for instance() method to capture the current request
   */
  public static function capture(): self
  {
    return self::instance();
  }

  public function method(): string
  {
    return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
  }

  public function uri(): string
  {
    return strtok($this->server['REQUEST_URI'] ?? '/', '?');
  }

  /**
   * Get a value from the request
   */
  public function get(string $key, mixed $default = null): mixed
  {
    if (array_key_exists($key, $this->json)) {
      return $this->json[$key];
    }

    if (array_key_exists($key, $this->post)) {
      return $this->post[$key];
    }

    if (array_key_exists($key, $this->get)) {
      return $this->get[$key];
    }

    if (array_key_exists($key, $this->uriparams)) {
      return $this->uriparams[$key];
    }

    return $default;
  }

  public function all(): array
  {
    return array_merge($this->get, $this->post, $this->json);
  }

  // ----------------------------------
  // URI Parameters
  // ----------------------------------
  /**
   * Add Uri Param
   * @invoked: Atusan\Route\Route::parseUriParams
   */
  public function addUriParam(string $key, mixed $value): void
  {
    $this->uriparams[$key] = $value;
  }

  /**
   * Get Uri Params
   * Este método retorna un arreglo asociativo con los parámetros de la "uri" que fueron
   * definidos en la ruta establecida en "Route.php". Este método es invocado desde el 
   * método "execute" de la clase "Kernel.php" para pasar los parámetros de la "uri" al
   * controlador invocado para resolver la petición. Los parámetros de la "uri" son 
   * aquellos que están definidos en la ruta con llaves, por ejemplo: "/user/{id}".
   * @return array
   */
  public function getUriParams(): array
  {
    return $this->uriparams;
  }

  /**
   * Get Uploaded Files
   */
  public function files(): FilesUploadedIterator
  {
    return $this->files;
  }
  
  /**
   * Check if a value exists in the request
   */
  function has(string $key): bool
  {
    return !is_null($this->get($key));
  }

  /**
   * Parse uploaded files
   */
  public function parseFiles(): FilesUploadedIterator
  {
    $iterator = new FilesUploadedIterator();

    foreach ($_FILES as $inputName => $data) {
      // Agrega el "input"
      $iterator->add($inputName);

      // Valida si el "control" contiene 1 o mas archivos en $_FILES
      if (is_array($data['error'])) {
        $nof = count($data['error']);

        for ($f = 0; $f < $nof; $f++) {
          $iterator->$inputName->add(new FileUploadedType(
            $data['error'][$f],
            $data['name'][$f],
            $data['type'][$f],
            $data['size'][$f],
            $data['tmp_name'][$f]
          ));
        }
      } else {
        $iterator->$inputName->add(new FileUploadedType(
          $data['error'],
          $data['name'],
          $data['type'],
          $data['size'],
          $data['tmp_name']
        ));
      }
    }

    return $iterator;
  }
  
  /**
   * Get Route Param
   */
  public function getRouteInput(string $key): mixed
  {
    return array_key_exists($key, $this->routeParams) ? $this->routeParams[$key] : null;
  }

  /**
   * Get Header Value
   */
  public function header(string $key): ?string
  {
    return $this->headers[strtolower($key)] ?? null;
  }

  /**
   * Check if the request body is JSON
   */
  public function isJson(): bool
  {
    return str_contains(
      strtolower($this->header('Content-Type') ?? ''),
      'application/json'
    );
  }

  /**
   * Get the JSON body of the request
   */
  public function json(): array
  {
    return $this->json;
  }

  /**
   * Parse the JSON body
   * ChatGPT/ATUSAN 3/Parse JSON Body
   * @return array
   */
  private function parseJsonBody(): array
  {
      if ($this->method() === 'GET') {
          return [];
      }

      $contentType = strtolower(
          trim(explode(';', $this->header('Content-Type') ?? '', 2)[0])
      );

      if ($contentType !== 'application/json') {
          return [];
      }

      $raw = file_get_contents('php://input');

      if ($raw === false || $raw === '') {
          return [];
      }

      $data = json_decode($raw, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        $this->jsonBodyInvalid = true;
        $this->jsonBodyError = json_last_error_msg();
        return [];
      }

      return is_array($data) ? $data : [];
  }

  public function hasInvalidJsonBody(): bool
  {
      return $this->jsonBodyInvalid;
  }

  public function jsonBodyError(): ?string
  {
      return $this->jsonBodyError;
  }
  /**
   * Parse the headers from the server variables
   */
  private function parseHeaders(): array
  {
    $headers = [];

    foreach ($this->server as $key => $value) {
      if (str_starts_with($key, 'HTTP_')) {
        $name = strtolower(str_replace('_', '-', substr($key, 5)));
        $headers[$name] = $value;
      }
    }

    return $headers;
  }
}