<script>
  var <?= $this->name ?> = new Subnavbar("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<nav>
  <div id="<?= $this->name ?>" class="ats-subnavbar ats-menubar">
    <span class="title"><?= $this->title ?></span>

    <div class="bars" ats-menuname="<?= $this->name ?>">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
      <div class="bar bar3"></div>
    </div>
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
</nav>