<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Helpers\BLoCParams;
use DAI\Utils\Interfaces\BLoCInterface;
use DAI\Utils\Traits\FileHandler;
use Validator;

abstract class Retrieval implements BLoCInterface
{
  use FileHandler;

  abstract protected function process(BLoCParams $parameters);

  public function execute(BLoCParams $parameters)
  {

    Validator::make($parameters, $this->validation($parameters))->validate();

    return $this->process($parameters);
  }

  protected function validation(BLoCParams $parameters = null)
  {
    return [];
  }
}
