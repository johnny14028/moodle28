<?php

require_once('Request.php');
require_once(__DIR__.'/../command/CommandResolver.php');

class mvc_controller_Controller {

    public function __construct() {
        
    }
    /**
     * obtenemos la instancia del mismo objeto
     */
    public static function run() {
        $instance = new mvc_controller_Controller();
        $instance->handleRequest();
    }

    public function handleRequest() {
        $objRequest = new mvc_controller_Request();
        $cmd_r = new mvc_command_CommandResolver();        
        $cmd = $cmd_r->getCommand($objRequest);
        $cmd->execute($objRequest);
    }

}