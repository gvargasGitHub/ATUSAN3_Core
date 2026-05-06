<?php

namespace App\Modules;

use App\Classes\module_parent;

class module_name extends module_parent
{
  function index()
  {
    $this->response->view($this);
  }
}
