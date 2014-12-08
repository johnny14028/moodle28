<?php

abstract class mvc_base_Registry {

    public function __construct() {
        
    }

    protected abstract function get($key);

    protected abstract function set($key, $value);
}
