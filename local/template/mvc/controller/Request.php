<?php
require_once(__DIR__.'/../base/RequestRegistry.php');
//require_once ('mvc/base/RequestRegistry.php');

class mvc_controller_Request {

    private $properties = array();
    private $feedback = array();

    public function __construct() {
        $this->init();
        mvc_base_RequestRegistry::setRequest($this);
    }

    public function init() {
        $this->properties = $_REQUEST;
        return;
    }

    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    public function setProperty($key, $value) {
        if (key_exists($key, $this->properties))
            $this->properties[$key] = $value;
        else
            return false;
    }

    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    public function getFeedbackString($separator = '\n') {
        $returnValue = (string) '';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}
