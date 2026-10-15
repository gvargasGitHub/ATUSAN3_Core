<?php
if (!$this->validateProperty('route')) $this->set('route', $_SERVER['REQUEST_URI']);

if (count($this->data) == 0) $this->data[0] = [];
?>
<form id="<?= $this->get('id') ?>-form" ats-route="<?= $this->get('route') ?>">
  <?php
  foreach ($this->components as $component)
    if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase'))
      $component->setData($this->data[0]);

  // Escribe la vista
  if (!empty($this->view)) {
    // escribe el control Csrf
    echo $this->writeControl('csrf_token');

    // integra la vista personalizada
    include $this->locateResource($this->get('view'));
  } else
    foreach ($this->components as $component) echo $component->make();
  ?>
</form>