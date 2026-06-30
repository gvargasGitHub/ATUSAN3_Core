<?php

namespace Atusan\Bootstrap;

use Atusan\Autoloader\Autoloader;
use Atusan\Errors\ErrorsController as Errors;
use Atusan\FileSystem\FileSystem;
use Atusan\Kernel\Kernel;

class Bootstrap
{
  /**
   * App
   */
  static public function app()
  {
    // Definición del tipo de petición.
    $s = 'X-Requested-With';
    $v = 'XMLHttpRequest';
    $h = apache_request_headers();

    define(
      'CONTENT_TYPE_REQUESTED',
      (array_key_exists($s, $h) && $h[$s] = $v) ? 'XHR' : 'HTML'
    );

    // Constantes de directorios
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
    | Manejo de errores
    |--------------------------------------------------------------------------
    */
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

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
    // 
    Kernel::execute(Kernel::handle());
  }
}
