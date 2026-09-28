<?php

namespace Atusan\Controller;

interface ControllerInterface
{
  // v3.0.20:
  // - Para las peticiones sin parámetros declarados en la ruta, index sin parámetros se ajusta
  // - Para las peticions con parámetros declarados, la ruta deberá declarar otro método de
  //    capture los parámetros
  public function index();
}
