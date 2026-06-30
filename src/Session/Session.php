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
   * 
   */
  static public function start(): void
  {
    global $phpsessid, $sessfile;
    
    // Log::info('Session Path:' . ini_get('session.save_path'));
    // Log::info('Session Status:' . self::$status[session_status()]);

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    // Log::info('Session Status:' . self::$status[session_status()]);

    if (session_status() === PHP_SESSION_NONE) {
      if (!isset($_COOKIE['PHPSESSID']) || empty($_COOKIE['PHPSESSID'])) {
        $phpsessid = self::base32Encode(self::randomBytes(16));
        setcookie('PHPSESSID', $phpsessid, 
          ini_get('session.cookie_lifetime'), 
          ini_get('session.cookie_path'), 
          ini_get('session.cookie_domain'), 
          ini_get('session.cookie_secure'), 
          ini_get('session.cookie_httponly'));
      } else {
        $phpsessid = substr(preg_replace('/[^a-z0-9]/', '', $_COOKIE['PHPSESSID']), 0, 26);
      }

      $sessfile = ini_get('session.save_path') . '/sess_' . $phpsessid;

      if (file_exists($sessfile)) {
        $_SESSION = unserialize(file_get_contents($sessfile));
      } else {
        $_SESSION = [];
      }
      register_shutdown_function(['Atusan\\Session\\Session', 'save']);
    }
  }

  static public function keepAlive(): void
  {
    self::start();
  }

  static public function writeClose()
  {
    session_write_close();
  }

  /**
   * Destroy
   */
  static public function destroy()
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
  static public function close()
  {
    self::start();

    $_SESSION['auth'] = 0;
  }

  /**
   * Get Global
   */
  static public function get(string $key): mixed
  {
    return $_SESSION[$key] ?? null;
  }

  /**
   * Set Global
   */
  static public function set(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Set or Get Auth State
   */
  static public function auth(?bool $state): bool
  {
    if (!is_null($state)) $_SESSION['auth'] = $state;

    return $_SESSION['auth'];
  }

  // ----------------------------------
  // 
  // ----------------------------------
  static public function save(): void
  {
    global $sessfile;

    file_put_contents($sessfile, serialize($_SESSION));
  }

  static public function id(): string
  {
    global $phpsessid;
    return $phpsessid;
  }

  /**
   * Random Bytes
   */
  static public function randomBytes(int $length): string
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

  static public function base32Encode(string $input): string
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
