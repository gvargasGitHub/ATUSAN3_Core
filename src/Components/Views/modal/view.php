<script>
  var <?= $this->name ?> = new Modal("<?= $this->name ?>", "<?= $this->owner->name ?>");
</script>
<Div id="<?= $this->name ?>" class="ats-modal">
  <Div class="content">
    <div class="header"><?= $this->title ?><i ats-owner="<?= $this->name ?>" class="close">&times;</i></div>
    <div class="body">
      <?php
      if (!empty($this->view))
        include $this->locateViewFile($this->view);
      else {
        foreach ($this->components as $component) $component->write();
      }
      ?>
    </div>
    <div class="footer"><?= $this->footer ?></div>
  </Div>
</Div>