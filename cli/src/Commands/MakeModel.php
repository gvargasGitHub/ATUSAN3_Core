<?php

namespace AtusanCLI\Commands;

class MakeModel extends MakeBase
{
  function required(): array
  {
    return ['--app', '--name'];
  }

  function defaults(): array
  {
    return [
      '--root' => 'Models'
    ];
  }

  function resolver()
  {
    // Valida si existe la carpeta de la Aplicación
    if (!$this->checkIfAppExists($this->args['--app']))
      throw new \Exception("{$this->args['--app']} no existe.");

    // Resolviendo ruta
    $this->args['--path'] = dirname($this->args['--name']);
    $this->args['--name'] = basename($this->args['--name']);

    $modelPath = APP_ROOT . DS . $this->appDir
      . DS . $this->args['--app'] . DS . $this->args['--root']
      . DS . $this->args['--path'];

    // Valida si ya existe la carpeta del Módulo
    if (@is_dir($modelPath . DS . $this->args['--name']))
      throw new \Exception("{$this->args['--name']} ya existe.");

    // Copia plantilla del Módulo
    self::copy(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'newmodel' . DS . 'Model.php',
      $modelPath . DS . 'Model.php'
    );

    // Edita contenidos
    $content = file_get_contents($modelPath . DS . 'Model.php');
    $content = str_replace('model_name', $this->args['--name'], $content);

    file_put_contents($modelPath . DS . 'Model.php', $content);
    rename($modelPath . DS . 'Model.php', $modelPath . DS . "{$this->args['--name']}.php");

    echo "El modelo {$this->args['--name']} se creó exitosamente." . EOL;
  }
}
