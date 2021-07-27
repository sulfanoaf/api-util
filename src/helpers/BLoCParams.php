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
            foreach ($params->all() as $key => $value) {
                if ($params->hasFile($key)) {
                    $this->params[$key] = $params->file($key);
                } else {
                    $this->params[$key] = $params->get($key);
                }
            }
            $this->params = $params->all();
        } else {
            $this->params = $params;
        }
        return $this->params;
    }

    public function __get($key) {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    public function __set($key, $value) {
        return $this->params[$key] = $value;
    }

    public function all() {
        return $this->params;
    }

    public function get($key, $fallback = null) {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return $fallback;
    }
}
