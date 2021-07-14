<?php
namespace DAI\Utils\Helpers;

class TailLog
{
  public static function debug(...$args)
  {
    $log = "\n[*******************] local.DEBUG: START TAIL LOG =================================================>";
    foreach ($args as $key => $value) {
      if (is_object($value)) {
        $string = get_class($value);
      } else if (is_string($value)) {
        $string = $value;
      } else {
        $string = json_encode($value);
      }
      $log .= "\n\t $key => $string";
    }
    $log .= "\n[*******************] local.DEBUG: END TAIL LOG ===================================================>";
  }

  public static function info(...$args)
  {
    $log = "\n[*******************] local.INFO: START TAIL LOG =================================================>";
    foreach ($args as $key => $value) {
      if (is_object($value)) {
        $string = get_class($value);
      } else if (is_string($value)) {
        $string = $value;
      } else {
        $string = json_encode($value);
      }
      $log .= "\n\t $key => $string";
    }
    $log .= "\n[*******************] local.INFO: END TAIL LOG ===================================================>";
  }

  public static function error(...$args)
  {
    $log = "\n[*******************] local.ERROR: START TAIL LOG =================================================>";
    foreach ($args as $key => $value) {
      if (is_object($value)) {
        $string = get_class($value);
      } else if (is_string($value)) {
        $string = $value;
      } else {
        $string = json_encode($value);
      }
      $log .= "\n\t $key => $string";
    }
    $log .= "\n[*******************] local.ERROR: END TAIL LOG ===================================================> \n\n";
  }
}
