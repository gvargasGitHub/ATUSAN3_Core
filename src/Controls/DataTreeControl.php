<?php

namespace Atusan\Controls;

use Atusan\Types\DataTreeDataType;

class DataTreeControl extends DataViewControlBase
{
  protected int $index;
  protected int $level;

  public function setDataType(DataTreeDataType $datatype)
  {
    $this->index = $datatype->index;
    $this->level = $datatype->level;
    $this->data  = $datatype->data;
  }

  public function write(): void
  {
    $this->{$this->type}();
  }

  public function getId(): string
  {
    return "{$this->parent->name}-{$this->index}-{$this->name}";
  }

  /**
   * Caret
   */
  protected function Caret()
  {
?>
    <td id="<?= $this->getId() ?>" class="caret" type="<?= strtolower($this->type) ?>"></td>
  <?php
  }
  /**
   * NoCaret
   */
  protected function NoCaret()
  {
  ?>
    <td id="<?= $this->getId() ?>" class="nocaret" type="<?= strtolower($this->type) ?>"></td>
  <?php
  }
  /**
   * Colspan
   */
  protected function Colspan()
  {
  ?>
    <td id="<?= $this->getId() ?>" colspan="<?= $this->xml->getAttribute('cols') ?>"></td>
  <?php
  }
  /**
   * Data
   */
  protected function Data()
  {
  ?>
    <td id="<?= $this->getId() ?>" class="clickEv" type="<?= strtolower($this->type) ?>" level="<?= $this->level ?>"><?= $this->getValue() ?></td>
  <?php
  }

  /**
   * Check
   */
  protected function Check()
  {
  ?>
    <td id="<?= $this->getId() ?>" type="<?= strtolower($this->type) ?>">
      <input type="checkbox" class="changeEv" value="0" id="<?= $this->getId() ?>-input" name="<?= $this->getId() ?>-input" level="<?= $this->level ?>" />
    </td>
  <?php
  }

  /**
   * ActionBar
   */
  protected function Actions()
  {
    $w = $this->xml->count() * 20;
  ?>
    <td id="<?= $this->getId() ?>" class="clickEv" type="<?= strtolower($this->type) ?>" style="width:<?= $w ?>px;">
      <?php
      foreach ($this->xml->children() as $action) {
      ?>
        <i type="action" class="<?= $action->getAttribute("icon") ?> action" id="<?= $this->getId() ?>-<?= $action->getAttribute('name') ?>" level="<?= $this->level ?>"></i>
      <?php
      }
      ?>
    </td>
<?php
  }
}
