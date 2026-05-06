<?php

namespace Atusan\Types;

class RouteType
{
  function __construct(public $uri, public $controller, public $resolve, public $middlewareState, public $middlewareFilter, public $middlewareRedirectUri) {}
}
