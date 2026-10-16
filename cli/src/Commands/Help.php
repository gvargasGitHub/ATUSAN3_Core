<?php
namespace AtusanCLI\Commands;

class Help extends MakeBase
{
  function required(): array
  {
    return [];
  }

  function defaults(): array
  {
    return [

    ];
  }

  function resolver()
  {
    $content = str_repeat('-', 40) . EOL;
    $content = 'Nomenclatura para uso de ATUSAN CLI:' . EOL;
    $content .= '>.\\atusan-cli [command] [args]' . EOL;

    if($this->checkArgument('help')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "Help.\n";
      $content .= "help\n";
      $content .= "help --? publish|app|module|nested|model|service|agents\n";
    }

    if($this->checkArgument('app')) {
      $content = str_repeat('-', 40) . EOL;
      $content .= "New Application.\n";
      $content .= "make:app --name ? --title ? --start ?\n";
    }
    if($this->checkArgument('module')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "New Module.\n";
      $content .= "make:module --app <appName> --name <modulePath> --type [basic*|nested] --start ? --parent ? --template ? --title ?\n";
    }
    if($this->checkArgument('nested')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "New Nested Module.\n";
      $content .= "make:nested --app <appName> --name <modulePath> --parent ? --template ? --title ?\n";
    }
    if($this->checkArgument('model')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "New Model.\n";
      $content .= "make:model --app <appName> --name <modelPath>\n";
    }
    if($this->checkArgument('service')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "New Service.\n";
      $content .= "make:service --app <appName> --name <servicePath>\n";
    }
    if($this->checkArgument('publish')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "Publish asset.\n";
      $content .= "publish --name <appName>\n";
    }
    if($this->checkArgument('agents')) {
      $content .= str_repeat('-', 40) . EOL;
      $content .= "Agents.\n";
      $content .= "Para actualizar el paquete:\n";
      $content .= "> composer update gvargas/atusan-agents\n\n";
      $content .= "Para sincronizar los archivos locales:\n";
      $content .= "agents:sync\n";
      $content .= "agents:sync --force-stack yes|no\n";
      $content .= "\t--force-stack = yes: Sobreescribe STACK.md\n";
      $content .= "\t--force-stack = no:  No sobreescribe STACK.md\n";
    }
    
    $content .= str_repeat('-', 40) . EOL;

    echo $content;
  }

  private function checkArgument(string $argValue): bool
  {
    return (count($this->args) == 0 || $this->args['--?'] == $argValue);
  }
}