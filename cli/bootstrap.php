<?php

declare(strict_types=1);

define('DS', DIRECTORY_SEPARATOR);
define('EOL', "\n");

$rootPath = findRoot(__DIR__);

if ($rootPath === false) die('No fue posible localizar la raíz del proyecto.');

define('ATUSANCLI_ROOT', realpath(__DIR__));
define('ATUSANCLI_SRC', ATUSANCLI_ROOT . '/src');
define('APP_ROOT', $rootPath);

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

/**
 * Busca recursivamente hacia arriba hasta encontrar
 * la carpeta raíz del proyecto.
 *
 * Criterios de validación:
 * - existencia de /app
 * - existencia de /public
 *
 * Retorna:
 * - string Ruta raíz encontrada
 * - false  Si no encuentra la raíz
 */
function findRoot(string $startPath)
{
    $current = realpath($startPath);

    if ($current === false) {
        return false;
    }

    // Si recibe archivo, tomar su directorio
    if (is_file($current)) {
        $current = dirname($current);
    }

    while (true) {

        $appDir    = $current . DIRECTORY_SEPARATOR . 'app';
        $publicDir = $current . DIRECTORY_SEPARATOR . 'public';

        $isRoot =
            is_dir($appDir) &&
            is_dir($publicDir);

        if ($isRoot) {
            return $current;
        }

        // Subir un nivel
        $parent = dirname($current);

        // Llegó al nivel máximo
        if ($parent === $current) {
            return false;
        }

        $current = $parent;
    }
}