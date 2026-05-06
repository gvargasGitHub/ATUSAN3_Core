<?php

namespace Atusan\Json;

use Exception;
use stdClass;

class JsonUtil
{
  const HTML_ENTITIES = 1;
  /**
   * 
   */
  public static function toArray($source): array
  {
    if (is_array($source)) return $source;

    # Decodifica la cadena como un arreglo asociativo
    $res = json_decode($source, true);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new Exception(json_last_error_msg());

    return $res;
  }

  /**
   * 
   */
  public static function toClass($source): stdClass
  {
    if (is_array($source)) return $source;

    # Decodifica la cadena como un arreglo asociativo
    $std = json_decode($source, false);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new Exception(json_last_error_msg());

    return $std;
  }

  /**
   * 
   */
  public static function toStringFormat($source, $option = 0): string
  {
    $res = json_encode($source, JSON_NUMERIC_CHECK);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new Exception(json_last_error_msg());

    return ($option == self::HTML_ENTITIES) ? htmlentities($res) : $res;
  }
}
