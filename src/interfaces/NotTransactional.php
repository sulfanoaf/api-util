<?php

namespace SAF\Helpers\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */

interface NotTransactional
{

  public function getDescription();
  public function execute($dto);
}
