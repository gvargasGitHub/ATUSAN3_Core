<?php

namespace Atusan\FileSystem;

use Atusan\Log\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class FileSystem
{
  /**
   * Exists
   */
  public static function exists($filename): bool
  {
    return @file_exists($filename);
  }

  /**
   * Get Class Directory (ChatGPT)
   */
  public static function getClassDirectory($object)
  {
    $r = new ReflectionClass(get_class($object));

    return dirname($r->getFileName());
  }

  /**
   * Locate File
   */
  static public function locateFile(string $root, string $name): string | false
  {
    // Obtiene colección de directorios
    $directories = scandir($root);

    if ($directories === false) return false;

    // Recorre la colección de directorios hasta encontrar el archivo
    foreach ($directories as $directory) {
      if ($directory == '.' || $directory == '..') continue;
      // Obtiene el elemento (directorio | archivo)
      $filename = $root . DS . $directory;
      // Valida si es directorio o archivo
      if (is_dir($filename))
        $filename = static::locateFile($filename, $name);

      if (pathinfo($filename, PATHINFO_FILENAME) == $name) return $filename;
    }

    return false;
  }

  /**
   * localeFiles (Gemini)
   */
  public static function locateFiles(string $rootPath, string $searchTerm, string $extension = null): array | false
  {
    if (!is_dir($rootPath)) {
      return false;
    }

    $resultados = [];
    $directory = new RecursiveDirectoryIterator($rootPath);
    $iterator = new RecursiveIteratorIterator($directory);

    // Normalizamos la extensión (quitamos el punto si el usuario lo puso)
    if ($extension) {
      $extension = ltrim(strtolower($extension), '.');
    }

    foreach ($iterator as $file) {
      if ($file->isFile()) {
        $nombreArchivo = $file->getFilename();
        $extActual = strtolower($file->getExtension());

        // 1. Verificamos si el nombre coincide (parcial)
        $coincideNombre = stripos($nombreArchivo, $searchTerm) !== false;

        // 2. Verificamos si la extensión coincide (si se proporcionó una)
        $coincideExt = ($extension === null) || ($extActual === $extension);

        if ($coincideNombre && $coincideExt) {
          $resultados[] = $file->getRealPath();
        }
      }
    }

    return !empty($resultados) ? $resultados : false;
  }

  /**
   * Copy Recursive
   */
  public static function copyR(string $path, string $dest): bool
  {
    if (is_dir($path)) {
      mkdir($dest, '0777', true);
      $objects = scandir($path);
      if (sizeof($objects) > 0) {
        foreach ($objects as $file) {
          if ($file == "." || $file == "..")
            continue;
          if (is_dir($path . DS . $file))
            self::copyR($path . DS . $file, $dest . DS . $file);
          else
            self::copy($path . DS . $file, $dest . DS . $file);
        }
      }
      return true;
    } elseif (is_file($path))
      return self::copy($path, $dest);
    else
      return false;
  }

  /**
   * Copy File
   */
  public static function copy(string $path, string $dest): bool
  {
    if (!is_dir(dirname($dest)))
      mkdir(dirname($dest), '0777', true);

    if (is_file($path))
      return copy($path, $dest);
    else
      return false;
  }

  /**
   * DotEnv
   */
  public static function dotEnv(string $root): void
  {
    $envFile = $root . '/.env';

    if (file_exists($envFile)) {

      $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

      foreach ($lines as $line) {

        if (str_starts_with(trim($line), '#')) {
          continue;
        }

        [$name, $value] = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value);

        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
      }
    }
  }

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
  static function findRoot(string $startPath)
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
}
