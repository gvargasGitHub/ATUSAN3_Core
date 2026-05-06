<?php

namespace Atusan\Exceptions;

class NoticeException extends \Exception
{
  public function html()
  {
    return "<div class=\"alert notice\">
    <p><strong>Aviso!</strong> {$this->message}</p>
    </div>";
  }
}
