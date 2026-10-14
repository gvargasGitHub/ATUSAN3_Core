<?php
include 'content-begin.view.php';

if ($this->validateProperty('view')) 
  include $this->locateResource($this->get('view'));
else {
  foreach ($this->components as $component) {
    if (is_subclass_of($component, '\\Atusan\\Controller\\ModuleNested'))
      echo $component->nested();
    else
      echo $component->make();
  }
}

include 'content-end.view.php';
