<?php
$type = $this->itemType();
?>
<Button id="<?= "{$this->parent->name}-{$type->name}-button" ?>" <?= $type->buildPairs() ?>>
  <?= $type->text ?></Button>