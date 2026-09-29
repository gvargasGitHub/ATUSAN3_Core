<?php

namespace Atusan\Route;

use Atusan\Exceptions\NotFoundException;
use Atusan\Session\Session;
use Atusan\Types\RouteType;

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
   * Obtiene el parámetro "uri" del URL de la petición y busca la coincidencia
   * en las declaraciones de rutas. En caso de que la ruta encontrada, esté
   * declarada dentro de un filtro de validación, verifica el filtro.
   * Resolve retornará:
   *  - RouteType
   *  - HttpUri
   * 
   * Resolve disparará un error controlado de tipo NOTICE en caso de error (v3.0.20).
   */
  static public function resolve(): array
  {
    // Se obtiene "URI" de variable establecida por .htaccess
    $httpURI = (!isset($_GET['uri']) || empty($_GET['uri'])) ? '/' : $_GET['uri'];
    
    // v3.0.21 Implementa rutas
    self::implement();

    // busca la coincidencia en Rutas
    if (($routeType = self::findRouteByUri($_SERVER['REQUEST_METHOD'], $httpURI)) === false) {
      // v3.0.20
      throw new NotFoundException("La ruta {$httpURI} no ha sido implementada para {$_SERVER['REQUEST_METHOD']}.");
    }
    // Si la Ruta tiene el "estado middleware" activo valida que
    // exista en la Sesión el valor, si no, entonces, re-direcciona
    // la petición a la ruta establecida 
    if ($routeType->middlewareState) {
      if (Session::get($routeType->middlewareFilter)) {
        $routeType = Route::redirect($routeType->middlewareRedirectUri);
      } else  {
        // v3.0.20
        throw new NotFoundException("El filtro {$routeType->middlewareFilter} no existe.");
      }
    }

    return [$routeType, $httpURI];
  }

  /**
   * Redirect
   */
  static public function redirect(
    string $redirectURI) : RouteType
  {
    if (($routeType = self::findRouteByUri('GET', $redirectURI)) === false) {
      // v3.0.20
      throw new NotFoundException("La ruta {$redirectURI} de direccionamiento no ha sido implementada para GET.");
    }

    return $routeType;
  }

  /**
   * Find Route By URI
   * Recibe el método de la petición y la ruta URI entregada por .htaccess.
   * Recorre las rutas establecidas bajo el método y busca la coincidencia.
   * 
   * @param string $method
   * @param string $httpURI
   * 
   * @return RouteType | false
   */
  static protected function findRouteByUri(
    string $method, 
    string $httpURI): RouteType | false
  {
    // Recorre las rutas establecidas bajo el método
    foreach (self::$routes[$method] as $routeType) {
      // Verifica si la ruta tiene declaración de parámetros
      if (preg_match('#\{([A-Za-z0-9\-\_\@|\.])+\}#', $routeType->uri)) {
        // crea el patrón de validación de "uri" eliminando las llaves {} de
        // la URI declarada y poder verificar la URI recibida
        $patt = '#^' .  preg_replace_callback('#\{([A-Za-z0-9\-\_\@|\.])+\}#',
          function(){
            return '([A-Za-z0-9\-\_\@|\.])+';
          }, $routeType->uri) . '(/)*$#';

        // Utiliza el patrón generado y verifica si la ruta coincide
        if (preg_match($patt, $httpURI)) return $routeType;

      } elseif ($routeType->uri == $httpURI) {
        return $routeType;
      }
    }

    return false;
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
