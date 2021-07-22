<?php

namespace DAI\Utils\Helpers;

use App;
use DAI\Utils\Interfaces\BLoCParamsInterface;
use Illuminate\Http\Request;

class BLoCParams implements BLoCParamsInterface
{
    protected $params = [];

    public function __construct($params)
    {
        if ($params instanceof Request) {
            $this->params = $params->all();
        } else {
            $this->params = $params;
        }
    }

    public function get($key, $fallback = null) {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return $fallback;
    }
}
