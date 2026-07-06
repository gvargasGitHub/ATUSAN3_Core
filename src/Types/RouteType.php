<?php

namespace Atusan\Types;

class RouteType
{
  function __construct(
    public string $uri, 
    public string $controller, 
    public string $resolve, 
    public int $middlewareState, 
    public string $middlewareFilter, 
    public string $middlewareRedirectUri) {}
}
