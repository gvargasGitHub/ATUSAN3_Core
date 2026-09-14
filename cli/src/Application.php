<?php

namespace AtusanCLI;

use AtusanCLI\Commands\MakeApp;
use AtusanCLI\Commands\MakeModel;
use AtusanCLI\Commands\MakeModule;
use AtusanCLI\Commands\MakeNested;
use AtusanCLI\Commands\MakeService;
use AtusanCLI\Commands\Publish;

use AtusanCLI\Commands\Info;
use AtusanCLI\Commands\Init;
use AtusanCLI\Commands\Validate;

class Application
{
  protected $commands = [];

  public function __construct()
  {
    $this->register('info', Info::class);
    $this->register('init', Init::class);
    $this->register('validate', Validate::class);

    $this->register('make:app', MakeApp::class);
    $this->register('make:module', MakeModule::class);
    $this->register('make:nested', MakeNested::class);
    $this->register('make:model', MakeModel::class);
    $this->register('make:service', MakeService::class);
    $this->register('publish', Publish::class);
  }

  protected function register(string $name, string $class)
  {
    $this->commands[$name] = $class;
  }

  public function run(array $argv)
  {
    $command = $argv[1] ?? null;

    if (!$command) {
      $this->showHelp();
      return;
    }

    try {
      if (!isset($this->commands[$command])) {
        echo "Comando {$command} no encontrado\n";
        return;
      }
      // analiza la lista de argumentos y retorna
      // colección de pares parametro=valor
      $pairs = $this->parse(array_slice($argv, 2));

      $class = $this->commands[$command];
      $instance = new $class($pairs);

      $instance->handle();
    } catch (\Exception $ex) {
      echo 'Error: ' . $ex->getMessage() . EOL . EOL;
    }
  }

  public static function parse(array $argv): array
  {
    $nof = count($argv);
    $pairs = [];
    // Salta cada 2 posiciones porque evalua argumento valor
    for ($p = 0; $p < $nof; $p += 2) {
      // valida sintaxis --argumento
      if (!preg_match('/^--[a-z]+$/', $argv[$p])) {
        throw new \Exception("El argumento {$argv[$p]} no es válido.");
      }
      $v = $p + 1;
      // valida que exista valor
      if ($v >= $nof) {
        throw new \Exception("{$argv[$p]} requiere de dato.");
      }
      // valida sintaxis valor o "valor con espacios"
      if (!preg_match('/(?:[^\s"]+|"[^"]*")/', $argv[$v])) {
        throw new \Exception("{$argv[$v]} no es válido");
      }

      $pairs[$argv[$p]] = $argv[$v];
    }

    return $pairs;
  }

  protected function showHelp()
{
    echo "ATUSAN CLI" . EOL . EOL;
    echo "Comandos disponibles:" . EOL;

    echo "init       Inicializa atusan.json" . EOL;
    echo "info       Muestra información del proyecto" . EOL;
    echo "validate   Valida el manifiesto del proyecto" . EOL;

    echo EOL;

    foreach (
        [
            'publish',
            'app',
            'module',
            'nested',
            'model',
            'service'
        ]
        as $w
    ) {
        $this->how(['--what' => $w]);
    }
}

  /**
   * How Command
   */
  protected function how(array $pairs)
  {
    switch ($pairs['--what']) {
      case 'app':
        echo "New Application.\n";
        echo "make:app --name ? --title ? --start ?\n";
        // echo str_repeat('-', 40) . EOL;
        break;
      case 'module':
        echo "New Module.\n";
        echo "make:module --app ? --name ? --type [basic*|nested] --start --parent ? --template ? --title ?\n";
        // echo str_repeat('-', 40) . EOL;
        break;
      case 'nested':
        echo "New Nested Module.\n";
        echo "make:nested --app ? --name ? --parent ? --template ? --title ?\n";
        // echo str_repeat('-', 40) . EOL;
        break;
      case 'model':
        echo "New Model.\n";
        echo "make:model --app ? --name ?\n";
        // echo str_repeat('-', 40) . EOL;
        break;
      case 'service':
        echo "New Service.\n";
        echo "make:service --app ? --name ?\n";
        // echo str_repeat('-', 40) . EOL;
        break;
      case 'publish':
        echo "Publish asset.\n";
        echo "publish\n";
        break;
      default:
        echo "comando {$pairs['--what']} desconocido." . EOL;
    }
    echo str_repeat('-', 40) . EOL;
  }
}
