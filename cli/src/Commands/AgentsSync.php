<?php
namespace AtusanCLI\Commands;

class AgentsSync extends MakeBase
{
  function required(): array
  {
    return [];
  }

  function defaults(): array
  {
    return [
      '--force-stack' => 'no'
    ];
  }

  protected function checkIfAgentsExists() : bool
  {
    return @is_dir(implode(DS, [APP_ROOT, 'temp', 'gvargas', 'atusan-agents']));
  }

  function resolver()
  {
    // Valida si existe la carpeta del proyecto AGENTS.
    if (!$this->checkIfAgentsExists())
      throw new \Exception('Proyecto Atusan-Agents no existe.');

    $agentsPath = implode(DS, [APP_ROOT, 'vendor', 'gvargas', 'atusan-agents']);

    // Copia o reemplaza los archivos en raiz
    self::copy("$agentsPath/AGENTS.md", APP_ROOT . '/AGENTS.md');
    self::copyR("$agentsPath/doc", APP_ROOT . '/doc');
    
    // En caso de que no exista STACK.md lo copia, si no, entonces lo respeta.
    if(!file_exists("$agentsPath/STACK.md"))
      self::copy("$agentsPath/STACK.md", APP_ROOT . '/STACK.md');

    echo "Atusan-Agents sincronizado.";
  }
}