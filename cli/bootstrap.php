<?php

declare(strict_types=1);

define('DS', DIRECTORY_SEPARATOR);
define('EOL', "\n");
define('ATUSANCLI_ROOT', realpath(__DIR__));
define('ATUSANCLI_SRC', ATUSANCLI_ROOT . '/src');
define('APP_ROOT', dirname(__DIR__, 1));

/*
|--------------------------------------------------------------------------
| Zona horaria
|--------------------------------------------------------------------------
*/
date_default_timezone_set('America/Mexico_City');

/*
|--------------------------------------------------------------------------
| Autoloader básico (si no se usa Composer)
|--------------------------------------------------------------------------
*/

spl_autoload_register(function ($class) {

  $prefix = 'AtusanCLI\\';
  $baseDir = ATUSANCLI_SRC . '/';

  if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
    return;
  }

  $relativeClass = substr($class, strlen($prefix));

  $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

  if (file_exists($file)) {
    require $file;
  }
});
