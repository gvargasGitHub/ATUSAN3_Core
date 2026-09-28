<?php
namespace Atusan\Exceptions;

use Exception;

class AuthenticationException extends Exception 
{
  public function __construct(string $message)
  {
    $this->message = $message;
    $this->code = 401;
  }
}