<?php

include_once 'colors.php';
include_once 'pde_local.php';
include_once 'pde_report.php';
include_once 'pde_module.php';

class generador {

    private $color;
    private $generate = 'generate';
    private $type = array('local', 'controller','activity','report');
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
                        if (isset($param[3]) && $this->nameValid($param[3])) {
                            //echo $this->color->getColoredString('paquete creado' . $param[3] . '', 'white', 'green');
                            switch ($param[2]) {
                                case 'local':
                                    $this->local->run($param[3]);
                                    break;
                                case 'activity':
                                    $this->module->run($param[3]);
                                    break;
                                case 'report':
                                    $this->report->run($param[3]);
                                    break;
                                case 'controller':
                                    $this->generateController($param);
                                    break;
                                default:
                                    echo $this->color->getColoredString('El comando no puede generar el tipo ingresado', 'white', 'red');
                                    break;
                            }
                        } else {
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
            echo $this->presentation();
        }
    }

    private function generateController($param) {
        if (is_array($param) && count($param) > 0) {
            //verificamos que al menos tenga 5 parametros
            if (count($param) > 5) {
                //validamos que el plugin exista y que tenga la estructura mvc
                if ($this->existe($param)) {
                    //validamos si el plugin tiene el directorio MVC
                    if ($this->isMVC($param)) {
                        //echo $this->color->getColoredString('good!', 'white', 'green');
                        //generamos el controlador
                        switch ($param[3]) {
                            case 'local':
                                $value = $this->local->generateController($param);
                                break;
                            case 'activity':
                                $value = $this->module->generateController($param);
                                break;
                            default:
                                break;
                        }
                        if($value){
                            echo $this->color->getColoredString('El controlador se generó satisfatoriamente', 'white', 'green');
                        }else{
                            echo $this->color->getColoredString('Error al crear el controlador', 'white', 'red');
                        }
                    } else {
                        echo $this->color->getColoredString('El plugin ' . $param[4] . ' de tipo ' . $param[3] . ' no tiene la estructura MVC', 'white', 'red');
                    }
                } else {
                    echo $this->color->getColoredString('El plugin ' . $param[4] . ' de tipo ' . $param[3] . ' no existe', 'white', 'red');
                }
            } else {
                echo $this->color->getColoredString('Ingrese los parametros correctos para crear el controlador', 'white', 'red');
            }
        }
    }

    private function isMVC($param) {
        $returnValue = FALSE;
        $pathPlugin = '';
        switch ($param[3]) {
            case 'local':
                $pathPlugin = getcwd() . '/' . $param[3] . '/' . $param[4];
                break;
            case 'activity':
                $pathPlugin = getcwd() . '/mod/' . $param[4];
                break;
            default:
                break;
        }
        if (file_exists($pathPlugin)) {
            //verificamos si existe la carpeta mvc, controllers, views, model
            $folderMVC = $pathPlugin . '/mvc';
            $folderController = $pathPlugin . '/controllers';
            $folderViews = $pathPlugin . '/views';
            $folderModel = $pathPlugin . '/model';
            if (file_exists($folderController) && file_exists($folderViews) && file_exists($folderMVC) && file_exists($folderModel)) {
                $returnValue = TRUE;
            }
        }
        return $returnValue;
    }

    private function existe($param) {
        $returnValue = FALSE;
        $pathPlugin = '';
        switch ($param[3]) {
            case 'local':
                $pathPlugin = getcwd() . '/' . $param[3] . '/' . $param[4];
                break;
            case 'activity':
                $pathPlugin = getcwd() . '/mod/' . $param[4];
                break;
            default:
                break;
        }
        if (file_exists($pathPlugin)) {
            $returnValue = TRUE;
        }
        return $returnValue;
    }

    private function nameValid($filename) {
        $returnValue = FALSE;
        if (strpbrk($filename, "\\/?%*:|\"<>") === FALSE) {
            $returnValue = TRUE;
        }
        return $returnValue;
    }

    private function presentation() {
        $returnValue = "\n";
        $returnValue.= $this->color->getColoredString('-------------------------------------------------------------', 'blue');
        $returnValue.= $this->color->getColoredString("  __  __                       _   _                                               
 |  \/  |   ___     ___     __| | | |   ___               _ __ ___   __   __   ___ 
 | |\/| |  / _ \   / _ \   / _` | | |  / _ \    _____    | '_ ` _ \  \ \ / /  / __|
 | |  | | | (_) | | (_) | | (_| | | | |  __/   |_____|   | | | | | |  \ V /  | (__ 
 |_|  |_|  \___/   \___/   \__,_| |_|  \___|             |_| |_| |_|   \_/    \___|
", 'blue');
        $returnValue.="\n";
        $returnValue.=$this->color->getColoredString("                                                                  _  
      _   ._   _  o   _   ._      _.  |  ._   |_    _.    /|     / \ 
 \/  (/_  |   _>  |  (_)  | |    (_|  |  |_)  | |  (_|     |  o  \_/ 
", 'blue');
        $returnValue.="\n";
        $returnValue.= $this->color->getColoredString('-------------------------------------------------------------', 'blue');
        $returnValue.="\n";
        $returnValue.= $this->color->getColoredString("generate type nombreplugin \n", 'green');
        $returnValue.= $this->color->getColoredString("generate controller type nombreplugin nombrecontroller \n", 'green');
        $returnValue.= $this->color->getColoredString("generate: \t\t comando para indicar el inicio del evento", 'green');
        $returnValue.= $this->color->getColoredString("type: \t\t\t local, report, activity, format, repository, controller", 'green');
        $returnValue.= $this->color->getColoredString("nombreplugin: \t\t nombre del plugin a generar", 'green');
        $returnValue.= $this->color->getColoredString("nombrecontroller: \t nombre controlador del plugin generado", 'green');
        return $returnValue;
    }

}
