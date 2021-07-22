<?php
namespace DAI\Utils\Interfaces;

use DAI\Utils\Helpers\BLoCParams;

/**
 * @author Sulfano Agus Fikri
 */

interface BLoCInterface
{

  public function getDescription();
  public function execute(BLoCParams $parameters);
}
