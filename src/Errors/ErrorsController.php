<?php

namespace Atusan\Errors;

use Atusan\Log\Log;
use Atusan\Http\Response\Response;
use Throwable;

class ErrorsController
{
  /**
   * Handle Error
   * Gestor de errores personalizados.
   * - Errores críticos.
   * - Errores generados (trigger_error()).
   * 
   * E_USER_ERROR es obsoleto desde PHP 8.4.0
   */
  public static function handle_error(mixed $errno, mixed $errstr, mixed $errfile, mixed $errline)
  {
    // if error reporting is off or add @ at begin of expression
    // doesn't show
    if (0 == error_reporting())
      return;

    // if exclude any type of error, doesn't show
    if (!(error_reporting() & $errno))
      return;

    // escape
    $errstr = htmlspecialchars($errstr);

    switch ($errno) {
      case E_USER_NOTICE:
        Response::instance()->notice($errstr);
        break;
      case E_USER_WARNING:
      case E_USER_DEPRECATED:
        Response::instance()->warning($errstr);
        break;
      default:
        // limpia el BUFFER de salida
        while (ob_get_length() !== false) ob_end_clean();
        
        Log::error("{$errstr} in {$errfile}:{$errline}");

        exit(Response::instance()->unknow($errstr, "{$errstr} in {$errfile}:{$errline}"));
    }

    // Evita el gestor de errores interno de PHP
    return (true);
  }

  /**
   * Handle Exceptions
   * Esta función será llamada en lugar de un bloque "catch" si ningún otro bloque es invocado.
   * El efecto es idéntico a envolver el programa entero en un bloque "try-catch" con esta función
   * como "catch" global.
   */
  public static function handle_exceptions(Throwable $ex)
  {
    Log::error(get_class($ex) . ':' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine());
    // Log::error(get_class($ex) . ':' . $ex->getMessage());
    // limpia el BUFFER de salida
    while (ob_get_length() !== false) ob_end_clean();

    exit(Response::instance()->error(
      $ex->getMessage(),
      get_class($ex) . ':' . $ex->getFile() . ':' . $ex->getLine()
    ));
    // exit(Response::instance()->error(
    //   $ex->getMessage(),
    //   get_class($ex)
    // ));
  }

  /**
   * 
   */
  public static function handle_shutdown()
  {
    echo 'Script ejecutado con exito';
  }

  public static function register()
  {
    // Sujetador personalizado de errores (trigger_error)
    set_error_handler(['\Atusan\Errors\ErrorsController', 'handle_error']);

    // Excepciones
    set_exception_handler(['\Atusan\Errors\ErrorsController', 'handle_exceptions']);

    // Ejecución de cierre
    // register_shutdown_function(['\Atusan\Errors\ErrorsController', 'handle_shutdown']);
  }
}
