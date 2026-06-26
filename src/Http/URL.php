<?php
namespace Atusan\Http;

class URL
{
  // Source - https://stackoverflow.com/a/8891890
  // Posted by Timo Huovinen, modified by community. See post 'Timeline' for change history
  // Retrieved 2026-06-22, License - CC BY-SA 4.0
  static public function origin(array $s, bool $use_forwarded_host = false ) : string
  {
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    
    return $protocol . '://' . $host;
  }

  /**
   * Full
   */
  static public function full( array $s, $use_forwarded_host = false ): string
  {
    return self::origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
  }

  /**
   * Base
   */
  static public function base(array $s, $use_forwarded_host=false)
  {
		return self::origin($s, $use_forwarded_host) . str_replace(self::getUri(), '', $s['REQUEST_URI']) ;
	}

  /**
   * Request URI
   */
  static public function requestUri(array $s): string
  {
    return $s['REQUEST_URI'];
  }

  /**
   * Script DirName
   */
  static public function scriptDirName(array $s): string
  {
    return pathinfo($s['SCRIPT_NAME'],PATHINFO_DIRNAME);
  }

  /**
   * Query String
   */
  static public function queryString(array $s): string
  {
    return $s['QUERY_STRING'];
  }

  /**
   * GET uri
   */
  static public function getUri(): string
  {
    return isset($_GET['uri']) ? $_GET['uri'] : '';
  }
}