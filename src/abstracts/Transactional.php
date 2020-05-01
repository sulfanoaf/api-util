<?php

namespace SAF\Helpers\Abstracts;

use SAF\Helpers\Interfaces\Transactional as InterfacesTransactional;
use SAF\Helpers\Exceptions\ValException;
use Exception;
use Validator;
use DB;
use Log;

abstract class Transactional implements InterfacesTransactional
{
  abstract protected function prepare($dto, $originalDto);
  abstract protected function process($dto, $originalDto);

  public function execute($dto)
  {
    $originalDto = $dto;
    $result = [];
    try {
      DB::beginTransaction();

      $validator = Validator::make($dto, $this->rules());

      if ($validator->fails()) {
        throw new ValException('validator', $validator->errors());
      }

      $modified_dto = $this->prepare($dto, $originalDto);
      if ($modified_dto != null) $dto = $modified_dto;
      $result =  $this->process($dto, $originalDto);

      DB::commit();
    } catch (Exception $ex) {
      Log::error($ex);
      DB::rollback();
      throw $ex;
    }
    return $result;
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
