<?php

namespace Atusan\Route;

use Atusan\Types\RouteType;

trait TraitRoute
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
  static protected function add(
    string $method, 
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    array_push(self::$routes[$method], new RouteType($uri, $controller, $resolve, self::$middlewareState, self::$middlewareFilter, self::$middlewareRedirectUri));
  }

  /**
   * Add GET Route
   */
  static public function get(
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    self::add('GET', $uri, $controller, $resolve);
  }

  static public function post(
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    self::add('POST', $uri, $controller, $resolve);
  }

  /**
   * Add AJAX Route
   */
  static public function ajax(
    string $uri, 
    string $controller, 
    string $resolve) : void
  {
    self::add('POST', $uri, $controller, $resolve);
  }

  /**
   * Add Nested Route
   */
  static public function nested(
    string $uri, 
    string $controller) : void
  {
    self::add('POST', $uri, $controller, 'nestedToJson');
  }

  /**
   * Add PUT Route
   */
  static public function put(
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    self::add('PUT', $uri, $controller, $resolve);
  }

  /**
   * Add PATCH Route
   */
  static public function patch(
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    self::add('PATCH', $uri, $controller, $resolve);
  }

  /**
   * Add DELETE Route
   */
  static public function delete(
    string $uri, 
    string $controller, 
    string $resolve = 'index') : void
  {
    self::add('DELETE', $uri, $controller, $resolve);
  }
}
