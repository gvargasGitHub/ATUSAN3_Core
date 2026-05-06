<script>
  var <?= $this->name ?> = new DataMultiForm("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-dataview ats-dataform">
  <div class="header"><?= $this->title ?></div>
  <div class="body">
    <?php include('body.view.php') ?>
  </div>
  <div class="stepmarks">
    <?php
    $numofSteps = $this->xml->StepForm->count();
    for ($s = 0; $s < $numofSteps; $s++) {
    ?>
      <span class="stepmark"></span>
    <?php
    } ?>
  </div>
  <div class="footer"><?= $this->footer ?></div>
</div>