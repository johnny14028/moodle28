<?php

/**
 * Archivo para registrar las sessiones
 */


require_once 'mvc/base/Registry.php';

/**
 * Clase de registros para las sessiones
 * 
 * Clase que se registra, crea, edita los valores de la session de su propia
 * instancia.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_base_SessionRegistry extends mvc_base_Registry {

    /**
     *
     * @var array 
     */
    private $value = array();

    /**
     *  instancia propia de esta clase
     * @var object 
     */
    private static $instance = NULL;

    /**
     * Método para obtener su propia instancia o crearla si es que no existe.
     * @return object
     */
    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance) {
            self::$instance = new self();
        }
        $returnValue = self::$instance;
        return $returnValue;
    }

    /**
     * Método para obtener un valor de session por su indice
     * @param string $key
     * @return object
     */
    protected function get($key) {
        return $this->value[$key];
    }

    /**
     * Método para setear un valor a un indice de una session
     * @param string $key
     * @param object $value
     */
    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    /**
     * método para obtener el valor de una session
     * @return type
     */
    public static function getSession() {
        $returnValue = NULL;
        $returnValue = self::instance()->get('preguntados');
        return $returnValue;
    }

    /**
     * Metodo para crear una variable en la session
     * @param mvc_controller_Session $objSession
     */
    public static function setSession(mvc_controller_Session $objSession) {
        self::instance()->set('preguntados', $objSession);
    }

}