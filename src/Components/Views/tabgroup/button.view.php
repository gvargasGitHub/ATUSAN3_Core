<?php
$type = $this->itemType();
?>
<button id="<?= "{$this->parent->get('id')}-{$type->id}-button" ?>" <?= $type->buildPairs() ?>>
  <?= $type->text ?></button>