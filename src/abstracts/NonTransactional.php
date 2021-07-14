<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Interfaces\NonTransactional as InterfacesNonTransactional;
use DAI\Utils\Traits\FileHandler;
use Validator;

abstract class NonTransactional implements InterfacesNonTransactional
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
