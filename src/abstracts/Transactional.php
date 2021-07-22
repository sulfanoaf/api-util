<?php

namespace DAI\Utils\Abstracts;

use DAI\Utils\Interfaces\BLoCInterface;
use DAI\Utils\Traits\FileHandler;
use Exception;
use Validator;
use DB;
use Log;

abstract class Transactional implements BLoCInterface
{
  use FileHandler;

  abstract protected function process($parameters);

  public function execute($parameters)
  {
    DB::beginTransaction();
    try {
      Validator::make($parameters, $this->validation($parameters))->validate();

      $result = $this->process($parameters);
      DB::commit();

      return $result;
    } catch (Exception $ex) {
      Log::error($ex);
      DB::rollback();
      throw $ex;
    }
  }

  protected function validation($parameters)
  {
    return [];
  }
}
