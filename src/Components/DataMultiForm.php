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

  /**
   * 
   */
  protected function setType()
  {
    $this->type = 'DataMultiForm';
  }

  /**
   * Init DataView Properties
   */
  protected function initProperties()
  {
    // El desarrollador deberá generar, asignar y validar
    // manualmente el token a este control mediante el
    // método "setCsrf(string)"
    $this->addDataAndCsrf();
  }
  /**
   * Build
   */
  public function build(): string
  {
    ob_start();

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/body.view.php';

    $html = ob_get_contents();

    ob_clean();

    return $html;
  }

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
      $child->addAttribute('name', "{$this->name}_{$btn}");
      $child->addAttribute('text', $this->$btn);
      $child->setAttribute('html:ats-form', $this->name, 'html');
      $child->setAttribute('html:ats-command', $btn, 'html');
      $child->setAttribute('html:onclick', "DataMultiForm.handlerButtonEvent(event)", 'html');
    }
  }

  /**
   * 
   */
  protected function writeSection(string $sectionName)
  {
    foreach ($this->sources->getAllByName($sectionName) as $form) {
      echo '<div class="' . strtolower($sectionName) . '">';
      // Escribe la vista
      if (!empty($form->view)) {
        if (file_exists($this->owner->directory . DS . "{$form->view}.php"))
          include $this->owner->directory . DS . "{$form->view}.php";
        else {
          $located = FileSystem::locateFile(APP_DIRECTORY, basename($form->view), 'php');
          if (!$located) trigger_error("La vista {$this->view} no existe", E_USER_ERROR);

          include $located[0];
        }
      } else {
        if (count($this->data) == 0) $this->data[0] = [];

        foreach ($form->xml->children() as $child) {
          $component = $this->components->getByName($child->getAttribute('name'));

          if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setData($this->data[0]);

          $component->write();
        }
      }
      echo "\n</div>\n";
    }
  }

  // ----------------------------------
  //  DataViewBase
  // ----------------------------------
  /**
   * Import
   */
  public function import(array $data): int
  {
    $this->data[0] = array_merge($this->data[0], $data);

    return count($this->data);
  }
  
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->setType();

    $this->initProperties();

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

  /**
   * 
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = '';
    if (!property_exists($this, 'footer')) $this->footer = '';
    if (!property_exists($this, 'route')) $this->route = $_SERVER['REQUEST_URI'];

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
  }
}
