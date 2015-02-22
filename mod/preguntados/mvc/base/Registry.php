<?php


/**
 * Clase para abstraer los metodos get y set
 */
abstract class mvc_base_Registry {

    /**
     * iniciamos los valores de entrada
     */
    public function __construct() {
        
    }

    /**
     * @param string $key nombre de la variable
     */
    protected abstract function get($key);

    /**
     * @param string $key indice del valor
     * @param string $value valor de la variable $key
     */
    protected abstract function set($key, $value);
}