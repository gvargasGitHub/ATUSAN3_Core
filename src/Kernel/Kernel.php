<?php

namespace Atusan\Kernel;

use Atusan\Exceptions\NotFoundException;
use Atusan\Http\Request\Request;
use Atusan\Route\Route;
use Atusan\Security\SecurityMiddleware;

class Kernel
{
  /**
   * Execute
   */
  static public function execute(Request $request)
  {
    // Kernel solicita a SecurityMiddleware ejecute los procesos de inicio:
    // a) Inicia Session
    // b) Escribe encabezados de seguridad
    // c) Valida método de petición
    SecurityMiddleware::handle($request);

    // Route: Resuelve la ruta (uri) de la petición.
    // En caso de que la ruta no sea reconocida, Route::resolve generará
    // una Exception de tipo NotFoundException.
    // v3.0.21 Route::resolve implementa las rutas de App
    [$routeType, $httpURI] = Route::resolve();

    // v3.0.20 Kernel crea la instancia del controlador.
    $controller = $routeType->controller::getNew();

    // v3.0.20 Kernel valida si tanto el controlador como el método existen
    if (!method_exists($controller, $routeType->resolve)) {
      throw new NotFoundException(
        "El método {$routeType->resolve} no existe para {$controller->name}");
    }
    
    // Una vez que se ha resuelto la ruta, Kernel solicitará a Request que
    // analice y, en su caso, obtenga los valores de los parámetros declarados.

    // Resuelve la petición. Ahora, al método del controlador invocado
    // se le pasan los parámetros de la ruta (uri) de la petición
    // emulando el comportamiento de CodeIgniter.
    call_user_func_array(
      [$controller, $routeType->resolve], 
      $request->getUriParams($routeType->uri, $httpURI)
    );
  }
}
