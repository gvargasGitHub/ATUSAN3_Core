<?php

namespace Atusan\Components;

// use Atusan\Components\Traits\TraitNavbar;

class Subnavbar extends NavBase
{
  // use TraitNavbar;
  // /* ------------------------
  // Properties
  // ------------------------ */
  // public string $type = 'Subnavbar';
  
  // // ----------------------------------
  // //  TraitComponent
  // // ----------------------------------
  // /**
  //  * 
  //  */
  // protected function setSources(): void
  // {
  //   // Integra las secciones Left & Right
  //   foreach ($this->xml->children() as $section) $this->addSource((string) $section->getName(), $section);
  // }

  // /**
  //  * 
  //  */
  // protected function finalDefinitions(): void
  // {
  //   foreach ($this->components as $component) $component->setParent($this);
  // }

  // // ----------------------------------
  // //  ComponentInterface
  // // ----------------------------------
  // /**
  //  * make
  //  * @return string
  //  */
  // public function make(): string
  // {
  //   if (!$this->validateProperty('title')) $this->set('title', '');

  //   ob_start();
  //   include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . DS . 'view.php';
  //   return ob_get_clean();
  // }

  // /**
  //  * write
  //  * @return void
  //  */
  // public function write(): void
  // {
  //   echo $this->make();
  // }
}
