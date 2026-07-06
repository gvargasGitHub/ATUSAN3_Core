<?php
if (empty($this->route)) $this->route = $_SERVER['REQUEST_URI']; ?>

<form id="<?= $this->name ?>-form" ats-route="<?= $this->route ?>">
  <?php
  foreach ($this->components as $component)
    if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase'))
      $component->setData($this->data[0]);
    
  // MultiForm no acepta el uso de "view" en el tag <DataMultiForm> y, por lo tanto,
  // no se puede personalizar la vista de los formularios. Por lo tanto, se debe usar 
  // la sección "TopForm", "StepForm" y "BottomForm" para personalizar la vista de los formularios.
  $this->writeSection('TopForm');
  $this->writeSection('StepForm');
  $this->writeSection('BottomForm');

  // Finalmente imprime grupo de botones
  echo $this->buttons->write();
  ?>
</form>