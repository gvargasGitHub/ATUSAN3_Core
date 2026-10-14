<script>
  var <?= $this->get('id') ?> = new <?= basename(get_class($this)) ?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-panel">
  <div id="<?= $this->get('id') ?>-left" class="panel left">
    <?= $this->writePanel('Left') ?>
  </div>
  <div id="<?= $this->get('id') ?>-content" class="panel content">
    <?= $this->writePanel('Content') ?>
  </div>
  <div id="<?= $this->get('id') ?>-right" class="panel right">
    <?= $this->writePanel('Right') ?>
  </div>
</div>
<!-- End of <?= $this->get('id') ?> -->