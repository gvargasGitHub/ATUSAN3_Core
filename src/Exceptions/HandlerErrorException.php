<?php
namespace Atusan\Exceptions;

use Exception;

class HandlerErrorException extends Exception {
  public function __construct(string $message, int $code, string $file, int $line)
  {
    $this->message = $message;
    $this->code = $code;
    $this->file = $file;
    $this->line = $line;
  }
}