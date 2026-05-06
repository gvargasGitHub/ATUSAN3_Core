<script>
  var <?= $this->name ?> = new Panel("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-panel">
  <div id="<?= $this->name ?>-left" class="panel left">
    <?= $this->writePanel('Left') ?>
  </div>
  <div id="<?= $this->name ?>-content" class="panel content">
    <?= $this->writePanel('Content') ?>
  </div>
  <div id="<?= $this->name ?>-right" class="panel right">
    <?= $this->writePanel('Right') ?>
  </div>
</div>
<!-- End of <?= $this->name ?> -->