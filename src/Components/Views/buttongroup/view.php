<script>
  var <?= $this->get('id') ?> = new ButtonGroup("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<Div id="<?= $this->get('id') ?>" class="<?= $this->printCssClass() ?>">
  <?php
  foreach ($this->xml->children() as $xml) include 'button.view.php';
  ?>
</Div>