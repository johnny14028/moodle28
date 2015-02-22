<?php

/**
 * Archivo para crear la clase del controlador general.
 */


require_once('Request.php');
require_once(__DIR__ . '/../command/CommandResolver.php');

/**
 * Controlador general
 * 
 * Clase de tipo controlador que recibe los request y este ejecuta metodos para
 * resolver el requerimiento y cargar las vistas necesarias.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Controller {

    /**
     * iniciando los valores del objeto controlador.
     */
    public function __construct() {
        
    }

    /**
     * obtenemos la instancia del mismo objeto
     */
    public static function run() {
        $instance = new mvc_controller_Controller();
        $instance->handleRequest();
    }

    /**
     * Método para manejar los request y ejecutar los metodos que le permiten
     * cargar las vistas.
     */
    public function handleRequest() {
        $objRequest = new mvc_controller_Request();
        $cmd_r = new mvc_command_CommandResolver();
        $cmd = $cmd_r->getCommand($objRequest);
        $cmd->execute($objRequest);
    }

}