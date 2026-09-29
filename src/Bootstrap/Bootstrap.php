<?php

namespace Atusan\Bootstrap;

use Atusan\Autoloader\Autoloader;
use Atusan\Errors\ErrorsController as Errors;
use Atusan\FileSystem\FileSystem;
use Atusan\Http\Request\Request;
use Atusan\Kernel\Kernel;
use Atusan\Log\Log;

class Bootstrap
{
  /**
   * App
   */
  static public function app()
  { 
    /*
    |--------------------------------------------------------------------------
    | Constantes de directorios
    |--------------------------------------------------------------------------
    */
    define('DS', DIRECTORY_SEPARATOR);
    define('APP_ROOT', FileSystem::findRoot(__DIR__));
    define('APP_CORE', dirname(__DIR__, 2));

    /*
    |--------------------------------------------------------------------------
    | Cargar variables de entorno (.env)
    |--------------------------------------------------------------------------
    */
    FileSystem::dotEnv(APP_ROOT);

    define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
    define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL));

    // define ubicación de Aplicación
    define('APP_NAME', $_ENV['APP_NAME']);
    define('APP_DIRECTORY', APP_ROOT . DS . $_ENV['APPS_DIRECTORY'] . DS . APP_NAME);
    /*
    |--------------------------------------------------------------------------
    | Tipo de Contenido solicitado
    | Determina el tipo de respuesta esperado.
    |--------------------------------------------------------------------------
    */
    $accept          = $_SERVER['HTTP_ACCEPT'] ?? '';
    $contentType     = $_SERVER['CONTENT_TYPE'] ?? '';
    $requestedWith   = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    $isJson =
        stripos($accept, 'application/json') !== false
        || stripos($contentType, 'application/json') !== false
        || strcasecmp($requestedWith, 'XMLHttpRequest') === 0;

    define(
        'CONTENT_TYPE_REQUESTED', $isJson ? 'JSON' : 'HTML'
    );
    // v3.0.20
    if (APP_DEBUG) {
      Log::info("Uri:{$_SERVER['QUERY_STRING']};Method:{$_SERVER['REQUEST_METHOD']};ContentTypeRequested:" 
        . CONTENT_TYPE_REQUESTED 
        . "; Accept:{$accept}; ContentType:{$contentType};RequestedWith:{$requestedWith};");
    }
    /*
    |--------------------------------------------------------------------------
    | Manejo de errores
    |--------------------------------------------------------------------------
    */
    error_reporting(E_ALL);
    ini_set('display_errors', APP_DEBUG ? '1' : '0');

    // Registro de manejo de errores
    Errors::register();
    /*
    |--------------------------------------------------------------------------
    | Integración de App
    |--------------------------------------------------------------------------
    */
    // Registro de autocarga de las clases de la App
    $loader = Autoloader::init(APP_DIRECTORY);
    $loader->addNamespace('App', APP_DIRECTORY);
    
    // Zona horaria
    date_default_timezone_set(\App\Config\Config::$time_zone);

    // Variables de entorno de la App
    FileSystem::dotEnv(APP_DIRECTORY);
    // v3.0.21 
    Kernel::execute(Request::capture());
  }
}
