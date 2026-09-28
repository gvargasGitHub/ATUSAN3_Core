<?php
namespace Atusan\Exceptions;

use Exception;

class InternalServerException extends Exception {
  public function __construct(string $message)
  {
    $this->message = $message;
    $this->code = 500;
  }
}