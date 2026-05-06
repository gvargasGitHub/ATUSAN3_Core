<script>
  var <?= $this->name ?> = new ButtonGroup("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<Div id="<?= $this->name ?>" class="<?= $this->printCssClass() ?>">
  <?php
  foreach ($this->xml->children() as $xml) include 'button.view.php';
  ?>
</Div>