<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Interfaces\BLoCInterface;
use DAI\Utils\Traits\FileHandler;
use Validator;

abstract class Retrieval implements BLoCInterface
{
  use FileHandler;

  abstract protected function process($parameters);

  public function execute($parameters)
  {

    Validator::make($parameters, $this->validation($parameters))->validate();

    return $this->process($parameters);
  }

  protected function validation($parameters = null)
  {
    return [];
  }
}
