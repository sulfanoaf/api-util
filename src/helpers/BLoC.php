<?php
namespace DAI\Utils\Helpers;

use App;

class BLoC
{
    public static function call($blocClass, $params) {
        $bloc = App::make($blocClass);

        return $bloc->execute($params);
    }
}
