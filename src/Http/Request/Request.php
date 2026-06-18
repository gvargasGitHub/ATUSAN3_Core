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
   * 
   */
  private static ?self $request = null;

  private array $get;
  private array $post;
  private array $server;
  private array $headers;
  private array $json;

  private FilesUploadedIterator $files;

  private function __construct()
  {
    $this->get     = $_GET;
    $this->post    = $_POST;
    $this->server  = $_SERVER;
    $this->headers = $this->parseHeaders();
    $this->json    = $this->parseJsonBody();
    $this->files   = $this->parseFiles();
  }

  public static function instance(): self
  {
    if (self::$request == NULL)
      self::$request = new self();

    return self::$request;
  }

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
   * 
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

    return $default;
  }

  public function files(): FilesUploadedIterator
  {
    return $this->files;
  }

  public function all(): array
  {
    return array_merge($this->get, $this->post, $this->json);
  }
  
  /**
   * 
   */
  function has(string $key): bool
  {
    return !is_null($this->get($key));
  }

  /**
   * 
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

  public function header(string $key): ?string
  {
    return $this->headers[strtolower($key)] ?? null;
  }

  public function isJson(): bool
  {
    return str_contains(
      strtolower($this->header('Content-Type') ?? ''),
      'application/json'
    );
  }

  public function json(): array
  {
    return $this->json;
  }

  private function parseJsonBody(): array
  {
    if ($this->method() === 'GET') {
      return [];
    }

    $contentType = strtolower($this->header('Content-Type') ?? '');

    if (!str_contains($contentType, 'application/json')) {
      return [];
    }

    $raw = file_get_contents('php://input');
    if (!$raw) {
      return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
  }

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
