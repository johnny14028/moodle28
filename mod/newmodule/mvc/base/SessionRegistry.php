<?php

require_once 'mvc/base/Registry.php';

class mvc_base_SessionRegistry extends mvc_base_Registry {

    private $value = array();
    private static $instance = NULL;

    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance) {
            self::$instance = new self();
        }
        $returnValue = self::$instance;
        return $returnValue;
    }

    protected function get($key) {
        return $this->value[$key];
    }

    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    public static function getSession() {
        $returnValue = NULL;
        $returnValue = self::instance()->get('testing');
        return $returnValue;
    }

    public static function setSession(mvc_controller_Session $objSession) {
        self::instance()->set('testing', $objSession);
    }

}
