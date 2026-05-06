<?php
[
  'name' => $name,
  'text' => $text,
  'icon' => $icon
] = Atusan\Types\ButtonType::pairs($xml)->properties();
?>
<Button id="<?= $name ?>" class="ats-btn" <?= $xml->buildAttributesPairs('html') ?>>
  <?= $icon, $text ?>
</Button>