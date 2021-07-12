<?php
namespace DAI\Utils\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */

interface NonTransactional
{

  public function getDescription();
  public function execute($dto);
}
