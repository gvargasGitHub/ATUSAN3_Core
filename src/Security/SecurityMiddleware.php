<?php

namespace Atusan\Security;

use Atusan\Http\Request\Request;
use Atusan\Session\Session;
use Exception;

class SecurityMiddleware
{
  /**
   * Handle
   * Bootstrap -> Kernel::execute
   */
  public static function handle(Request $request): void
  {
    self::startSession();
    self::securityHeaders();
    self::checkHttpMethod($request);
  }

  private static function startSession(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      Session::start();
    }
  }

  private static function securityHeaders(): void
  {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin');
    // header("Content-Security-Policy: default-src 'self';");
  }

  private static function checkHttpMethod(Request $request): void
  {
    $allowed = ['GET', 'POST', 'PUT', 'DELETE'];

    if (!in_array($request->method(), $allowed, true)) {
      throw new Exception('Método ' . $request->method() . ' no permitido.');
    }
  }

  public static function generateCsrf(): string
  {
    return Csrf::generate();
  }

  public static function validateCsrf(Request $request): bool
  {
    $token =
      $request->get('csrf_token') ??
      $request->header('X-CSRF-TOKEN');

    return Csrf::validate($token);
  }

  public static function regenerateCsrf(): void
  {
    Csrf::regenerate();
  }
}
