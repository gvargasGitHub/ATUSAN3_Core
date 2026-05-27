<?php

namespace Atusan\Controls;

use Atusan\Components\Component;
use Atusan\Controller\Module;
use Atusan\Types\MenuItemType;
use Atusan\XML\XMLExtended;

class SubnavbarControl extends Component
{
  protected string $type;

  function __construct(Module $owner, XMLExtended $xml)
  {
    parent::__construct($owner, $xml);

    $this->type = substr($this->xml->getName(), 4);
  }

  /**
   * 
   */
  public function write(): void
  {
    match (strtolower($this->type)) {
      'item' => $this->Item(),
      'content' => $this->Content(),
      'separator' => $this->Separator(),
      'view' => $this->View()
    };
  }

  function Item()
  {
    $miType = $this->itemType();
?>
    <a class="item itemClickEv" href="<?= $miType->route ?>" <?= $miType->pairs() ?>><i class="icon <?= $miType->icon ?>"></i><?= $miType->text ?></a>
  <?php
  }

  function Content()
  {
    $miType = $this->itemType();
  ?>
    <div class="subnav ddClickEv">
      <button class="subnavbtn"><i class="icon <?= $miType->icon ?>"></i><?= $miType->text ?> <i class="caret"></i></button>
      <div class="subnav-content">
        <?php
        foreach ($this->xml->children() as $xml) {
          $item = SubnavbarControl::fromXML($this->parent->owner, $xml);
          $item->setParent($this->parent);
          $item->write();
        } ?>
      </div>
    </div>
  <?php
  }

  function View()
  {
    $miType = $this->itemType();
  ?>
    <div class="subnav ddClickEv">
      <button class="subnavbtn"><i class="icon <?= $miType->icon ?>"></i><?= $miType->text ?> <i class="caret"></i></button>
      <div class="subnav-content">
        <?php include $this->parent->locateResource($miType->view) ?>
      </div>
    </div>
<?php
  }

  /**
   * 
   */
  protected function Separator(){}
  
  /**
   * 
   */
  protected function itemType(): MenuItemType
  {
    $itemNumber = $this->parent->getItemNoNameCounter();

    return new MenuItemType($this->parent->name, $itemNumber, $this->xml);
  }
}
