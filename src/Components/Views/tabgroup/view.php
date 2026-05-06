<script>
  var <?= $this->name ?> = new TabGroup("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-tabgroup">
  <div class="buttons">
    <?php
    foreach ($this->components as $content) $content->button();
    ?>
  </div>
  <div class="contents">
    <?php
    foreach ($this->components as $content) $content->write();
    ?>
  </div>
</div>
<!-- End of <?= $this->name ?> -->