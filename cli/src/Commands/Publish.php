<?php

namespace AtusanCLI\Commands;

class Publish extends MakeBase
{
  function required(): array
  {
    return ['--name'];
  }

  function defaults(): array
  {
    return [];
  }

  function resolver()
  {
    // Renombra en app/
    $appDir = APP_ROOT . DS . 'app' . DS;
    @rename($appDir . 'newapp', $appDir . $this->args['--name']);
    
    // Renombra en public/
    $pubDir = APP_ROOT . DS . 'public' . DS;
    @rename($pubDir . 'newapp', $pubDir . $this->args['--name']);
    
    //Edita archivo .env.example
    $envFile = APP_ROOT . DS . '.env.example';
    if(($fileContent = @file_get_contents($envFile)) !== false) {
      $fileContent = str_replace('newapp', $this->args['--name'], $fileContent);
      file_put_contents($envFile, $fileContent);
    }

    // Renombra .env
    @rename($envFile,  APP_ROOT . DS . '.env');

    // Crea carpeta logs/
    $logsFolder = APP_ROOT . DS . 'logs';
    if (!@file_exists($logsFolder)) @mkdir($logsFolder, '0777');

    echo "Proyecto {$this->args['--name']} publicado." . EOL;
  }
}
