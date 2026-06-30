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
    ob_start();

    // Si el "controller" es una subclase de "ModuleInterface", entonces,
    // invocará la carga de los Componentes de "Module"
    // if (is_subclass_of($controller, 'Atusan\\Module\\ModuleInterface'))
    //   $controller->attachComponents();

    // Resuelve la petición
    $controller->{$routeType->resolve}();
  }
}
