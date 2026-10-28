<script>
  var <?= $this->get('id') ?> = new <?= basename(get_class($this)) ?>("<?= $this->get('id') ?>", "<?= $this->getOwner()->get('id') ?>");
</script>
<div id="<?= $this->get('id') ?>" class="ats-panel">
  <?php foreach(['left', 'content', 'right'] as $panel): ?>
    <div id="<?= $this->get('id') ?>-<?= $panel ?>" class="panel <?= $panel ?>">
      <?php 
      foreach ($this->sources->getAllByName($panel) as $source) {
        if ($source->xml->hasAttribute('view')) {
          include $this->owner->getDirectory() . DS . "{$source->xml->getAttribute('view')}.php";
        } else {
          foreach($this->namespaces as $ns=>$url) {
            foreach ($source->xml->children($ns, true) as $child) {
              $component = $this->components->getById($child->getAttribute('id'));

              if ($component === null) {
                continue;
              }
              if (is_subclass_of($component, '\\Atusan\\Controller\\ModuleNested')) {
                echo $component->nested();
              } else {
                echo $component->make();
              }
            }
          }
        }
      }
      ?>
    </div>
  <?php endforeach; ?>
</div>
<!-- End of <?= $this->get('id') ?> -->