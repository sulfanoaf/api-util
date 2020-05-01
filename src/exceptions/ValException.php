<?php

namespace SAF\Helpers\Exceptions;

use Exception;

class ValException extends Exception
{
  protected $errorMessage;
  protected $errorKey;

  public function __construct($errorKey, ...$errorMessage)
  {
    $this->errorKey = $errorKey;
    $this->errorMessage = $errorMessage;
  }

  public function getMessages()
  {
    if ($this->errorKey == 'validator') {
      return $this->errorMessages;
    } else {
      $errorKey = $this->errorKey;
      $errorMessages = [];
      foreach ($this->errorMessage as $key => $value) {
        $errorMessages[$errorKey][] = $value;
      }
      return $errorMessages;
    }
  }
}
