<?php
include 'content-begin.view.php';

if (!empty($this->view))
  include $this->locateResource($this->view);
else {
  foreach ($this->components as $component) {
    if (is_subclass_of($component, '\\Atusan\\Controller\\ModuleNested'))
      echo $component->nested();
    else
      echo $component->write();
  }
}

include 'content-end.view.php';
