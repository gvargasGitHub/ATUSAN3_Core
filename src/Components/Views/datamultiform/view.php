<script>
  var <?= $this->get('id') ?> = new <?= basename(get_class($this))?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-dataview ats-dataform">
  <div class="header"><?= $this->get('title') ?></div>
  <div class="body">
    <?php include('body.view.php') ?>
  </div>
  <div class="stepmarks">
    <?php
    $numofSteps = $this->xml->StepForm->count();
    for ($s = 0; $s < $numofSteps; $s++):
    ?>
      <span class="stepmark"></span>
    <?php endfor ?>
  </div>
  <div class="footer"><?= $this->get('footer') ?></div>
</div>