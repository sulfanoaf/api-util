<?php

namespace SAF\Helpers\Features;

class TailLog
{
  public static function error($class, $method, $message = [], $line = null)
  {
    if (is_object($class)) {
      $name = get_class($class);
    } else if (is_string($class)) {
      $name = $class;
    } else {
      $name = "PROVIDE A NAME PLEASE";
    }
    $msg = is_string($message) ? $message : json_encode($message);
    \Log::error("Namespace => [" . $name . "] \n\t Function => " . $method . " \n\t Title/Line => " . $line . "\n\t Message/DTO/Request => " . $msg);
  }

  public static function debug($class, $method, $message = [], $line = null)
  {
    if (is_object($class)) {
      $name = get_class($class);
    } else if (is_string($class)) {
      $name = $class;
    } else {
      $name = "PROVIDE A NAME PLEASE";
    }
    $msg = is_string($message) ? $message : json_encode($message);
    \Log::debug("Namespace => [" . $name . "] \n\t Function => " . $method . " \n\t Title/Line => " . $line . "\n\t Message/DTO/Request => " . $msg);
  }

  public static function info($class, $method, $message = [], $line = null)
  {
    if (is_object($class)) {
      $name = get_class($class);
    } else if (is_string($class)) {
      $name = $class;
    } else {
      $name = "PROVIDE A NAME PLEASE";
    }
    $msg = is_string($message) ? $message : json_encode($message);
    \Log::info("Namespace => [" . $name . "] \n\t Function => " . $method . " \n\t Title/Line => " . $line . "\n\t Message/DTO/Request => " . $msg);
  }
}
