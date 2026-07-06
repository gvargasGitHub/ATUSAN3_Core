<?php

namespace Atusan\Kernel;

use Atusan\Http\Request\Request;
use Atusan\Route\Route;
use Atusan\Security\SecurityMiddleware;

class Kernel
{
  /**
   * Handle
   */
  static public function handle(): Request
  {
    Route::implement();

    return Request::capture();
  }

  /**
   * Execute
   */
  static public function execute(Request $request)
  {
    // SecurityMiddleware:
    // a) Inicia Session
    // b) Escribe encabezados de seguridad
    // c) Valida método de petición
    SecurityMiddleware::handle($request);

    // Route: Resuelve la ruta (uri) de la petición
    [$controller, $routeType] = Route::resolve();

    // Inicia captura de salida
    // ob_start();

    // Resuelve la petición. Ahora, al método del controlador invocado
    // se le pasan los parámetros de la ruta (uri) de la petición.
    call_user_func_array(
      [$controller, $routeType->resolve], 
      $request->getUriParams()
    );
  }
}
