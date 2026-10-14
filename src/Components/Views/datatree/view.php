<script>
  var <?= $this->get('id') ?> = new DataTree("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-dataview ats-datatree">
  <div class="header"><?= $this->get('title') ?></div>
  <div class="body">
    <ul>
      <?= $this->writeBodyRecursive($this->data, 0) ?>
    </ul>
  </div>
  <div class="footer"><?= $this->get('footer') ?></div>
</div>