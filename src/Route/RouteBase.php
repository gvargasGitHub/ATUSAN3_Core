<?php

namespace Atusan\Route;

use Atusan\Types\RouteType;

trait RouteBase
{
  static private $routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'PATCH' => [],
    'DELETE' => []
  ];

  static protected $middlewareState = 0;
  static protected $middlewareFilter = '';
  static protected $middlewareRedirectUri = '';

  /**
   * Add Route
   */
  static protected function add(string $method, string $uri, string $controller, string $resolve = 'index')
  {
    array_push(self::$routes[$method], new RouteType($uri, $controller, $resolve, self::$middlewareState, self::$middlewareFilter, self::$middlewareRedirectUri));
  }


  static public function get(string $uri, string $controller)
  {
    self::add('GET', $uri, $controller);
  }

  static public function post(string $uri, string $controller)
  {
    self::add('POST', $uri, $controller);
  }

  static public function ajax(string $uri, string $controller, string $resolve)
  {
    self::add('POST', $uri, $controller, $resolve);
  }

  static public function nested(string $uri, string $controller)
  {
    self::add('POST', $uri, $controller, 'nestedToJson');
  }

  static public function put(string $uri, string $controller)
  {
    self::add('PUT', $uri, $controller);
  }

  static public function patch(string $uri, string $controller)
  {
    self::add('PATCH', $uri, $controller);
  }

  static public function delete(string $uri, string $controller)
  {
    self::add('DELETE', $uri, $controller);
  }
}
