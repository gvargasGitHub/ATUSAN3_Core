<?php

namespace AtusanCLI\Commands;

class MakeService extends MakeBase
{
  function required(): array
  {
    return ['--app', '--name'];
  }

  function defaults(): array
  {
    return [
      '--root' => 'Services'
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

    $servicePath = APP_ROOT . DS . $this->appDir
      . DS . $this->args['--app'] . DS . $this->args['--root']
      . DS . $this->args['--path'];

    // Valida si ya existe la carpeta del Módulo
    if (@is_dir($servicePath . DS . $this->args['--name']))
      throw new \Exception("{$this->args['--name']} ya existe.");

    // Copia plantilla del Módulo
    self::copy(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'newservice' . DS . 'Service.php',
      $servicePath . DS . 'Service.php'
    );

    // Edita contenidos
    $content = file_get_contents($servicePath . DS . 'Service.php');
    $content = str_replace('service_name', $this->args['--name'], $content);

    file_put_contents($servicePath . DS . 'Service.php', $content);
    rename($servicePath . DS . 'Service.php', $servicePath . DS . "{$this->args['--name']}.php");

    echo "El servicio {$this->args['--name']} se creó exitosamente." . EOL;
  }
}
