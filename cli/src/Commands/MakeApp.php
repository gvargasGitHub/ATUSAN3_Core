<?php

namespace AtusanCLI\Commands;

class MakeApp extends MakeBase
{
  function required(): array
  {
    return ['--name'];
  }

  function defaults(): array
  {
    return [
      '--title' => $this->args['--name'],
      '--start' => 'modAcceso'
    ];
  }

  function resolver()
  {
    // Valida si ya existe la carpeta de la Aplicación
    if ($this->checkIfAppExists($this->args['--name']))
      throw new \Exception("{$this->args['--name']} ya existe.");

    $appDir = APP_ROOT . DS . $this->appDir . DS . $this->args['--name'];

    // Copia plantilla de la Aplicación
    self::copyR(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'newapp',
      APP_ROOT . DS . $this->appDir . DS . $this->args['--name']
    );

    // Copia recursos CSS y JS
    self::copyR(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'statics' . DS . 'css',
      APP_ROOT . DS . 'public' . DS . $this->args['--name'] . DS . 'css'
    );
    self::copyR(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'statics' . DS . 'js',
      APP_ROOT . DS . 'public' . DS . $this->args['--name'] . DS . 'js'
    );
    // Edita archivo .env
    $fileContent = file_get_contents($appDir . DS . '.env');
    $fileContent = str_replace('app_title', $this->args['--title'], $fileContent);
    $fileContent = str_replace('app_start', $this->args['--start'], $fileContent);
    file_put_contents($appDir . DS . '.env', $fileContent);

    echo "La aplicación {$this->args['--name']} se creó exitosamente." . EOL;
  }
}
