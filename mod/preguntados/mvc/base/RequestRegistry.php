<?php
/**
 * Archivo para crear la clase de registro de request para obtener y crear
 * objetos de tipo request y trabajar con sus valores.
 */


require_once('mvc/base/Registry.php');

/**
 * Clase de registro de request
 * 
 * Clase para registrar movimiento y cambios de los request por session.
 * 
 * @package Preguntados
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_base_RequestRegistry extends mvc_base_Registry {

    /**
     *
     * @var array 
     */
    private $value;

    /**
     *
     * @var object 
     */
    private static $instance;

    /**
     * Método para inicializar los valores de este objeto
     */
    public function __construct() {
        
    }

    /**
     * Método para obtener su propia instancia o crearla si es que no existe.
     * @return object
     */
    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance)
            self::$instance = new self;
        $returnValue = self::$instance;
        return $returnValue;
    }

    /**
     * Método para retornar el valor de un indice
     * @param string $key
     * @return object
     */
    protected function get($key) {
        return $this->value[$key];
    }

    /**
     * Método para setear el valor de un indice o crear
     * @param string $key
     * @param object $value
     */
    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    /**
     * Método para retornar el objeto request y sus valores cargados hasta el
     * momento.
     * @return object
     */
    public static function getRequest() {
        $returnValue = NULL;
        $returnValue = self::instance()->get('request');
        return $returnValue;
    }

    /**
     * Método para cargar el nuevo request con los valores que se desea
     * @param mvc_controller_Request $objRequest
     */
    public static function setRequest(mvc_controller_Request $objRequest) {
        self::instance()->set('request', $objRequest);
    }

}
