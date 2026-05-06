<script>
  var <?= $this->name ?> = new DataTree("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-dataview ats-datatree">
  <div class="header"><?= $this->title ?></div>
  <div class="body">
    <ul>
      <?= $this->writeBodyRecursive($this->data, 0) ?>
    </ul>
  </div>
  <div class="footer"><?= $this->footer ?></div>
</div>