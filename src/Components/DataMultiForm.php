<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitDataForm;
use Atusan\FileSystem\FileSystem;

class DataMultiForm extends DataViewBase
{
  use TraitDataForm;

  // Atributos obtenidos desde XML.
  // Nota: Estos atributos pueden ser personalizados
  // desde el elemento raiz:
  // <DataMultiForm backward="" forward="" finish=""></DataMultiForm>
  protected string $backward = 'Anterior';
  protected string $forward = 'Siguiente';
  protected string $finish = 'Enviar';

  // Propiedades
  protected int $numOfForms = 0;

  // /**
  //  * 
  //  */
  // protected function setType()
  // {
  //   $this->type = 'DataMultiForm';
  // }

  // /**
  //  * Init DataView Properties
  //  */
  // protected function initProperties()
  // {
  //   // El desarrollador deberá generar, asignar y validar
  //   // manualmente el token a este control mediante el
  //   // método "setCsrf(string)"
  //   $this->addDataAndCsrf();
  // }
  // /**
  //  * Build
  //  */
  // public function build(): string
  // {
  //   ob_start();

  //   include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/body.view.php';

  //   $html = ob_get_contents();

  //   ob_clean();

  //   return $html;
  // }

  /**
   * 
   */
  public function writeTopForms()
  {
    $this->writeSection('TopForm');
  }

  /**
   * 
   */
  public function writeStepForms()
  {
    $this->writeSection('StepForm');
  }

  /**
   * 
   */
  public function writeBottomForms()
  {
    $this->writeSection('BottomForm');
  }

  /**
   * 
   */
  protected function buildButtons()
  {
    // Integra el grupo de botones
    $buttons = $this->xml->addChild('Buttons');
    $bgroup = $buttons->addChild('ButtonGroupRow');
    $bgroup->addAttribute('name', "buttons");
    $bgroup->addAttribute('align', 'right');

    $buttons = ['finish', 'forward', 'backward'];

    foreach ($buttons as $btn) {
      $child = $bgroup->addChild('Button');
      $child->addAttribute('name', "{$this->get('id')}_{$btn}");
      $child->addAttribute('text', $this->$btn);
      $child->setAttribute('html:ats-form', $this->get('id'), 'html');
      $child->setAttribute('html:ats-command', $btn, 'html');
      $child->setAttribute('html:onclick', "DataMultiForm.handlerButtonEvent(event)", 'html');
    }
  }

  /**
   * Write Section
   */
  protected function writeSection(string $sectionName)
  {
    foreach ($this->sources->getAllByName($sectionName) as $form) {
      echo '<div class="' . strtolower($sectionName) . '">';
      
      // Escribe la vista
      if ($form->xml->hasAttribute('view')) {
        $view = $form->xml->getAttribute('view') . '.php';
        if (file_exists($this->getOwner()->getDirectory() . DS . $view))
          include $this->getOwner()->getDirectory() . DS . $view;
        elseif (file_exists(APP_DIRECTORY . DS . 'Views' . DS . $view))
          include APP_DIRECTORY . DS . 'Views' . DS . $view;
        else
          trigger_error("La vista {$view} no existe");
        
      } else {
        if (count($this->data) == 0) $this->data[0] = [];

        foreach ($form->xml->children() as $child) {
          $component = $this->components->getById($child->getAttribute('id'));

          if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setData($this->data[0]);

          echo $component->make();
        }
      }
      echo "\n</div>\n";
    }
  }

  // ----------------------------------
  //  DataViewBase
  // ----------------------------------
  
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    // $this->setType();

    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
    $this->addDataAndCsrf();

    $this->buildButtons();

    // Recorre las secciones Thead, Tbody, Tsummary & Tfoot
    foreach ($this->xml->children() as $section) {
      $sectioName = (string) $section->getName();

      $this->addSource($sectioName, $section);
    }
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    foreach ($this->components as $component) if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setParent($this);
  }

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
  //   if (!$this->validateProperty('footer')) $this->set('footer', '');

  //   ob_start();
  //   include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
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
