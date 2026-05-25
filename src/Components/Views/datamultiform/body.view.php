<?php
if (empty($this->route)) $this->route = $_SERVER['REQUEST_URI']; ?>

<form id="<?= $this->name ?>-form" ats-route="<?= $this->route ?>">
  <?php
  foreach ($this->components as $component)
    if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase'))
      $component->setData($this->data[0]);

  if (!empty($this->view))
    include $this->locateResource($this->view);
  else {
    $this->writeSection('TopForm');
    $this->writeSection('StepForm');
    $this->writeSection('BottomForm');
  }
  // Imprime grupo de botones
  $this->buttons->write();
  ?>
</form>