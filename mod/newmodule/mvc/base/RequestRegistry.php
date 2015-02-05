<?php

require_once('mvc/base/Registry.php');

class mvc_base_RequestRegistry extends mvc_base_Registry {

    private $value;
    private static $instance;

    public function __construct() {
        
    }

    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance)
            self::$instance = new self;
        $returnValue = self::$instance;
        return $returnValue;
    }

    protected function get($key) {
        return $this->value[$key];
    }

    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    public static function getRequest() {
        $returnValue = NULL;
        $returnValue = self::instance()->get('request');
        return $returnValue;
    }

    public static function setRequest(mvc_controller_Request $objRequest) {
        self::instance()->set('request', $objRequest);
    }

}