<?php

namespace Atusan\Route;

use Atusan\Session\Session;
use Exception;

class Route
{
  use RouteBase;
  /**
   * Implementation
   */
  static public function implement(): void
  {
    include APP_DIRECTORY . DS . 'Route.php';
  }

  /**
   * Resolve
   */
  static public function resolve(): array
  {
    if (($routeType = self::findRouteByUri($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])) === false)
      throw new Exception("La ruta {$_SERVER['REQUEST_URI']} no ha sido implementada para {$_SERVER['REQUEST_METHOD']}.");

    if ($routeType->middlewareState) {
      if (!Session::get($routeType->middlewareFilter)) $routeType = Route::redirect($routeType->middlewareRedirectUri);
    }

    $controller = new $routeType->controller();

    // Valida que exista el método establecido en Route para resolver la petición
    if (!method_exists($controller, $routeType->resolve))
      throw new Exception("El método {$routeType->resolve} no existe para {$controller->name}");

    return [$controller, $routeType];
  }

  /**
   * Redirect
   */
  static public function redirect($uri)
  {
    if (($routeType = self::findRouteByUri('GET', $uri)) === false)
      throw new Exception("La ruta {$uri} no ha sido implementada para GET.");

    return $routeType;
  }

  /**
   * Find Route By URI
   */
  static protected function findRouteByUri($method, $uri): mixed
  {
    foreach (self::$routes[$method] as $route)
      if ($route->uri == $uri) return $route;

    return false;
  }

  /**
   * MiddleWare
   */
  static public function middleware(string $filter, callable $addRoutes, string $redirect = '/')
  {
    self::$middlewareState = 1;
    self::$middlewareFilter = $filter;
    self::$middlewareRedirectUri = $redirect;

    if (!is_callable($addRoutes)) throw new Exception('El segundo parámetro debe ser una función.');

    call_user_func($addRoutes);
  }
}
