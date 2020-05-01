<?php

namespace App\Exceptions;

use Exception;

class MultiLangException extends Exception
{
  protected $message = "";
  protected $errorKey;
  protected $errorDefined;
  protected $args;

  public function __construct($errorKey, ...$args)
  {
    $this->errorKey = $errorKey;
    $this->args = $args;
  }

  public function getArgs()
  {
    return $this->args;
  }

  public function getErrorDefined()
  {
    return config("multi_language_errors." . $this->errorKey);
  }

  public function getMessages()
  {
    $definedError = $this->getErrorDefined();
    if (!is_null($definedError)) {
      $errors = [];
      if (is_array($definedError)) {
        foreach ($definedError as $lang => $msg) {
          $array_args = [];

          foreach ($this->getArgs() as $key => $value) {
            $arr = [
              "{" . $key . "}" => $value
            ];
            $array_args = array_merge($array_args, $arr);
          }
          $errors[$lang] = str_replace(array_keys($array_args), array_values($array_args), $definedError[$lang]);
        }
      } else {
        return $this->message = json_encode($definedError);
      }
      return $this->message = $errors;
    } else {
      return $this->message = [
        "default" => "Undefined Exception"
      ];
    }
  }
}
