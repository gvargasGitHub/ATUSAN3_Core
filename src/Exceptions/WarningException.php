<?php
declare(strict_types=1);

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
