<?php

namespace Atusan\Controls;

use Atusan\Types\DataTreeDataType;

class DataTreeControl extends DataViewControlBase
{
  protected int $index = 0;
  protected int $level = 0;

  public function setDataType(DataTreeDataType $datatype)
  {
    $this->index = $datatype->index;
    $this->level = $datatype->level;
    $this->data  = $datatype->data;
  }

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
    $this->{$this->get('type')}();
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
    return "{$this->parent->get('id')}-{$this->index}-{$this->get('id')}";
  }

  /**
   * Caret
   */
  protected function Caret()
  {
?>
    <td id="<?= $this->getId() ?>" class="caret" type="<?= strtolower($this->get('type')) ?>"></td>
  <?php
  }
  /**
   * NoCaret
   */
  protected function NoCaret()
  {
  ?>
    <td id="<?= $this->getId() ?>" class="nocaret" type="<?= strtolower($this->get('type')) ?>"></td>
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
    <td id="<?= $this->getId() ?>" class="clickEv" type="<?= strtolower($this->get('type')) ?>" level="<?= $this->level ?>"><?= $this->getValue() ?></td>
  <?php
  }

  /**
   * Check
   */
  protected function Check()
  {
  ?>
    <td id="<?= $this->getId() ?>" type="<?= strtolower($this->get('type')) ?>">
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
    <td id="<?= $this->getId() ?>" class="clickEv" type="<?= strtolower($this->get('type')) ?>" style="width:<?= $w ?>px;">
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
