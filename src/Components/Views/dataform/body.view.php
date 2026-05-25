<?php
if (empty($this->route)) $this->route = $_SERVER['REQUEST_URI'];

if (count($this->data) == 0) $this->data[0] = [];
?>
<form id="<?= $this->name ?>-form" ats-route="<?= $this->route ?>">
  <?php
  foreach ($this->components as $component)
    if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase'))
      $component->setData($this->data[0]);

  // Escribe la vista
  if (!empty($this->view))
    include $this->locateResource($this->view);
  else
    foreach ($this->components as $component) $component->write();
  ?>
</form>