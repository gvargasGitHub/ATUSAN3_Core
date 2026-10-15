<script>
  var <?= $this->get('id') ?> = new <?= basename(get_class($this)) ?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-tabgroup">
  <div class="buttons">
    <?php
    foreach ($this->components as $content) echo $content->makeButton();
    ?>
  </div>
  <div class="contents">
    <?php
    foreach ($this->components as $content) echo $content->make();
    ?>
  </div>
</div>
<!-- End of <?= $this->get('id') ?> -->