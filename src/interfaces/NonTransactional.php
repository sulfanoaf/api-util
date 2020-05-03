<?php

namespace SAF\Helpers\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */

interface NonTransactional
{

  public function getDescription();
  public function execute($dto);
}
