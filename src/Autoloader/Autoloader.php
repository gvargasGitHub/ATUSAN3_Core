<?php

namespace Atusan\Autoloader;

use Atusan\Log\Log;

/**

 */
class Autoloader
{
  /**
   * 
   */
  protected static ?self $loader = null;

  /**
   * @var array $alias
   * Sobre nombres autorizados para los archivos que contengan las clases.
   */
  protected array $alias = [
    'Controller',
    'Service',
    'Model'
  ];

  /**
   * @var array $prefixes
   */
  protected array $prefixes = [];

  /**
   * Init
   * Invocado por core/Bootstrap::app
   */
  public static function init(string $root): self
  {
    if (self::$loader == NULL)
      self::$loader = new self($root);

    return self::$loader;
  }

  /**
   * Autoloader
   */
  function __construct(private string $root)
  {
    # param throw : true activa disparo de excepciones
    # param prepend: false coloca el "autoload" al final de la cola
    spl_autoload_register([$this, 'loadClass'], true, false);
  }

   /**
   * Registra un namespace.
   */
  public function addNamespace(string $prefix, string $baseDir): void
  {
      $prefix = trim($prefix, '\\') . '\\';
      $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

      $this->prefixes[$prefix] = $baseDir;
  }

  /**
   * Carga una clase.
   */
  protected function loadClass(string $class): void
  {
    foreach ($this->prefixes as $prefix => $baseDir) {
      // evalua si el prefijo existe dentro del nombre de la clase;
      // si no existe, entonces pasa al siguiente prefijo
      if (strpos($class, $prefix) !== 0) continue;
      
      // quita el prefijo del nombre de la clase quedando la
      // ruta relativa a la clase a partir del directorio registrado
      // con el prefijo
      $relative = str_replace('\\', DS, substr($class, strlen($prefix)));

      // arma el nombre del archivo PHP con el nombre de la clase
      // reemplazando el separador de "namespace" por el separador
      // de directorios del sistema:
      $file = $baseDir . $relative . '.php';

      if (is_file($file)) {
        require $file;
        return;
      }
      // En caso de no existir, agrega el nombre de la clase a la ruta
      $file = $baseDir . $relative . DS . basename($relative) . '.php';

      if (is_file($file)) {
        require $file;
        return;
      }
      
      foreach($this->alias as $alias) {
        $file = $baseDir . $relative. DS . $alias . '.php';
        if (is_file($file)) {
          require $file;
          return;
        }
      }
      return;
    }
}
}
