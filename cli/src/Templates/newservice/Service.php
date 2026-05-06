<?php

namespace App\Services;

use Atusan\Services\ServiceBase;
use Atusan\Session\AppSession;

class service_name extends ServiceBase
{
  function close()
  {
    AppSession::close();

    $this->response->json();
  }

  function keepAlive()
  {
    AppSession::keepAlive();

    $this->response->json();
  }
}
