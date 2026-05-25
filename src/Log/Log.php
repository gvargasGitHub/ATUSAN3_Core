<?php

namespace Atusan\Log;

class Log
{
  static function info(string $text, array $trace = [])
  {
    error_log(
      date('H:i:s') . ": {$text} \n",
      3,
      APP_ROOT . '/logs/Log-' . date('Ymd') . '.log'
    );
  }

  static function error(string $text, array $trace = [])
  {
    error_log(
      date('H:i:s') . ": {$text} \n",
      3,
      APP_ROOT . '/logs/Log-' . date('Ymd') . '.log'
    );
  }
}
