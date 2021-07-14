<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Interfaces\Retrieval as IRetrieval;
use DAI\Utils\Traits\FileHandler;
use Validator;

abstract class Retrieval implements IRetrieval
{
  use FileHandler;

  abstract protected function process($parameters);

  public function execute($parameters)
  {

    Validator::make($parameters, $this->rules())->validate();

    return $this->process($parameters);
  }

  protected function rules()
  {
    return [];
  }
}
