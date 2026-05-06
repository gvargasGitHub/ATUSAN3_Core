<script>
  var <?= $this->name ?> = new DataGrid("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<div id="<?= $this->name ?>" class="ats-dataview ats-datagrid">
  <div class="header"><?= $this->title ?></div>
  <div class="body">
    <?php include('body.view.php') ?>
  </div>
  <div class="footer"><?= $this->footer ?></div>
</div>