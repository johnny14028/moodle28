<?php

/**
 * Archivo para definir una sola clase de tipo Model que es la que se creará
 * todos los metodos para interactuar con la Base de datos directamente
 * a traves del ORM de Moodle
 */


/**
 * Clase Model para definir los metodos que proveerá los datos de la BD
 * 
 * Esta clase se instanciará en todos los controladores para ser usados por estos
 * y de esta manera tener separados el modelo y el controlador.
 * 
 * @package Preguntados
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class Model {

    /**
     * Método para obtener el detalle del usuario que unició session
     * @global array $USER
     * @global array $DB
     * @return object
     */
    public function getUser() {
        global $USER, $DB;
        $returnValue = NULL;
        $user = $DB->get_record('user', array('id' => $USER->id));
        if (is_object($user)) {
            $returnValue = $user;
        }
        return $returnValue;
    }

}