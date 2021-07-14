<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Interfaces\Transactional as ITransactional;
use DAI\Utils\Traits\FileHandler;
use Exception;
use Validator;
use DB;
use Log;

abstract class Transactional implements ITransactional
{
  use FileHandler;

  abstract protected function prepare($parameters, $original_parameters);
  abstract protected function process($parameters, $original_parameters);

  public function execute($parameters)
  {
    $original_parameters = $parameters;
    $result = [];
    try {
      DB::beginTransaction();

      Validator::make($parameters, $this->rules())->validate();

      $motified_parameters = $this->prepare($parameters, $original_parameters);
      if ($motified_parameters != null) $parameters = $motified_parameters;
      $result =  $this->process($parameters, $original_parameters);

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
}
