<?php

namespace Atusan\Controls\Traits;

// use Atusan\Components\Traits\TraitComponent;
// use Atusan\Components\Traits\TraitComponentNest;
// use Atusan\Controls\DataFormControl;

trait TraitDataFormControl
{
  protected function Csrf()
  {
?>
    <Input type="hidden" id="<?= $this->getId() ?>" name="csrf_token" value="<?= $this->getValue() ?>" />
  <?php
  }

  protected function InputHidden()
  {
  ?>
    <Input type="hidden" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>" />
    <?php
  }

  protected function Data()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    ?>
    <Input type="text" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
      class="form-control" readonly="readonly" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" />
    <?php
  }

  protected function AutoComplete()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    ?>
    <Div class="autocomplete">
      <Input type="text" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
        class="form-control inputEv completeEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
        style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" autocomplete="off" />
      <Div class="autocomplete-list"></Div>
    </Div>
    <?php
  }

  protected function InputCheck()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label><br />
    <?php
    }
    $selected = ($this->getValue() == 1) ? 'checked' : '';
    ?>
    <Input type="checkbox" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
      class="changeEv" <?= $selected ?> <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" /><br />
    <?php
  }

  protected function InputTextArea()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    ?>
    <TextArea type="text" id="<?= $this->getId() ?>" name="<?= $this->name ?>"
      class="form-control inputEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>"><?= $this->getValue() ?></TextArea>
    <?php
  }

  protected function InputBasic()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    ?>
    <Input type="<?= strtolower($this->type) ?>" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
      class="form-control inputEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" />
    <?php
  }

  protected function InputFile()
  {
    $text = ($this->xml->hasAttribute('text')) ? $this->xml->getAttribute('text') : 'Elige un archivo';

    $icon = ($this->xml->hasAttribute('icon')) ? $this->xml->getAttribute('icon') : 'fa fa-upload';

    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    $name = ($this->xml->hasAttribute('multiple', 'html', 1)) ? $this->name . '[]' : $this->name;
    ?>
    <Input type="file" class="file changeEv" id="<?= $this->getId() ?>" name="<?= $name ?>" value="<?= $this->getValue() ?>" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?> />
    <Label id="<?= $this->getId() ?>-label" class="form-control" for="<?= $this->getId() ?>"><i class="<?= $icon ?>"></i><?= $text ?></Label>
    <label class="file-foot">Tamaño máximo permitido: <?= ini_get('upload_max_filesize') ?></label><br />
    <?php
  }

  protected function inputRadio()
  {
    $display = ($this->xml->hasAttribute('display')) ? $this->xml->getAttribute('display') : 'block';
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->name ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label><br />
    <?php
    }
    $index = 0;
    foreach ($this->xml->children() as $opt) {
      $index += 1;
      $selected = ($this->getValue() == $opt->getAttribute('value')) ? 'checked' : '';
    ?>
      <div class="radiobox <?= $display ?>">
        <Input type="radio" class="radio changeEv" id="<?= $this->getId() ?>-<?= $index ?>" name="<?= $this->name ?>" value="<?= $opt->getAttribute('value') ?>" <?= $selected ?> />
        <label class="label" for="<?= $this->getId() ?>-<?= $index ?>"><?= $opt->getAttribute('text') ?></label>
      </div>
    <?php
    }
    ?>
    <br />
    <?php
  }

  protected function inputSelect()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label>
    <?php
    }
    ?>
    <Select id="<?= $this->getId() ?>" type="select" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
      class="form-control changeEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <?php
      foreach ($this->xml->children() as $opt) {
        $selected = ($this->getValue() == $opt->getAttribute('value')) ? 'selected' : '';
      ?>
        <option value="<?= $opt->getAttribute('value') ?>" <?= $selected ?>><?= $opt->getAttribute('text') ?></option>
      <?php
      }
      ?>
    </Select>
    <?php
  }

  protected function InputSwitch()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <Label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></Label><br />
    <?php
    }
    $selected = ($this->getValue() == 1) ? 'checked' : '';
    ?>
    <label class="switch">
      <Input type="checkbox" class="changeEv" <?= $selected ?> id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>" />
      <Span class="slider"></Span>
    </label><br />
<?php
  }

  /**
   * Case
   * @attribute string name   : El nombre del campo origen.
   * @attribute string resolve: El nombre del método responsable de retornar
   *  el tipo de control que será construido.
   * 
   * Los hijos de este elemento son declaraciones de controles. Cada declaración
   * debe incluir el atributo "match" el cual debe contener el valor que coincida
   * con el retorno del método definido en "resolve";
   */
  protected function Case()
  {
    if (($resolve = $this->xml->getAttribute('resolve')) == null) trigger_error($this->name . ' requiere el atributo "resolve."', E_USER_ERROR);
    
    if (!method_exists($this->parent->getOwner(), $resolve)) trigger_error("$resolve no existe en {$this->parent->getOwner()->name}", E_USER_ERROR);
    
    $value = $this->parent->getOwner()->$resolve($this->getData());

    foreach($this->xml->children() as $case) {
      if ($case->getAttribute('match') == $value) {
        $case->setAttribute('name', $this->name);

        // $caseControl = DataFormControl::fromXML($this->parent->getOwner(), $case);
        // $caseControl->setParent($this->parent);
        // $caseControl->setData($this->getData());
        
        // return $caseControl->write();
        echo "<strong>{$this->name}</strong>";
      }
    }
  }
}
