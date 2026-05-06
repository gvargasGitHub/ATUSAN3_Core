<?php

namespace AtusanCLI\Commands;

class MakeModule extends MakeBase
{
  function required(): array
  {
    return ['--app', '--name'];
  }

  function defaults(): array
  {
    return [
      '--title' => basename($this->args['--name']),
      '--root' => 'Modules',
      '--parent' => 'AppModuleParent',
      '--template' => 'frame'
    ];
  }

  function resolver()
  {
    // Valida si existe la carpeta de la Aplicación
    if (!$this->checkIfAppExists($this->args['--app']))
      throw new \Exception("{$this->args['--app']} no existe.");

    // Resolviendo ruta
    $this->args['--path'] = $this->args['--name'];
    $this->args['--name'] = basename($this->args['--name']);

    $modulePath = APP_ROOT . DS . $this->appDir
      . DS . $this->args['--app'] . DS . $this->args['--root']
      . DS . $this->args['--path'];

    // Valida si ya existe la carpeta del Módulo
    if (@is_dir($modulePath))
      throw new \Exception("{$this->args['--path']} ya existe.");

    // Copia plantilla del Módulo
    self::copyR(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'newmodule',
      $modulePath
    );

    // Edita contenidos
    $xml = file_get_contents($modulePath . DS . 'Components.xml');
    $xml = str_replace('module_title', $this->args['--title'], $xml);
    $xml = str_replace('module_template', $this->args['--template'], $xml);
    file_put_contents($modulePath . DS . 'Components.xml', $xml);

    $php = file_get_contents($modulePath . DS . 'Controller.php');
    $php = str_replace('module_name', $this->args['--name'], $php);
    $php = str_replace('module_parent', $this->args['--parent'], $php);
    file_put_contents($modulePath . DS . 'Controller.php', $php);

    $view = file_get_contents($modulePath . DS . 'View.php');
    $view = str_replace('module_name', $this->args['--name'], $view);
    file_put_contents($modulePath . DS . 'View.php', $view);

    echo "El módulo {$this->args['--name']} se creó exitosamente." . EOL;
  }
}
