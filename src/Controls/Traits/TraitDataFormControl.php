<?php

namespace Atusan\Controls\Traits;

trait TraitDataFormControl
{
  protected function Csrf()
  {
?>
    <input type="hidden" id="<?= $this->getId() ?>" name="csrf_token" value="<?= $this->getValue() ?>" />
  <?php
  }

  protected function inputHidden()
  {
  ?>
    <input type="hidden" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>" />
    <?php
  }

  protected function Data()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    ?>
    <input type="text" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>"
      class="form-control" readonly="readonly" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" />
    <?php
  }

  protected function AutoComplete()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    ?>
    <div class="autocomplete">
      <input type="text" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>"
        class="form-control inputEv completeEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
        style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" autocomplete="off" />
      <div class="autocomplete-list"></div>
    </div>
    <?php
  }

  protected function inputCheck()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label><br />
    <?php
    }
    $selected = ($this->getValue() == 1) ? 'checked' : '';
    ?>
    <input type="checkbox" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>"
      class="changeEv" <?= $selected ?> <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" /><br />
    <?php
  }

  protected function inputTextArea()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    ?>
    <textarea type="text" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>"
      class="form-control inputEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>"><?= $this->getValue() ?></textarea>
    <?php
  }

  protected function inputBasic()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    ?>
    <input type="<?= strtolower($this->get('type')) ?>" id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>"
      class="form-control inputEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>" />
    <?php
  }

  protected function inputFile()
  {
    $text = ($this->xml->hasAttribute('text')) ? $this->xml->getAttribute('text') : 'Elige un archivo';

    $icon = ($this->xml->hasAttribute('icon')) ? $this->xml->getAttribute('icon') : 'fa fa-upload';

    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    $name = ($this->xml->hasAttribute('multiple', 'html', 1)) ? $this->get('id') . '[]' : $this->get('id');
    ?>
    <input type="file" class="file changeEv" id="<?= $this->getId() ?>" name="<?= $name ?>" value="<?= $this->getValue() ?>" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?> />
    <label id="<?= $this->getId() ?>-label" class="form-control" for="<?= $this->getId() ?>"><i class="<?= $icon ?>"></i><?= $text ?></label>
    <label class="file-foot">Tamaño máximo permitido: <?= ini_get('upload_max_filesize') ?></label><br />
    <?php
  }

  protected function inputRadio()
  {
    $display = ($this->xml->hasAttribute('display')) ? $this->xml->getAttribute('display') : 'block';
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->get('id') ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label><br />
    <?php
    }
    $index = 0;
    foreach ($this->xml->children() as $opt) {
      $index += 1;
      $selected = ($this->getValue() == $opt->getAttribute('value')) ? 'checked' : '';
    ?>
      <div class="radiobox <?= $display ?>">
        <input type="radio" class="radio changeEv" id="<?= $this->getId() ?>-<?= $index ?>" name="<?= $this->get('id') ?>" value="<?= $opt->getAttribute('value') ?>" <?= $selected ?> />
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
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label>
    <?php
    }
    ?>
    <select id="<?= $this->getId() ?>" type="select" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>"
      class="form-control changeEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <?php
      foreach ($this->xml->children() as $opt) {
        $optText =  $opt->getAttribute('text');

        if(!$opt->hasAttribute('value')) $opt->setAttribute('value', $optText);

        $optValue = $opt->getAttribute('value');

        $selected = ($this->getValue() == $optValue) ? 'selected' : '';
      ?>
        <option value="<?= $optValue ?>" <?= $selected ?>><?= $optText ?></option>
      <?php
      }
      ?>
    </select>
    <?php
  }

  protected function inputSwitch()
  {
    if ($this->xml->hasAttribute('label')) {
    ?>
      <label for="<?= $this->getId() ?>" class="form-label"><?= $this->xml->getAttribute('label') ?></label><br />
    <?php
    }
    $selected = ($this->getValue() == 1) ? 'checked' : '';
    ?>
    <label class="switch">
      <input type="checkbox" class="changeEv" <?= $selected ?> id="<?= $this->getId() ?>" name="<?= $this->get('id') ?>" value="<?= $this->getValue() ?>" />
      <span class="slider"></span>
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
    if (($resolve = $this->xml->getAttribute('resolve')) == null) trigger_error($this->get('id') . ' requiere el atributo "resolve."', E_USER_ERROR);
    
    if (!method_exists($this->parent->getOwner(), $resolve)) trigger_error("$resolve no existe en {$this->parent->getOwner()->get('id')}", E_USER_ERROR);
    
    $value = $this->parent->getOwner()->$resolve($this->getData());

    foreach($this->xml->children() as $case) {
      if ($case->getAttribute('match') == $value) {
        $case->setAttribute('name', $this->get('id'));

        // $caseControl = DataFormControl::fromXML($this->parent->getOwner(), $case);
        // $caseControl->setParent($this->parent);
        // $caseControl->setData($this->getData());
        
        // return $caseControl->write();
        echo "<strong>{$this->get('id')}</strong>";
      }
    }
  }
}
