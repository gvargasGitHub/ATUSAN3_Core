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

  /**
   * checkHttpMethod
   * @param Request $request
   * @throws Exception
   */
  private static function checkHttpMethod(Request $request): void
  {
    $allowed = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

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

  // ------------------------------------------------
  // Authorization Block
  // ------------------------------------------------
  /**
   * Get Authorization Header
   * ChatGPT/ATUSAN 3/Authorization Header
   * @return string
   */
  public static function getAuthorizationHeader(): string
  {
      $authHeader =
          $_SERVER['HTTP_AUTHORIZATION'] ??
          $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ??
          '';

      if ($authHeader === '' && function_exists('getallheaders')) {
          $headers = getallheaders();
          $authHeader = $headers['Authorization'] ?? '';
      }

      return trim($authHeader);
  }

  /**
   * Get Bearer Token
   * ChatGPT/ATUSAN 3/Authorization Header
   * @return string|null
   */
  public static function getBearerToken(): ?string
  {
      $authHeader = self::getAuthorizationHeader();

      return stripos($authHeader, 'Bearer ') === 0
          ? trim(substr($authHeader, 7))
          : null;
  }

  /**
   * Get Basic Auth
   * ChatGPT/ATUSAN 3/Authorization Header
   * @return array|null
   */
  public static function getBasicAuth(): ?array
  {
      $authHeader = self::getAuthorizationHeader();

      if (stripos($authHeader, 'Basic ') !== 0) {
          return null;
      }

      $decoded = base64_decode(trim(substr($authHeader, 6)), true);

      if ($decoded === false || !str_contains($decoded, ':')) {
          return null;
      }

      [$username, $password] = explode(':', $decoded, 2);

      return [
          'username' => $username,
          'password' => $password
      ];
  }
}
