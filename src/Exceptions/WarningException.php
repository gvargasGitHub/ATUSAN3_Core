<?php

namespace Atusan\Exceptions;

class WarningException extends \Exception
{
  public function html()
  {
    return "<div class=\"alert warning\">
    <p><strong>Advertencia!</strong> {$this->message}</p>
    </div>";
  }
}
