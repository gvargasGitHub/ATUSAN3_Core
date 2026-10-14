<?php

namespace Atusan\Components;

use Atusan\Components\Traits\TraitDataForm;

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

  /**
   * @var int $numOfForms
   */
  protected int $numOfForms = 0;

  /**
   * @var ButtonGroupRow $buttonBroup
   */
  protected ButtonGroupRow $buttonGroup;

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
  protected function attachButtonGroup()
  {
    // Integra el grupo de botones
    $buttons = $this->xml->addChild('Buttons');

    $bgroup = $buttons->addChild('ButtonGroupRow');
    $bgroup->addAttribute('id', "buttons");
    $bgroup->addAttribute('align', 'right');

    $options = ['finish', 'forward', 'backward'];

    foreach ($options as $btn) {
      $child = $bgroup->addChild('Button');
      $child->addAttribute('id', "{$this->get('id')}_{$btn}");
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
    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
    $this->addDataAndCsrf();

    $this->attachButtonGroup();

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
    foreach ($this->components as $component) 
      if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) 
        $component->setParent($this);

    $this->buttonGroup = $this->findComponentById('buttons');
  }
}
