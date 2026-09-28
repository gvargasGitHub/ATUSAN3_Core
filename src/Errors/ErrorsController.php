<?php

namespace Atusan\Errors;

use Atusan\Exceptions\HandlerErrorException;
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
  public static function handle_error(
    mixed $errno, 
    mixed $errstr, 
    mixed $errfile, 
    mixed $errline) : bool
  {
    // if error reporting is off or add @ at begin of expression doesn't show
    if (0 == error_reporting()) return false;

    // if exclude any type of error, doesn't show
    if (!(error_reporting() & $errno)) return false;
    
    // v3.0.20: Components disparan errores controlados
    // 406 Not Acceptable
    // Esta respuesta se envía cuando el servidor web, tras realizar una negociación de contenido dirigida 
    // por el servidor, no encuentra ningún contenido que cumpla con los criterios especificados por el 
    // agente de usuario.
    // 409 Conflict
    // Esta respuesta se envía cuando una solicitud entra en conflicto con el estado actual del servidor. 
    // En la edición web remota mediante WebDAV, las respuestas 409 son errores enviados al cliente para 
    // que el usuario pueda resolver el conflicto y volver a enviar la solicitud.
    $http_code = ($errno === E_NOTICE || $errno === E_USER_NOTICE) ? 409 : 406;
    
    throw new HandlerErrorException(
        $errstr,
        $http_code,
        $errfile,
        $errline
    );
  }

  /**
   * Handle Exceptions
   * Esta función será llamada en lugar de un bloque "catch" si ningún otro bloque es invocado.
   * El efecto es idéntico a envolver el programa entero en un bloque "try-catch" con esta función
   * como "catch" global.
   */
  public static function handle_exceptions(Throwable $exception)
  {
    // v3.0.20
    Log::incident($exception);
    // limpia el buffer de salida que se inició en TraitRender::renderView
    while (ob_get_length() !== false) ob_end_clean();
    
    // Si APP_DEBUG esta activo, muestra el detalle (File/line)
    $detail = APP_DEBUG 
      ? "{$exception->getFile()}:{$exception->getLine()}\n{$exception->getTraceAsString()}" 
      : null;
    // Establece el código http que deberá responder
    $http_code = $exception->getCode() == 0 ? 500 : $exception->getCode();

    // throwExitError finaliza el programa con exit(1)
    Response::instance()->throwExitError(
      CONTENT_TYPE_REQUESTED,
      $http_code, 
      $exception->getMessage(),
      self::get_exception_class_name($exception::class),
      $detail);
  }

  /**
   * 
   */
  public static function handle_shutdown()
  {
    
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

  protected static function get_exception_class_name(string $classname) : string
  {
    if($pos = strrpos($classname, '\\')) return substr($classname, $pos +1);

    return $classname;
  }
}
