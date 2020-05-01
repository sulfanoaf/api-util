<?php

namespace SAF\Helpers\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */

interface Transactional
{

  public function getDescription();
  public function execute($dto);
}
