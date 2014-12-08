<?php

include_once 'colors.php';
include_once 'pde_local.php';
include_once 'pde_report.php';
include_once 'pde_module.php';

class generador {

    private $color;
    private $generate = 'generate';
    private $type = array('local');
    public $local;
    public $report;
    public $module;

    public function __construct($param) {
        $this->color = new Colors();
        $this->local = new pde_local();
        $this->report = new pde_report();
        $this->module = new pde_module();
        if (php_sapi_name() === 'cli') {
            $this->run($param);
        }
    }

    public function run($param) {
        //verificamos si tiene parametros
        if (is_array($param) && count($param) > 1) {
            //validamos que por lo menos aya 4 parametros
            // generate, type, name
            if (count($param) > 3) {
                //verificamos el primer comando
                if (isset($param[1]) && $param[1] == $this->generate) {
                    //validamos el segundo parametro, donde identificamos que queremos generar
                    if (isset($param[2]) && in_array($param[2], $this->type)) {
                        //verificamos si el nombre del paquete a generar es posible hacerlo
                        if(isset($param[3]) && $this->nameValid($param[3])){
                            //echo $this->color->getColoredString('paquete creado' . $param[3] . '', 'white', 'green');
                            switch ($param[2]){
                                case 'local':
                                    $this->local->run($param[3]);
                                    break;
                                default:
                                    break;
                            }
                        }else{
                            echo $this->color->getColoredString('El nombre del paquete a generar es ilegal' . $param[3] . '', 'white', 'red');
                        }
                    } else {
                        echo $this->color->getColoredString('No es posible generar paquete de tipo ' . $param[2] . '', 'white', 'red');
                    }
                } else {
                    echo $this->color->getColoredString('El primer comando es incorrecto', 'white', 'red');
                }
            } else {
                echo $this->color->getColoredString('Ingrese los parametros completos', 'white', 'red');
            }
        } else {
            echo $this->color->getColoredString($this->presentation(), 'blue');
        }
    }

    private function nameValid($filename) {
        $returnValue = FALSE;
        if (strpbrk($filename, "\\/?%*:|\"<>") === FALSE) {
            $returnValue = TRUE;
        }
        return $returnValue;
    }

    private function presentation() {
        $returnValue = '';
        $returnValue.='█▀▀█ █░░█ █▀▀ █▀▀█ ░░ █▀▀▀ █▀▀ █▀▀▄ █▀▀ █▀▀█ █▀▀█ ▀▀█▀▀ █▀▀█ █▀▀█ 
█░░█ █░░█ █░░ █░░█ ▀▀ █░▀█ █▀▀ █░░█ █▀▀ █▄▄▀ █▄▄█ ░░█░░ █░░█ █▄▄▀ 
█▀▀▀ ░▀▀▀ ▀▀▀ █▀▀▀ ░░ ▀▀▀▀ ▀▀▀ ▀░░▀ ▀▀▀ ▀░▀▀ ▀░░▀ ░░▀░░ ▀▀▀▀ ▀░▀▀ ';
        $returnValue.="\n";
        return $returnValue;
    }

}
