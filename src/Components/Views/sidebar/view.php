<script>
  var <?= $this->name ?> = new Sidebar("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>

<div id="<?= $this->name ?>" class="ats-sidebar ats-menubar">
  <div class="bars" ats-menuname="<?= $this->name ?>">
    <div class="bar bar1"></div>
    <div class="bar bar2"></div>
    <div class="bar bar3"></div>
  </div>
  <span class="title"><?= $this->title ?></span>
  <?php
  foreach ($this->sources as $source) {
  ?>
    <ul class="<?= strtolower($source->name) ?>">
      <?php
      foreach ($source->xml->children() as $child) $this->components->getByName($child->getAttribute('name'))->write();
      ?>
    </ul>
  <?php
  }
  ?>
</div>