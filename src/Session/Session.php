<?php

namespace Atusan\Session;

use Atusan\Log\Log;

class Session
{
  static protected array $status = [
    PHP_SESSION_DISABLED => 'Session disabled',
    PHP_SESSION_NONE => 'Session enabled, but none exists',
    PHP_SESSION_ACTIVE => 'Session enabled and one exists'
  ];

  /**
   * Start
   */
  public static function start(): void
  {
    // Reporte
    $info = "Session Status:" . self::$status[session_status()] . "; Session ID:" . session_id() . PHP_EOL;
    
    $isStarted = session_start([
        'cookie_lifetime' => 86400 // Esto envía un cookie persistente que dura un día.
    ]);

    $info .= "Session started:";
    $info .= ($isStarted) ? 'Yes' : 'No';
    $info .= PHP_EOL . "Session Status:" . self::$status[session_status()] . "; Session ID:" . session_id() . PHP_EOL;

    if (session_status() === PHP_SESSION_NONE) {
      self::register();
    }

    if (APP_DEBUG) Log::info($info);
  }

  /**
   * 
   */
  public static function isActive() : bool
  {
    return (session_status() === PHP_SESSION_ACTIVE);
  }

  public static function keepAlive(): void
  {
    self::start();
  }

  public static function writeClose()
  {
    session_write_close();
  }

  /**
   * Destroy
   */
  public static function destroy()
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();

      # define la cookie para ser enviada junto con el resto de los
      # headers de HTTP. 
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    session_destroy();
  }

  /**
   * 
   */
  public static function close()
  {
    self::start();

    $_SESSION['auth'] = 0;
  }

  /**
   * Get Global
   */
  public static function get(string $key): mixed
  {
    return $_SESSION[$key] ?? null;
  }

  /**
   * Set Global
   */
  public static function set(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Set or Get Auth State
   */
  public static function auth(?bool $state): bool
  {
    if (!is_null($state)) $_SESSION['auth'] = $state;

    return $_SESSION['auth'];
  }

  // ----------------------------------
  // NGINX Solution
  // ----------------------------------
  public static function register(): void
  {
    global $phpsessid, $sessfile;

    if (!isset($_COOKIE['PHPSESSID']) || empty($_COOKIE['PHPSESSID'])) {
      $phpsessid = self::base32Encode(self::randomBytes(16));
      setcookie('PHPSESSID', $phpsessid, ini_get('session.cookie_lifetime'), ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
    } else {
      $phpsessid = substr(preg_replace('/[^a-z0-9]/', '', $_COOKIE['PHPSESSID']), 0, 26);
    }

    $sessfile = ini_get('session.save_path') . '/sess_' . $phpsessid;
    if (is_file($sessfile)) {
      $_SESSION = unserialize(file_get_contents($sessfile));
    } else {
      $_SESSION = [];
    }
    register_shutdown_function(['Atusan\\Session\\Session', 'save']);
  }

  public static function save(): void
  {
    global $sessfile;

    file_put_contents($sessfile, serialize($_SESSION));
  }

  public static function id(): string
  {
    global $phpsessid;
    return $phpsessid;
  }

  /**
   * Random Bytes
   */
  public static function randomBytes(int $length): string
  {
    if (function_exists('random_bytes')) {
      return random_bytes($length);
    }
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= chr(rand(0, 255));
    }
    return $randomString;
  }

  public static function base32Encode(string $input): string
  {
    $BASE32_ALPHABET = 'abcdefghijklmnopqrstuvwxyz234567';
    $output = '';
    $v = 0;
    $vbits = 0;
    for ($i = 0, $j = strlen($input); $i < $j; $i++) {
      $v <<= 8;
      $v += ord($input[$i]);
      $vbits += 8;
      while ($vbits >= 5) {
        $vbits -= 5;
        $output .= $BASE32_ALPHABET[$v >> $vbits];
        $v &= ((1 << $vbits) - 1);
      }
    }
    if ($vbits > 0) {
      $v <<= (5 - $vbits);
      $output .= $BASE32_ALPHABET[$v];
    }
    return $output;
  }
}
