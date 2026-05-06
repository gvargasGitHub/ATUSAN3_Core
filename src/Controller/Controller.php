<?php

namespace Atusan\Controller;

use Atusan\Http\Request\Request;
use Atusan\Http\Response\Response;

abstract class Controller implements ControllerInterface
{
  public string $name;
  protected Request $request;
  protected Response $response;

  function __construct()
  {
    // Obtiene el nombre base de la clase
    $this->name = basename(get_class($this));

    // Obtiene el objeto Request
    $this->request = Request::instance();

    // Crea objeto Response
    $this->response = Response::instance();
  }
}
