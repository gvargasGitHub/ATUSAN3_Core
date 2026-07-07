<script>
  var <?= $this->name ?> = new TabGroup("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-tabgroup">
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
<!-- End of <?= $this->name ?> -->