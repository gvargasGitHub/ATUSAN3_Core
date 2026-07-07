<?php

namespace Atusan\Controls;

use Atusan\Controls\Traits\TraitDataFormControl;

class DataMultiFormControl extends DataViewControlBase
{
  use TraitDataFormControl;

  // ----------------------------------
  //  ComponentInterface
  // ----------------------------------

  /**
   * make
   * @return string
   */
  public function make(): string
  {
    ob_start(); 
    match (strtolower($this->type)) {
      'autocomplete' => $this->AutoComplete(),
      'csrf' => $this->Csrf(),
      'data' => $this->Data(),
      'check' => $this->InputCheck(),
      'date' => $this->InputBasic(),
      'time' => $this->InputBasic(),
      'file' => $this->InputFile(),
      'hidden' => $this->InputHidden(),
      'text' => $this->InputBasic(),
      'textarea' => $this->InputTextArea(),
      'password' => $this->InputBasic(),
      'radio' => $this->inputRadio(),
      'switch' => $this->inputSwitch(),
      'select' => $this->inputSelect()
    };
    return ob_get_clean();
  }

  /**
   * write
   * @return void
   */
  public function write(): void
  {
    echo $this->make();
  }
  
  public function getId(): string
  {
    return "{$this->parent->name}-{$this->name}";
  }
}
