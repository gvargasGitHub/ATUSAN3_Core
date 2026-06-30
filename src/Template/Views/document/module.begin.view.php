<?php
$className = (self::$module instanceof \Atusan\Controller\ModuleNested) ? 'ModuleNested' : 'Module';
?>
<script>
  var <?= self::$module->name ?> = new <?= $className ?>("<?= self::$module->name ?>");
</script>
