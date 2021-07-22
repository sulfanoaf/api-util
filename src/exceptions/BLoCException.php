<?php

namespace DAI\Utils\Exceptions;

use Exception;

class BLoCException extends Exception
{
  protected $message;
  protected $errors;

  public function __construct($message, $errors = null)
  {
    $this->message = $message;
    $this->errors = $errors;
  }

  public function message()
  {
    return $this->message;
  }

  public function errors()
  {
    return $this->errors;
  }
}