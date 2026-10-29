<?php
/** @var Atusan\XML\XMLExtended $xml */
[
  'id' => $id,
  'text' => $text,
  'icon' => $icon
] = Atusan\Types\ButtonType::getNew($this->getOwner()->get('id'), $xml)->properties();
?>
<button id="<?= $id ?>" class="ats-btn" ats-owner="<?= $this->getOwner()->get('id') ?>" <?= $xml->buildAttributesPairs('html') ?>>
  <?= $icon, $text ?>
</button>