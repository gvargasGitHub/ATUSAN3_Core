<script>
  var <?= $this->get('id') ?> = new <?= $this->get('type') ?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>

<div id="<?= $this->get('id') ?>" class="ats-<?= strtolower($this->get('type')) ?> ats-menubar">
    <div class="bars" ats-menuname="<?= $this->get('id') ?>">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
      <div class="bar bar3"></div>
    </div>
    <span class="title"><?= $this->get('title') ?></span>
    <?php
    foreach ($this->sources as $source) {
    ?>
      <ul class="<?= strtolower($source->name) ?>">
        <?php
        foreach ($source->xml->children() as $child) echo $this->components->getById($child->getAttribute('id'))->make();
        ?>
      </ul>
    <?php
    }
    ?>
  </div>