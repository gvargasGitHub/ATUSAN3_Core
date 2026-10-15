<?php
if (!$this->validateProperty('route')) $this->set('route', $_SERVER['REQUEST_URI']);

if (count($this->data) == 0) $this->data[0] = [];
?>
<form id="<?= $this->get('id') ?>-form" ats-route="<?= $this->get('route') ?>">
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
  echo $this->buttonGroup->make();
  ?>
</form>