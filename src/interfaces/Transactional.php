<?php
namespace DAI\Utils\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */

interface Transactional
{

  public function getDescription();
  public function execute($dto);
}
