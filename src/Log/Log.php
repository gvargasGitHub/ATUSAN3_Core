<?php

namespace Atusan\Log;

use Throwable;

class Log
{
  static function info(string $text)
  {
    error_log(
      date('H:i:s') . ": {$text} \n",
      3,
      APP_ROOT . '/logs/Info-' . date('Ymd') . '.log'
    );
  }

  static function error(string $text)
  {
    error_log(
      date('H:i:s') . ": {$text} \n",
      3,
      APP_ROOT . '/logs/Err-' . date('Ymd') . '.log'
    );
  }

  /**
   * Incident
   */
  public static function incident(Throwable $exception): void
    {
        $record = [
            'date'      => date('c'),
            'level'     => 'ERROR',
            'exception' => $exception::class,
            'message'   => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'trace'     => $exception->getTraceAsString(),
        ];

        $line = json_encode(
            $record,
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );

        if ($line === false) {
            error_log('ATUSAN: no se pudo codificar una incidencia.');
            return;
        }

        $path = APP_ROOT . '/logs/Err-' . date('Ymd') . '.log';

        if (@file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            error_log('ATUSAN: no se pudo escribir el log. ' . $line);
        }
    }
}
