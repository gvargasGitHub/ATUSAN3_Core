<?php

namespace Atusan\Controls;

use Atusan\Types\MenuOptionType;

class DataGridControl extends DataViewControlBase
{
  /**
   * Write
   */
  public function write(): void
  {
    match ($this->type) {
      'Hidden' => $this->InputHidden(),
      'Icon' => $this->IconStates(),
      'States' => $this->IconStates(),
      'Actions' => $this->Actions(),
      'Data' => $this->Data(),
      'Text' => $this->InputBasic(),
      'Date' => $this->InputBasic(),
      'Time' => $this->InputBasic(),
      'CheckBox' => $this->InputCheck(),
      'Check' => $this->InputCheck(),
      'Switch' => $this->InputSwitch(),
      'Select' => $this->InputSelect(),
      'Menu' => $this->MenuOptions(),
      'Case' => $this->Case()
    };
  }

  public function getId(): string
  {
    return "{$this->parent->name}-{$this->name}-{$this->row}";
  }

  protected function InputHidden()
  {
?>
    <input type="hidden" id="<?= $this->getId() ?>" name="<?= $this->name ?>" value="<?= $this->getValue() ?>" />
  <?php
  }

  /**
   * Icon, States
   */
  protected function IconStates()
  {
    // Obtiene referencia del "Controller".
    $owner = $this->parent->getOwner();

    // Evalua su el valor del control se obtiene de un método del "Controller".
    if ($this->xml->hasAttribute('resolve')) {
      $resolve = $this->xml->getAttribute('resolve');
      if (!method_exists($owner, $resolve)) trigger_error("$resolve no existe en {$owner->name}", E_USER_ERROR);
      $value = $owner->$resolve($this->getData());
    } else
      $value = $this->getValue();
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="state" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      value="<?= $value ?>" style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <?php
      foreach ($this->xml->children() as $state) {
        // Compara el valor del registro con el valor del "state",
        // si no coincide, pasa al siguiente estado
        if ($value != $state->getAttribute('value')) continue;
      ?>
        <i id="<?= $this->getId() ?>-state" type="state" class="state <?= $state->getAttribute('icon') ?>" title="<?= $state->getAttribute('title') ?>"
          style="<?= $state->buildPairs(';', ':', '', 'css') ?>"></i>
        <span id="<?= $this->getId() ?>-text" class="state-text"><?= $state->getAttribute('text') ?></span>
      <?php
      }
      ?>
    </td>
  <?php
  }

  /**
   * Action Bar, Actions
   */
  protected function Actions()
  {
    $owner = $this->parent->getOwner();
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="action" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <?php
      // Se puede definir si la "Action Bar" se mostrará o no a partir de un método en "Controller".
      if ($this->xml->hasAttribute('resolve')) {
        $resolve = $this->xml->getAttribute('resolve');
        if (!method_exists($owner, $resolve)) trigger_error("$resolve no existe en {$owner->name}", E_USER_ERROR);

        if (!$owner->$resolve($this->name, $this->getData())) {
          echo "</td>\n";
          return;
        }
      }
      foreach ($this->xml->children() as $action) {
        if ($action->hasAttribute('resolve')) {
          $resolve = $action->getAttribute('resolve');

          if (!method_exists($owner, $resolve)) trigger_error("$resolve no existe en {$owner->name}", E_USER_ERROR);

          if (!$owner->$resolve($action->getAttribute('name'), $this->getData())) continue;
        }
        // Se re-define el ID de las acciones: viewName-actionName-viewRow
      ?>
        <i id="<?= "{$this->parent->name}-{$action->getAttribute('name')}-{$this->row}" ?>" type="action" class="action <?= $action->getAttribute('icon') ?>" title="<?= $action->getAttribute('title') ?>"
          style="<?= $action->buildPairs(';', ':', '', 'css') ?>"></i>
      <?php
      }
      ?>
    </td>
  <?php
  }

  /**
   * Data
   */
  protected function Data()
  {
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="data"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>"><?= $this->getValue() ?></td>
  <?php
  }

  protected function MenuOptions()
  {
  ?>
    <td id="<?= $this->getId() ?>" type="menu" class="menu-options"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="text-align:center;cursor:pointer;<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <i id="<?= $this->getId() ?>-menu" type="menu" class="fa fa-ellipsis-v"></i>
      <div class="content menu-options-content">
        <?php
        foreach ($this->xml->children() as $opt) {
          $motype = new MenuOptionType($this->parent->name, $this->row, $opt);
        ?>
          <a class="option" style="display: block;" onclick="(event) => event.preventDefault()" <?= $motype->buildPairs() ?>>
            <i class="<?= $motype->icon ?>" style="margin-right: 3px"></i><?= $motype->text ?></a>
        <?php
        }
        ?>
      </div>
    </td>
  <?php
  }

  /**
   * Text, Date, Time
   */
  protected function InputBasic()
  {
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="basic"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <input type="<?= strtolower($this->type) ?>" class="inputEv" id="<?= $this->getId() ?>-input" name="<?= $this->name ?>" value="<?= $this->getValue() ?>" />
    </td>
  <?php
  }

  /**
   * Select
   */
  protected function inputSelect()
  {
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="select"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <select id="<?= $this->getId() ?>-input" type="select" name="<?= $this->name ?>" value="<?= $this->getValue() ?>"
        class="changeEv" <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
        style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
        <?php
        foreach ($this->xml->children() as $opt) {
          $selected = ($this->getValue() == $opt->getAttribute('value')) ? 'selected' : '';
        ?>
          <option value="<?= $opt->getAttribute('value') ?>" <?= $selected ?>><?= $opt->getAttribute('text') ?></option>
        <?php
        }
        ?>
      </select>
    </td>
  <?php
  }

  /**
   * Check
   */
  protected function InputCheck()
  {
    $selected = ($this->getValue() == 1) ? 'checked' : '';
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="checkbox"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <input type="checkbox" class="changeEv" id="<?= $this->getId() ?>-input" name="<?= $this->name ?>" value="<?= $this->getValue() ?>" <?= $selected ?> />
    </td>
  <?php
  }

  /**
   * Switch (checkbox)
   */
  protected function InputSwitch()
  {
    $selected = ($this->getValue() == 1) ? 'checked' : '';
  ?>
    <td class="data" id="<?= $this->getId() ?>" type="switch"
      <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?>
      style="<?= $this->xml->buildPairs(';', ':', '', 'css') ?>">
      <label class="switch" id="<?= $this->getId() ?>-switch">
        <input type="checkbox" class="changeEv" id="<?= $this->getId() ?>-input"
          name="<?= $this->name ?>" value="<?= $this->getValue() ?>" <?= $selected ?>
          <?= $this->xml->buildPairs(' ', '=', '"', 'html') ?> />
        <Span class="slider" id="<?= $this->getId() ?>-slider"></Span>
      </label>
    </td>
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

        $caseControl = static::fromXML($this->parent->getOwner(), $case);
        $caseControl->setParent($this->parent);
        $caseControl->setRow($this->row);
        $caseControl->setData($this->getData());
        
        return $caseControl->write();
      }
    }
  }
}
