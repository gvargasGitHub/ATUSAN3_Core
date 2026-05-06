<script>
  var <?= $this->name ?> = new Navbar("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<nav>
  <div id="<?= $this->name ?>" class="ats-navbar ats-menubar">
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
</nav>