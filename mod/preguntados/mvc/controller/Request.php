<?php

/**
 * Archivo del controlador de request;
 */


require_once(__DIR__ . '/../base/RequestRegistry.php');

/**
 * Clase controlador de request.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Request {

    /**
     *
     * @var array 
     */
    private $properties = array();

    /**
     *
     * @var array 
     */
    private $feedback = array();

    /**
     * Método para inciar el manejador de request de este controlador.
     */
    public function __construct() {
        $this->init();
        mvc_base_RequestRegistry::setRequest($this);
    }

    /**
     * Mátodo para inicializar capturando el request.
     * @return void
     */
    public function init() {
        $this->properties = $_REQUEST;
        return;
    }

    /**
     * Método para obtener el valor de un indice de este objeto.
     * @param string $key
     * @return boolean
     */
    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    /**
     * Método para setear el valor de un nuevo indice o crearla.
     * @param string $key
     * @param object $value
     * @return boolean
     */
    public function setProperty($key, $value) {
        if (key_exists($key, $this->properties))
            $this->properties[$key] = $value;
        else
            return false;
    }

    /**
     * Método para registrar los mensajes de tipo feedback
     * @param string $message
     */
    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    /**
     * Método para obtener los feedbacks
     * @param string $separator
     * @return string
     */
    public function getFeedbackString($separator = '\n') {
        $returnValue = (string) '';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}
