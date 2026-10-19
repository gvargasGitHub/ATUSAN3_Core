<script>
  var <?= $this->get('id') ?> = new <?= basename(get_class($this)) ?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-panel">
  