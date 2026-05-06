<?php

namespace Atusan\Security;

class Csrf
{
  private const SESSION_KEY = '_csrf_token';

  public static function generate(): string
  {
    if (empty($_SESSION[self::SESSION_KEY])) {
      $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }
    return $_SESSION[self::SESSION_KEY];
  }

  public static function token(): string
  {
    return $_SESSION[self::SESSION_KEY] ?? '';
  }

  public static function validate(?string $token): bool
  {
    if (!$token || empty($_SESSION[self::SESSION_KEY])) {
      return false;
    }

    return hash_equals($_SESSION[self::SESSION_KEY], $token);
  }

  public static function regenerate(): void
  {
    $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
  }
}
