<?php
/** @var Atusan\XML\XMLExtended $xml */
[
  'id' => $id,
  'text' => $text,
  'icon' => $icon
] = Atusan\Types\ButtonType::pairs($xml)->properties();
?>
<Button id="<?= $id ?>" class="ats-btn" <?= $xml->buildAttributesPairs('html') ?>>
  <?= $icon, $text ?>
</Button>