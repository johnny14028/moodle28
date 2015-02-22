<?php

/**
 * Archivo para controlar las sessiones.
 */


require_once 'mvc/base/SessionRegistry.php';

/**
 * Clase controladora de sessiones.
 * 
 * Clase para manipular las sessiones como objeto.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Session {

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
     * Método para incializar los valores de la session en curso.
     */
    public function __construct() {
        $this->init();
        mvc_base_SessionRegistry::setSession($this);
    }

    /**
     * Método para iniciar session si es que no existieray regitrar en su propiedad 
     * el valor de la session.
     * @return void
     */
    public function init() {
        if (strlen(trim(session_id())) > 0)
            $this->properties = &$_SESSION;
        else
            session_start();
        return;
    }

    /**
     * Método para obtener el valor de una session.
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
     * Método para registrar un valor en una session de PHP.
     * @param string $key
     * @param object $value
     */
    public function setProperty($key, $value) {
        $this->properties[$key] = $value;
    }

    /**
     * Método para eliminar un valor de la sesión.
     * @param string $key
     */
    public function deleteProperty($key) {
        if (key_exists($key, $this->properties))
            unset($this->properties[$key]);
    }

    /**
     * Método para obtener los feedbacks.
     * @return array
     */
    public function getFeedback() {
        $returnValue = array();
        $returnValue = $this->feedback;
        return (array) $returnValue;
    }

    /**
     * Método para registrar los feedbacks
     * @param string $message
     */
    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    /**
     * Método para obtener los feedbacks separador por el caracter que desees.
     * @param string $separator
     * @return string
     */
    public function getFeedbackString($separator = '\n') {
        $returnValue = (string) '';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}