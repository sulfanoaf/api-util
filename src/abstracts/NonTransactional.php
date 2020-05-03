<?php

namespace SAF\Helpers\Abstracts;

use SAF\Helpers\Exceptions\ValException;
use SAF\Helpers\Interfaces\NonTransactional as InterfacesNonTransactional;
use Validator;

abstract class NonTransactional implements InterfacesNonTransactional
{
  abstract protected function process($dto);

  public function execute($dto)
  {

    $validator = Validator::make($dto, $this->rules());

    if ($validator->fails()) {
      throw new ValException('validator', $validator->errors());
    }

    return $this->process($dto);
  }

  protected function rules()
  {
    return [];
  }

  protected function throwValidation($key, ...$msg)
  {
    throw new ValException($key, $msg);
  }
}
