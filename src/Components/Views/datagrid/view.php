<script>
  var <?= $this->get('id') ?> = new DataGrid("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-dataview ats-datagrid">
  <div class="header"><?= $this->get('title') ?></div>
  <div class="body">
    <?php include('body.view.php') ?>
  </div>
  <div class="footer"><?= $this->get('footer') ?></div>
</div>