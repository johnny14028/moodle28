<?php

/**
 * Archivo para cargar los request con valores por defecto.
 */


require_once(__DIR__ . '/../command/Command.php');
require_once(__DIR__ . '/../controller/Request.php');

/**
 * 
 * 
 * Clase para cargar el primer request y tener los valoresde los objetos
 * request y session con valores iniciales para cargar las vistas por defecto.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_command_DefaultCommand extends mvc_command_Command {

    /**
     * Método para cargar el request y obtener las vistas.
     * @param mvc_controller_Request $objRequest
     */
    public function doExecute(mvc_controller_Request $objRequest) {
        $objRequest->addFeedback("Bienvenido: Moodle usando MVC");
    }

}