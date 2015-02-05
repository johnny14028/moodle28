<?php
require_once(__DIR__.'/../mvc/command/Command.php');
require_once(__DIR__.'/../model/Model.php');
class CopiarController extends mvc_command_Command{
    private $model;
    
    public function __construct() {
        $this->model = new Model();
    }
    
    public function index(){
        global $USER;
        $objUsuario = $this->model->getUser();
        return array(
            'usuario'=>$USER->firstname,
            'controlador'=>'Copiar',
            'name_plugin'=>  get_string('pluginname', 'newmodule'),
            'objUsuario'=> $objUsuario
            );        
    }
}