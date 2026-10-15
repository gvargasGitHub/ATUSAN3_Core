<?php

namespace Atusan\Controls;

class NavbarControl extends NavControlBase
{
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
    match (strtolower($this->get('type'))) {
      'item' => $this->Item(),
      'content' => $this->Content(),
      'separator' => $this->Separator(),
      'view' => $this->View()
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

  protected function Item()
  {
    $miType = $this->itemType();

?>
    <li class="item itemClickEv" <?= $miType->pairs() ?>><i class="icon <?= $miType->icon ?>"></i><?= $miType->text ?>
      <?php if (!empty($miType->view)) include $this->parent->locateResource($miType->view) ?>
    </li>
  <?php
  }

  protected function Content()
  {
    $miType = $this->itemType();
  ?>
    <li class="item dropdown ddClickEv">
      <?= $miType->text ?><i class="caret"></i>
      <div class="content">
        <ul>
          <?php
          foreach ($this->xml->children() as $xml) {
            $item = NavbarControl::fromXML($this->parent->getOwner(), $xml);
            $item->setParent($this->parent);
            echo $item->make();
          } ?>
        </ul>
      </div>
    </li>
  <?php
  }

  protected function View()
  {
    $miType = $this->itemType();
  ?>
    <li class="item dropdown ddClickEv">
      <?= $miType->text ?><i class="caret"></i>
      <div class="content">
        <?php include $this->parent->locateResource($miType->view) ?>
      </div>
    </li>
<?php
  }

  protected function Separator(){}
  
}
