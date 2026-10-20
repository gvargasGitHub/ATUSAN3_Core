<?php

namespace App\Modules;

class module_name extends module_parent
{
  function index()
  {
    $this->initializeComponents();
    
    $this->response->view($this);
  }

  function initializeComponents()
  {

  }
}
