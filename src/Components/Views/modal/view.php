<script>
  var <?= $this->get('id') ?> = new Modal("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<Div id="<?= $this->get('id') ?>" class="ats-modal">
  <Div class="content">
    <div class="header"><?= $this->get('title') ?><i ats-owner="<?= $this->get('id') ?>" class="close">&times;</i></div>
    <div class="body">
      <?php
      if ($this->validateProperty('view'))
        include $this->locateResource($this->get('view'));
      else {
        foreach ($this->components as $component) echo $component->make();
      }
      ?>
    </div>
    <div class="footer"><?= $this->get('footer') ?></div>
  </Div>
</Div>