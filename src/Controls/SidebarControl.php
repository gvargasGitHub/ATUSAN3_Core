<?php

namespace Atusan\Controls;

use Atusan\Components\Component;
use Atusan\Controller\Module;
use Atusan\Types\MenuItemType;
use Atusan\XML\XMLExtended;

class SidebarControl extends Component
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
    <li class="item itemClickEv" <?= $miType->pairs() ?>><i class="icon <?= $miType->icon ?>"></i><?= $miType->text ?>
      <?php if (!empty($miType->view)) include $this->parent->locateResource($miType->view) ?>
    </li>
  <?php
  }

  function Content()
  {
    $miType = $this->itemType();
  ?>
    <li class="item dropdown ddClickEv">
      <?= $miType->text ?><i class="caret"></i>
      <div class="content">
        <ul>
          <?php
          foreach ($this->xml->children() as $xml) {
            $item = SidebarControl::fromXML($this->parent->owner, $xml);
            $item->setParent($this->parent);
            $item->write();
          } ?>
        </ul>
      </div>
    </li>
<?php
  }

  /**
   * 
   */
  protected function Separator(){}

  /**
   * 
   */
  protected function View(){}
  /**
   * 
   */
  protected function itemType(): MenuItemType
  {
    $itemNumber = $this->parent->getItemNoNameCounter();

    return new MenuItemType($this->parent->name, $itemNumber, $this->xml);
  }
}
