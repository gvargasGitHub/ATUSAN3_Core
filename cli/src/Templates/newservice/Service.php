<?php

namespace App\Services;

use Atusan\Controller\Service;

class service_name extends Service
{
  public function index()
  {
    return $this->response->json([
      'message' => 'Hello World'
    ]);
  }
}
