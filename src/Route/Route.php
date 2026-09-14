<?php

namespace Atusan\Route;

use Atusan\Http\Request\Request;
use Atusan\Session\Session;
use Atusan\Types\RouteType;
use Exception;

class Route
{
  use TraitRoute;
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
    // Se obtiene "URI" de variable establecida por .htaccess
    $uri = (!isset($_GET['uri']) || empty($_GET['uri'])) ? '/' : $_GET['uri'];

    // busca la coincidencia en Rutas
    if (($routeType = self::findRouteByUri($_SERVER['REQUEST_METHOD'], $uri)) === false)
      throw new Exception("La ruta {$uri} no ha sido implementada para {$_SERVER['REQUEST_METHOD']}.");
    
    // Si la Ruta tiene el "estado middleware" activo valida que
    // exista en la Sesión el valor, si no, entonces, re-direcciona
    // la petición a la ruta establecida 
    if ($routeType->middlewareState) {
      if (!Session::get($routeType->middlewareFilter)) $routeType = Route::redirect($routeType->middlewareRedirectUri);
    }

    // Obtiene los parámetros de "uri"
    self::parseUriParams($routeType->uri, $uri);

    $controller = new $routeType->controller();

    // Valida que exista el método establecido en Route para resolver la petición
    if (!method_exists($controller, $routeType->resolve))
      throw new Exception("El método {$routeType->resolve} no existe para {$controller->name}");

    return [$controller, $routeType];
  }

  /**
   * Redirect
   */
  static public function redirect(string $uri)
  {
    if (($routeType = self::findRouteByUri('GET', $uri)) === false)
      throw new Exception("La ruta {$uri} de direccionamiento no ha sido implementada para GET.");

    return $routeType;
  }

  /**
   * Find Route By URI
   */
  static protected function findRouteByUri(string $method, string $uri): RouteType | false
  {
    foreach (self::$routes[$method] as $type) {
      // Valida si la ruta en turno tiene declarados parámetros
      if(preg_match('#\{([A-Za-z0-9\-\_\@|\.])+\}#', $type->uri)) {
        // crea el patrón de validación de "uri"
        $patt = '#^' .  preg_replace_callback('#\{([A-Za-z0-9\-\_\@|\.])+\}#',
          function(){
            return '([A-Za-z0-9\-\_\@|\.])+';
          }, $type->uri) . '(/)*$#';;
        // si la ruta coincide, entonces retorna el tipo
        if (preg_match($patt, $uri)) return $type;
      } elseif ($type->uri == $uri) return $type;
    }

    return false;
  }

  /**
   * Parse Uri Params
   */
  static protected function parseUriParams(string $typeUri, string $uri): void
  {
    $request = Request::instance();

    $typeUriParts = explode('/', $typeUri);
    $uriParts = explode('/', $uri);

    foreach($typeUriParts as $i=>$segment) {
      if(preg_match('#\{([A-Za-z0-9\-\_\@|\.])+\}#', $segment)) {
        $key = str_replace(['{','}'], '', $segment);
        $request->addUriParam($key, $uriParts[$i]);
      }
    }
  }

  /**
   * MiddleWare
   */
  static public function middleware(
    string $filter,
    callable $addRoutes,
    string $redirect = '/'
  ): void {
    $previousState = self::$middlewareState;
    $previousFilter = self::$middlewareFilter;
    $previousRedirect = self::$middlewareRedirectUri;

    self::$middlewareState = 1;
    self::$middlewareFilter = $filter;
    self::$middlewareRedirectUri = $redirect;

    try {
        $addRoutes();
    } finally {
        self::$middlewareState = $previousState;
        self::$middlewareFilter = $previousFilter;
        self::$middlewareRedirectUri = $previousRedirect;
    }
  }
}
